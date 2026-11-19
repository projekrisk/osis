<?php

namespace App\Http\Controllers;

use App\Models\ElectionSetting;
use App\Models\Tps;
use App\Models\Voter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TpsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q', '');
        $status = $request->get('status', '');

        $tpsList = Tps::withCount([
            'voters',
            'voters as voted_voters_count' => fn ($q) => $q->where('has_voted', true),
        ])
            ->when($search, function ($query) use ($search) {
                $query->where('kode_tps', 'like', "%{$search}%")
                    ->orWhere('nama_tps', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('kode_tps')
            ->get();

        return view('tps.index', compact('tpsList', 'search', 'status'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_tps' => 'required|string|max:20|unique:tps,kode_tps',
            'nama_tps' => 'required|string|max:120',
            'lokasi' => 'nullable|string|max:150',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Tps::create($validated);

        return redirect()->route('tps.index')
            ->with('success', "TPS {$validated['kode_tps']} berhasil ditambahkan.");
    }

    public function update(Request $request, Tps $tps)
    {
        $validated = $request->validate([
            'kode_tps' => 'required|string|max:20|unique:tps,kode_tps,' . $tps->id,
            'nama_tps' => 'required|string|max:120',
            'lokasi' => 'nullable|string|max:150',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $tps->update($validated);

        return redirect()->route('tps.index')
            ->with('success', "Data {$tps->kode_tps} berhasil diperbarui.");
    }

    public function destroy(Tps $tps)
    {
        $kode = $tps->kode_tps;
        $tps->delete();

        return redirect()->route('tps.index')
            ->with('success', "TPS {$kode} telah dihapus.");
    }

    public function monitor(Request $request)
    {
        $tpsList = Tps::orderBy('kode_tps')->get();
        $selectedTpsId = $request->get('tps_id') ?: ($tpsList->first()?->id);
        $selectedTps = $tpsList->firstWhere('id', $selectedTpsId);

        $stats = $this->buildMonitorData($selectedTpsId);

        return view('tps.monitor', compact('tpsList', 'selectedTps', 'selectedTpsId', 'stats'));
    }

    public function monitorFeed(Request $request)
    {
        $tpsId = $request->get('tps_id');
        return response()->json($this->buildMonitorData($tpsId));
    }

    public function exportAttendancePdf(Request $request)
    {
        $setting = ElectionSetting::current();
        $tpsId = $request->get('tps_id');
        $tps = $tpsId ? Tps::find($tpsId) : Tps::orderBy('kode_tps')->first();

        $voters = Voter::with('tps')
            ->when($tps, fn ($q) => $q->where('tps_id', $tps->id))
            ->orderBy('kelas')
            ->orderBy('nama')
            ->get();

        $totalDpt = $voters->count();
        $hadirCount = $voters->where('has_voted', true)->count();
        $belumCount = max(0, $totalDpt - $hadirCount);
        $partisipasi = $totalDpt > 0 ? round(($hadirCount / $totalDpt) * 100, 1) : 0;

        $filename = $tps ? "daftar-hadir-{$tps->kode_tps}.pdf" : 'daftar-hadir-tps.pdf';

        $pdf = Pdf::loadView('tps.attendance-pdf', compact(
            'setting',
            'tps',
            'voters',
            'totalDpt',
            'hadirCount',
            'belumCount',
            'partisipasi'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    private function buildMonitorData(?int $tpsId): array
    {
        $isFinished = ElectionSetting::current()->votingState() === 'selesai';

        if (!$tpsId) {
            return [
                'total_dpt' => 0,
                'sudah_memilih' => 0,
                'belum_memilih' => 0,
                'partisipasi' => 0,
                'recent' => [],
                'is_finished' => $isFinished,
                'updated_at' => now()->format('H:i:s'),
            ];
        }

        $totalDpt = Voter::where('tps_id', $tpsId)->count();
        $sudah = Voter::where('tps_id', $tpsId)->where('has_voted', true)->count();
        $belum = max(0, $totalDpt - $sudah);
        $partisipasi = $totalDpt > 0 ? round(($sudah / $totalDpt) * 100, 1) : 0;

        $recent = Voter::where('tps_id', $tpsId)
            ->where('has_voted', true)
            ->orderByDesc('voted_at')
            ->take(10)
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'nis' => $v->nis,
                'nama' => $v->nama,
                'kelas' => $v->kelas,
                'receipt_code' => $v->receipt_code ?? '-',
                'voted_at' => $v->voted_at?->format('H:i:s') ?? '-',
                'voted_ago' => $v->voted_at?->diffForHumans() ?? 'Baru saja',
            ])
            ->values();

        return [
            'total_dpt' => $totalDpt,
            'sudah_memilih' => $sudah,
            'belum_memilih' => $belum,
            'partisipasi' => $partisipasi,
            'recent' => $recent,
            'is_finished' => $isFinished,
            'updated_at' => now()->format('H:i:s'),
        ];
    }
}
