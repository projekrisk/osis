<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Tps;
use App\Models\Vote;
use App\Models\Voter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RecapController extends Controller
{
    public function index(Request $request)
    {
        $setting = ElectionSetting::current();
        $candidates = Candidate::withCount('votes')->orderByDesc('votes_count')->orderBy('nomor_urut')->get();
        $totalVotes = Vote::count();
        $totalVoters = Voter::count();

        $kelasStats = Voter::select('kelas')
            ->selectRaw('COUNT(*) as total_dpt')
            ->selectRaw('SUM(CASE WHEN has_voted = 1 THEN 1 ELSE 0 END) as sudah_memilih')
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        $tpsList = Tps::withCount([
            'voters as total_dpt',
            'voters as sudah_memilih' => fn ($q) => $q->where('has_voted', true),
        ])->orderBy('kode_tps')->get();

        $filterTpsId = $request->get('tps_id', '');
        $filterHadir = $request->get('hadir', '');
        $search = $request->get('q', '');

        $attendanceVoters = Voter::with('tps')
            ->when($filterTpsId, fn ($q) => $q->where('tps_id', $filterTpsId))
            ->when($filterHadir === 'sudah', fn ($q) => $q->where('has_voted', true))
            ->when($filterHadir === 'belum', fn ($q) => $q->where('has_voted', false))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('receipt_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('tps_id')
            ->orderBy('kelas')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString()
            ->appends(['tab' => 'daftar-hadir']);

        return view('recap.index', compact(
            'setting',
            'candidates',
            'totalVotes',
            'totalVoters',
            'kelasStats',
            'tpsList',
            'attendanceVoters',
            'filterTpsId',
            'filterHadir',
            'search'
        ));
    }

    public function exportCsv()
    {
        $candidates = Candidate::withCount('votes')->orderBy('nomor_urut')->get();
        $totalVotes = max(1, Vote::count());

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekapitulasi-pemilihan-osis.csv"',
        ];

        $callback = function () use ($candidates, $totalVotes) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nomor Urut', 'Kode Paslon', 'Ketua OSIS', 'Wakil Ketua OSIS', 'Jumlah Suara', 'Persentase (%)']);
            foreach ($candidates as $c) {
                $pct = round(($c->votes_count / $totalVotes) * 100, 2);
                fputcsv($handle, [
                    $c->nomor_urut,
                    $c->kode_paslon,
                    $c->ketua_nama,
                    $c->wakil_nama,
                    $c->votes_count,
                    $pct,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $setting = ElectionSetting::current();
        $candidates = Candidate::withCount('votes')
            ->orderByDesc('votes_count')
            ->orderBy('nomor_urut')
            ->get();

        $totalVotes = Vote::count();
        $totalVoters = Voter::count();
        $votedCount = Voter::where('has_voted', true)->count();
        $notVotedCount = max(0, $totalVoters - $votedCount);
        $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 1) : 0;

        $tpsStats = Tps::withCount([
            'voters as total_dpt',
            'voters as sudah_memilih' => fn ($q) => $q->where('has_voted', true),
        ])->orderBy('kode_tps')->get();

        $kelasStats = Voter::select('kelas')
            ->selectRaw('COUNT(*) as total_dpt')
            ->selectRaw('SUM(CASE WHEN has_voted = 1 THEN 1 ELSE 0 END) as sudah_memilih')
            ->groupBy('kelas')
            ->get()
            ->sort(fn ($a, $b) => strnatcasecmp($a->kelas, $b->kelas))
            ->values();

        $tpsWithVoters = Tps::with('voters')
            ->orderBy('kode_tps')
            ->get()
            ->map(function ($tps) {
                $sorted = $tps->voters
                    ->sort(function ($a, $b) {
                        $cmp = strnatcasecmp($a->kelas, $b->kelas);
                        return $cmp !== 0 ? $cmp : strnatcasecmp($a->nama, $b->nama);
                    })
                    ->values();
                $tps->setRelation('voters', $sorted);
                return $tps;
            });

        $tanggalIndo = now()->locale('id')->translatedFormat('d F Y');

        $pdf = Pdf::loadView('recap.recap-pdf', compact(
            'setting',
            'candidates',
            'totalVotes',
            'totalVoters',
            'votedCount',
            'notVotedCount',
            'participationRate',
            'tpsStats',
            'kelasStats',
            'tpsWithVoters',
            'tanggalIndo'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('rekapitulasi-pemilihan-osis.pdf');
    }

    public function exportAttendanceCsv(Request $request)
    {
        $tpsId = $request->get('tps_id');
        $tps = $tpsId ? Tps::find($tpsId) : null;
        $filename = $tps ? "daftar-hadir-{$tps->kode_tps}.csv" : 'daftar-hadir-semua-tps.csv';

        $voters = Voter::with('tps')
            ->when($tpsId, fn ($q) => $q->where('tps_id', $tpsId))
            ->orderBy('tps_id')
            ->orderBy('kelas')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString()
            ->appends(['tab' => 'daftar-hadir']);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($voters) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['NIS', 'Nama Pemilih', 'Kelas', 'TPS', 'Status Kehadiran', 'Waktu Memilih', 'Kode Bukti']);
            foreach ($voters as $v) {
                fputcsv($handle, [
                    $v->nis,
                    $v->nama,
                    $v->kelas,
                    $v->tps?->kode_tps ?? '-',
                    $v->has_voted ? 'Hadir / Sudah Memilih' : 'Belum Hadir',
                    $v->voted_at?->format('d/m/Y H:i:s') ?? '-',
                    $v->receipt_code ?? '-',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTabPdf(Request $request)
    {
        $setting = ElectionSetting::current();
        $tab = $request->get('tab', 'perolehan');

        $candidates = collect();
        $kelasStats = collect();
        $tpsList = collect();
        $attendanceVoters = collect();
        $selectedTps = null;

        $totalVotes = Vote::count();
        $totalVoters = Voter::count();

        if ($tab === 'kelas') {
            $title = 'Rekapitulasi Partisipasi per Kelas';
            $filename = 'rekap-partisipasi-per-kelas.pdf';
            $kelasStats = Voter::select('kelas')
                ->selectRaw('COUNT(*) as total_dpt')
                ->selectRaw('SUM(CASE WHEN has_voted = 1 THEN 1 ELSE 0 END) as sudah_memilih')
                ->groupBy('kelas')
                ->get()
                ->sort(fn ($a, $b) => strnatcasecmp($a->kelas, $b->kelas))
                ->values();
        } elseif ($tab === 'tps') {
            $title = 'Rekapitulasi Partisipasi per TPS';
            $filename = 'rekap-partisipasi-per-tps.pdf';
            $tpsList = Tps::withCount([
                'voters as total_dpt',
                'voters as sudah_memilih' => fn ($q) => $q->where('has_voted', true),
            ])->orderBy('kode_tps')->get();
        } elseif ($tab === 'daftar-hadir') {
            $filterTpsId = $request->get('tps_id', '');
            $filterHadir = $request->get('hadir', '');
            $search = $request->get('q', '');
            $selectedTps = $filterTpsId ? Tps::find($filterTpsId) : null;

            $title = $selectedTps
                ? "Daftar Hadir Pemilih — {$selectedTps->nama_tps}"
                : 'Daftar Hadir Pemilih (Semua TPS)';
            $filename = $selectedTps
                ? "daftar-hadir-{$selectedTps->kode_tps}.pdf"
                : 'daftar-hadir-semua-tps.pdf';

            $attendanceVoters = Voter::with('tps')
                ->when($filterTpsId, fn ($q) => $q->where('tps_id', $filterTpsId))
                ->when($filterHadir === 'sudah', fn ($q) => $q->where('has_voted', true))
                ->when($filterHadir === 'belum', fn ($q) => $q->where('has_voted', false))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('receipt_code', 'like', "%{$search}%");
                    });
                })
                ->get()
                ->sort(function ($a, $b) {
                    $cmp = strnatcasecmp($a->kelas, $b->kelas);
                    return $cmp !== 0 ? $cmp : strnatcasecmp($a->nama, $b->nama);
                })
                ->values();
        } else {
            $tab = 'perolehan';
            $title = 'Hasil Perolehan Suara Pasangan Calon';
            $filename = 'rekap-hasil-perolehan-suara.pdf';
            $candidates = Candidate::withCount('votes')
                ->orderByDesc('votes_count')
                ->orderBy('nomor_urut')
                ->get();
        }

        $pdf = Pdf::loadView('recap.tab-pdf', compact(
            'setting',
            'tab',
            'title',
            'candidates',
            'kelasStats',
            'tpsList',
            'attendanceVoters',
            'selectedTps',
            'totalVotes',
            'totalVoters'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }
}
