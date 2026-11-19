<?php

namespace App\Http\Controllers;

use App\Models\ElectionSetting;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = ElectionSetting::current();
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = ElectionSetting::current();

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:150',
            'nama_kegiatan' => 'required|string|max:180',
            'tahun_ajaran' => 'required|string|max:20',
            'status_pemilihan' => 'required|in:persiapan,berlangsung,selesai',
            'jadwal_mulai' => 'required|string|max:50',
            'jadwal_selesai' => 'required|string|max:50',
            'require_token' => 'required|boolean',
            'allow_abstain' => 'required|boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logo', 'public');
        }

        $setting->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function removeLogo()
    {
        $setting = ElectionSetting::current();
        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
            $setting->update(['logo_path' => null]);
        }

        return redirect()->route('settings.index')
            ->with('info', 'Logo sekolah/OSIS telah dihapus.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:4',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();
        if ($user && !\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai.');
        }

        if ($user) {
            $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->new_password)]);
        }

        return back()->with('success', 'Password akun berhasil diperbarui.');
    }

    public function resetVoterStatus()
    {
        DB::transaction(function () {
            Vote::query()->delete();
            Voter::query()->update([
                'has_voted' => false,
                'voted_at' => null,
                'receipt_code' => null,
            ]);
        });

        return redirect()->route('settings.index')
            ->with('success', 'Status memilih seluruh pemilih berhasil direset dan perolehan suara dikosongkan.');
    }

    public function clearVoters()
    {
        DB::transaction(function () {
            Vote::query()->delete();
            Voter::query()->delete();
        });

        return redirect()->route('settings.index')
            ->with('success', 'Seluruh data pemilih (DPT) beserta suara terkait telah dihapus.');
    }
}
