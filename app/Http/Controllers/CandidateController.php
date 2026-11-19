<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q', '');
        $status = $request->get('status', '');

        $candidates = Candidate::withCount('votes')
            ->when($search, function ($query) use ($search) {
                $query->where('ketua_nama', 'like', "%{$search}%")
                    ->orWhere('wakil_nama', 'like', "%{$search}%")
                    ->orWhere('kode_paslon', 'like', "%{$search}%")
                    ->orWhere('ketua_kelas', 'like', "%{$search}%");
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('nomor_urut')
            ->get();

        return view('candidates.index', compact('candidates', 'search', 'status'));
    }

    public function create()
    {
        $candidate = new Candidate(['status' => 'aktif']);
        return view('candidates.form', [
            'candidate' => $candidate,
            'isEdit' => false,
        ]);
    }

    public function edit(Candidate $candidate)
    {
        return view('candidates.form', [
            'candidate' => $candidate,
            'isEdit' => true,
        ]);
    }

    public function searchDpt(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $voters = Voter::query()
            ->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")
                    ->orWhere('kelas', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->limit(8)
            ->get(['id', 'nis', 'nama', 'kelas']);

        return response()->json($voters);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_urut' => 'required|integer|min:1|max:99|unique:candidates,nomor_urut',
            'kode_paslon' => 'required|string|max:20|unique:candidates,kode_paslon',
            'ketua_nama' => 'required|string|max:120',
            'ketua_kelas' => 'required|string|max:30',
            'wakil_nama' => 'required|string|max:120',
            'wakil_kelas' => 'required|string|max:30',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'status' => 'required|in:aktif,verifikasi,draft',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto_path'] = $request->file('foto')->store('paslon', 'public');
        }

        Candidate::create($validated);

        return redirect()->route('candidates.index')
            ->with('success', 'Pasangan calon berhasil ditambahkan ke daftar kandidat OSIS.');
    }

    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'nomor_urut' => 'required|integer|min:1|max:99|unique:candidates,nomor_urut,' . $candidate->id,
            'kode_paslon' => 'required|string|max:20|unique:candidates,kode_paslon,' . $candidate->id,
            'ketua_nama' => 'required|string|max:120',
            'ketua_kelas' => 'required|string|max:30',
            'wakil_nama' => 'required|string|max:120',
            'wakil_kelas' => 'required|string|max:30',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'status' => 'required|in:aktif,verifikasi,draft',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($candidate->foto_path) {
                Storage::disk('public')->delete($candidate->foto_path);
            }
            $validated['foto_path'] = $request->file('foto')->store('paslon', 'public');
        }

        $candidate->update($validated);

        return redirect()->route('candidates.index')
            ->with('success', "Data {$candidate->kode_paslon} berhasil diperbarui.");
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->foto_path) {
            Storage::disk('public')->delete($candidate->foto_path);
        }
        $kode = $candidate->kode_paslon;
        $candidate->delete();

        return redirect()->route('candidates.index')
            ->with('success', "Kandidat {$kode} telah dihapus dari sistem.");
    }
}
