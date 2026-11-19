<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Tps;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VotingBoothController extends Controller
{
    public function index(Request $request)
    {
        $setting = ElectionSetting::current();
        $isElectionOpen = $setting->isVotingOpen();
        $electionState = $setting->votingState();
        $tpsList = Tps::where('status', 'aktif')->orderBy('kode_tps')->get();
        if ($request->filled('tps_id') && $tpsList->contains('id', (int) $request->get('tps_id'))) {
            session(['booth_tps_id' => (int) $request->get('tps_id')]);
        }
        $activeTpsId = session('booth_tps_id') ?: $tpsList->first()?->id;
        $activeTps = $tpsList->firstWhere('id', $activeTpsId);

        if ($activeTps && !session('booth_tps_id')) {
            session(['booth_tps_id' => $activeTps->id]);
        }

        $candidates = Candidate::where('status', 'aktif')->orderBy('nomor_urut')->get();

        $activeVoter = null;
        if (!$isElectionOpen) {
            session()->forget('verified_voter_id');
        } elseif (session('verified_voter_id')) {
            $activeVoter = Voter::with('tps')->find(session('verified_voter_id'));
            if (!$activeVoter || $activeVoter->has_voted || ($activeTps && $activeVoter->tps_id !== $activeTps->id)) {
                session()->forget('verified_voter_id');
                $activeVoter = null;
            }
        }

        $demoVoters = Voter::with('tps')
            ->where('has_voted', false)
            ->where('status', 'terverifikasi')
            ->when($activeTps, fn ($q) => $q->where('tps_id', $activeTps->id))
            ->orderBy('kelas')
            ->take(5)
            ->get();

        $receipt = session('last_vote_receipt');

        return view('voting.index', compact(
            'setting',
            'isElectionOpen',
            'electionState',
            'tpsList',
            'activeTps',
            'candidates',
            'activeVoter',
            'demoVoters',
            'receipt'
        ));
    }

    public function setTps(Request $request)
    {
        $request->validate([
            'tps_id' => 'required|exists:tps,id',
        ]);

        $tps = Tps::where('status', 'aktif')->findOrFail($request->tps_id);
        session([
            'booth_tps_id' => $tps->id,
        ]);
        session()->forget(['verified_voter_id', 'last_vote_receipt']);

        return redirect()->route('voting.index')
            ->with('info', "Bilik suara disetel ke {$tps->nama_tps}.");
    }

    public function verify(Request $request)
    {
        $setting = ElectionSetting::current();
        if (!$setting->isVotingOpen()) {
            $msg = $setting->votingState() === 'selesai'
                ? 'Pemungutan suara telah selesai dan ditutup.'
                : 'Pemungutan suara belum dimulai.';
            return redirect()->route('voting.index')->with('error', $msg);
        }

        $rules = ['nis' => 'required|string'];
        if ($setting->require_token) {
            $rules['token'] = 'required|string';
        }
        $request->validate($rules);

        $voter = Voter::with('tps')->where('nis', trim($request->nis))->first();
        if (!$voter) {
            return back()->with('error', 'NIS tidak ditemukan dalam Daftar Pemilih Tetap (DPT).')->withInput();
        }

        if ($voter->status !== 'terverifikasi') {
            return back()->with('error', 'Akun pemilih belum diaktivasi oleh panitia OSIS.')->withInput();
        }

        if (!$voter->tps || $voter->tps->status !== 'aktif') {
            return back()->with('error', 'TPS yang terdaftar pada akun Anda belum aktif atau tidak tersedia.')->withInput();
        }

        if ($voter->has_voted) {
            return back()->with('error', "Pemilih {$voter->nama} sudah menggunakan hak pilihnya.")->withInput();
        }

        if ($setting->require_token && strtoupper(trim($request->token)) !== strtoupper($voter->token)) {
            return back()->with('error', 'Token tidak sesuai. Periksa kembali kartu pemilih Anda.')->withInput();
        }

        session([
            'booth_tps_id' => $voter->tps->id,
            'verified_voter_id' => $voter->id,
        ]);
        session()->forget('last_vote_receipt');

        return redirect()->route('voting.index')
            ->with('success', "Verifikasi berhasil di {$voter->tps->kode_tps}! Silakan pilih pasangan calon, {$voter->nama}.");
    }

    public function cast(Request $request)
    {
        $setting = ElectionSetting::current();
        if (!$setting->isVotingOpen()) {
            session()->forget('verified_voter_id');
            return redirect()->route('voting.index')->with('error', 'Bilik suara sedang ditutup.');
        }

        $voterId = session('verified_voter_id');
        if (!$voterId) {
            return redirect()->route('voting.index')
                ->with('error', 'Sesi pemilih tidak valid atau telah berakhir.');
        }

        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $voter = Voter::with('tps')->findOrFail($voterId);
        if ($voter->has_voted) {
            session()->forget('verified_voter_id');
            return redirect()->route('voting.index')
                ->with('error', 'Hak pilih untuk akun ini sudah tercatat sebelumnya.');
        }

        $receiptCode = 'BKT-' . strtoupper(Str::random(6));
        $votedAt = now();
        $ua = $request->userAgent() ?? '';
        $parsed = $this->parseUserAgent($ua);
        $ipAddress = $request->ip();

        DB::transaction(function () use ($request, $voter, $receiptCode, $votedAt, $ua, $parsed, $ipAddress) {
            Vote::create([
                'candidate_id' => $request->candidate_id,
                'voter_id' => $voter->id,
                'tps_id' => $voter->tps_id,
            ]);

            $voter->update([
                'has_voted' => true,
                'voted_at' => $votedAt,
                'receipt_code' => $receiptCode,
                'ip_address' => $ipAddress,
                'browser' => $parsed['browser'],
                'os' => $parsed['os'],
                'device_type' => $parsed['device_type'],
                'user_agent' => $ua,
            ]);
        });

        session()->forget('verified_voter_id');
        session()->flash('last_vote_receipt', [
            'nama' => $voter->nama,
            'nis' => $voter->nis,
            'kelas' => $voter->kelas,
            'tps' => $voter->tps?->nama_tps ?? 'TPS Terdaftar',
            'kode_tps' => $voter->tps?->kode_tps ?? '-',
            'receipt_code' => $receiptCode,
            'voted_at' => $votedAt->format('d/m/Y H:i:s'),
        ]);

        return redirect()->route('voting.index')
            ->with('success', 'Suara berhasil dikirim! Harap perlihatkan bukti telah memilih ini kepada Panitia TPS.');
    }

    public function resetSession()
    {
        session()->forget(['verified_voter_id', 'last_vote_receipt']);
        return redirect()->route('voting.index')
            ->with('info', 'Sesi pemilih dikosongkan.');
    }

    private function parseUserAgent(string $ua): array
    {
        $deviceType = 'Desktop';
        if (preg_match('/ipad|tablet|playbook|silk/i', $ua)) {
            $deviceType = 'Tablet';
        } elseif (preg_match('/iphone/i', $ua)) {
            $deviceType = 'Mobile (iPhone)';
        } elseif (preg_match('/mobile|android|ipod|blackberry|iemobile|opera mini/i', $ua)) {
            $model = '';
            if (preg_match('/android[^;]*;\s*([^;\)]+?)(?:\s+Build|\)|;)/i', $ua, $mm)) {
                $candidateModel = trim($mm[1]);
                if ($candidateModel !== '' && !preg_match('/^(K|wv|Mobile|Linux|U)$/i', $candidateModel)) {
                    $model = ' (' . Str::limit($candidateModel, 24, '') . ')';
                }
            }
            $deviceType = 'Mobile' . $model;
        }

        $os = 'Tidak Diketahui';
        if (preg_match('/android\s*([0-9\.]+)?/i', $ua, $m)) {
            $os = 'Android' . (!empty($m[1]) ? ' ' . $m[1] : '');
        } elseif (preg_match('/iphone os\s*([0-9_]+)|cpu os\s*([0-9_]+)/i', $ua, $m)) {
            $ver = str_replace('_', '.', $m[1] ?: ($m[2] ?? ''));
            $os = 'iOS' . ($ver !== '' ? ' ' . $ver : '');
        } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
            $os = 'iOS';
        } elseif (preg_match('/windows nt 10\.0/i', $ua)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/windows nt 6\.3/i', $ua)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6\.1/i', $ua)) {
            $os = 'Windows 7';
        } elseif (preg_match('/windows/i', $ua)) {
            $os = 'Windows';
        } elseif (preg_match('/mac os x\s*([0-9_\.]+)?/i', $ua, $m)) {
            $ver = !empty($m[1]) ? ' ' . str_replace('_', '.', $m[1]) : '';
            $os = 'macOS' . $ver;
        } elseif (preg_match('/cros/i', $ua)) {
            $os = 'ChromeOS';
        } elseif (preg_match('/linux/i', $ua)) {
            $os = 'Linux';
        }

        $browser = 'Tidak Diketahui';
        $patterns = [
            '/Instagram\s*([0-9\.]+)?/i' => 'Instagram In-App',
            '/(?:FBAN|FBAV)\/([0-9\.]+)?/i' => 'Facebook In-App',
            '/WhatsApp\/([0-9\.]+)?/i' => 'WhatsApp In-App',
            '/Line\/([0-9\.]+)?/i' => 'LINE In-App',
            '/BytedanceWebview|TikTok/i' => 'TikTok In-App',
            '/SamsungBrowser\/([0-9\.]+)/i' => 'Samsung Internet',
            '/MiuiBrowser\/([0-9\.]+)/i' => 'Xiaomi / MIUI Browser',
            '/VivoBrowser\/([0-9\.]+)/i' => 'Vivo Browser',
            '/HeyTapBrowser\/([0-9\.]+)/i' => 'OPPO / HeyTap Browser',
            '/HuaweiBrowser\/([0-9\.]+)/i' => 'Huawei Browser',
            '/UCBrowser\/([0-9\.]+)/i' => 'UC Browser',
            '/Vivaldi\/([0-9\.]+)/i' => 'Vivaldi',
            '/DuckDuckGo\/([0-9\.]+)|Ddg\/([0-9\.]+)/i' => 'DuckDuckGo Browser',
            '/Brave\/([0-9\.]+)/i' => 'Brave',
            '/(?:Edg|EdgA|EdgiOS)\/([0-9\.]+)/i' => 'Microsoft Edge',
            '/(?:OPR|OPT|Opera)\/([0-9\.]+)/i' => 'Opera',
            '/Opera Mini\/([0-9\.]+)/i' => 'Opera Mini',
            '/(?:Firefox|FxiOS)\/([0-9\.]+)/i' => 'Mozilla Firefox',
            '/CriOS\/([0-9\.]+)/i' => 'Google Chrome (iOS)',
            '/Chrome\/([0-9\.]+)/i' => 'Google Chrome',
            '/Version\/([0-9\.]+).*Safari\//i' => 'Safari',
        ];

        foreach ($patterns as $regex => $label) {
            if (preg_match($regex, $ua, $m)) {
                $ver = $m[1] ?? ($m[2] ?? '');
                $browser = $label . ($ver !== '' ? ' ' . $ver : '');
                break;
            }
        }

        if ($browser === 'Tidak Diketahui' && $ua !== '') {
            $browser = Str::limit($ua, 40);
        }

        return [
            'browser' => $browser,
            'os' => $os,
            'device_type' => $deviceType,
        ];
    }
}
