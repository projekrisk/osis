<?php

namespace App\Http\Controllers;

use App\Models\ElectionSetting;
use App\Models\Tps;
use App\Models\Voter;
use App\Services\XlsxService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q', '');
        $kelas = $request->get('kelas', '');
        $tpsId = $request->get('tps_id', '');
        $statusPilih = $request->get('status_pilih', '');

        $kelasList = Voter::select('kelas')->distinct()->pluck('kelas')
            ->sort(fn ($a, $b) => strnatcasecmp($a, $b))
            ->values();
        if ($kelasList->isEmpty()) {
            $kelasList = collect(['X-E1', 'X-E2', 'XI-F1', 'XI-F2', 'XII-A1']);
        }

        $tpsList = Tps::orderBy('kode_tps')->get();

        $sortedVoters = Voter::with('tps')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                    if (Auth::user()?->isAdmin()) {
                        $sub->orWhere('token', 'like', "%{$search}%");
                    }
                });
            })
            ->when($kelas, fn ($q) => $q->where('kelas', $kelas))
            ->when($tpsId, fn ($q) => $q->where('tps_id', $tpsId))
            ->when($statusPilih !== '', function ($q) use ($statusPilih) {
                if ($statusPilih === 'sudah') {
                    $q->where('has_voted', true);
                } elseif ($statusPilih === 'belum') {
                    $q->where('has_voted', false);
                }
            })
            ->get()
            ->sort(function ($a, $b) {
                $cmp = strnatcasecmp($a->kelas, $b->kelas);
                return $cmp !== 0 ? $cmp : strnatcasecmp($a->nama, $b->nama);
            })
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $voters = new LengthAwarePaginator(
            $sortedVoters->forPage($currentPage, $perPage)->values(),
            $sortedVoters->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('voters.index', compact('voters', 'kelasList', 'tpsList', 'search', 'kelas', 'tpsId', 'statusPilih'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:voters,nis',
            'nama' => 'required|string|max:120',
            'kelas' => 'required|string|max:30',
            'tps_id' => 'required|exists:tps,id',
            'status' => 'required|in:terverifikasi,belum_aktivasi',
        ]);

        $validated['token'] = strtoupper(Str::random(6));
        Voter::create($validated);

        return redirect()->route('voters.index')
            ->with('success', "Pemilih {$validated['nama']} berhasil ditambahkan ke DPT.");
    }

    public function update(Request $request, Voter $voter)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:voters,nis,' . $voter->id,
            'nama' => 'required|string|max:120',
            'kelas' => 'required|string|max:30',
            'tps_id' => 'required|exists:tps,id',
            'status' => 'required|in:terverifikasi,belum_aktivasi',
        ]);

        $voter->update($validated);

        return redirect()->route('voters.index')
            ->with('success', "Data pemilih {$voter->nama} berhasil diperbarui.");
    }

    public function destroy(Voter $voter)
    {
        $nama = $voter->nama;
        $voter->delete();

        return redirect()->route('voters.index')
            ->with('success', "Pemilih {$nama} berhasil dihapus dari DPT.");
    }

    public function resetToken(Voter $voter)
    {
        if ($voter->has_voted) {
            return redirect()->route('voters.index')
                ->with('error', "Token {$voter->nama} sudah hangus karena pemilih telah menggunakan hak pilihnya.");
        }

        $newToken = strtoupper(Str::random(6));
        $voter->update(['token' => $newToken]);

        $msg = Auth::user()?->isAdmin()
            ? "Token baru untuk {$voter->nama}: {$newToken}"
            : "Token untuk {$voter->nama} berhasil diperbarui.";

        return redirect()->route('voters.index')->with('success', $msg);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_dpt' => 'required|file|mimes:xlsx|max:4096',
            'default_tps_id' => 'nullable|exists:tps,id',
        ]);

        $rows = XlsxService::readRows($request->file('file_dpt')->getRealPath());
        $imported = 0;
        $tpsByCode = Tps::pluck('id', 'kode_tps');
        $fallbackTpsId = $request->input('default_tps_id') ?: Tps::first()?->id;

        foreach ($rows as $index => $row) {
            if ($index === 0 && isset($row[0]) && str_contains(strtolower($row[0]), 'nis')) {
                continue;
            }
            if (count($row) < 3) {
                continue;
            }

            $nis = trim((string) $row[0]);
            $nama = trim((string) $row[1]);
            $kelas = trim((string) $row[2]);
            $kodeTps = isset($row[3]) ? strtoupper(trim((string) $row[3])) : '';
            $tpsId = $kodeTps && isset($tpsByCode[$kodeTps]) ? $tpsByCode[$kodeTps] : $fallbackTpsId;

            if ($nis && $nama && $kelas && !Voter::where('nis', $nis)->exists()) {
                Voter::create([
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'tps_id' => $tpsId,
                    'token' => strtoupper(Str::random(6)),
                    'status' => 'terverifikasi',
                ]);
                $imported++;
            }
        }

        return redirect()->route('voters.index')
            ->with('success', "{$imported} data pemilih dari file XLSX berhasil diimpor ke DPT.");
    }

    public function downloadTemplate()
    {
        $path = XlsxService::createWorkbook([
            ['nis', 'nama', 'kelas', 'kode_tps'],
            ['202604001', 'Contoh Siswa Satu', 'X-E1', 'TPS-01'],
            ['202604002', 'Contoh Siswa Dua', 'XI-F1', 'TPS-02'],
        ]);

        return response()->download($path, 'template-dpt-osis.xlsx')->deleteFileAfterSend(true);
    }

    public function printCards(Request $request)
    {
        $setting = ElectionSetting::current();
        $tpsId = $request->get('tps_id', '');
        $kelas = $request->get('kelas', '');

        $voters = Voter::with('tps')
            ->when($tpsId, fn ($q) => $q->where('tps_id', $tpsId))
            ->when($kelas, fn ($q) => $q->where('kelas', $kelas))
            ->orderBy('tps_id')
            ->orderBy('kelas')
            ->orderBy('nama')
            ->get();

        $tpsList = Tps::orderBy('kode_tps')->get();
        $kelasList = Voter::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('voters.print-cards', compact('setting', 'voters', 'tpsList', 'kelasList', 'tpsId', 'kelas'));
    }

    public function printDpt(Request $request)
    {
        $setting = ElectionSetting::current();
        $tpsList = Tps::orderBy('kode_tps')->get();
        $tpsId = $request->get('tps_id', $tpsList->first()?->id ?? '');

        $selectedTps = $tpsId ? $tpsList->firstWhere('id', (int) $tpsId) : null;

        $voters = Voter::with('tps')
            ->when($tpsId, fn ($q) => $q->where('tps_id', $tpsId))
            ->get()
            ->sort(function ($a, $b) {
                $cmp = strnatcasecmp($a->kelas, $b->kelas);
                return $cmp !== 0 ? $cmp : strnatcasecmp($a->nama, $b->nama);
            })
            ->values();

        return view('voters.print-dpt', compact('setting', 'tpsList', 'selectedTps', 'tpsId', 'voters'));
    }
}
