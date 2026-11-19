<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Tps;
use App\Models\Vote;
use App\Models\Voter;

class HomeController extends Controller
{
    public function index()
    {
        $setting = ElectionSetting::current();
        $candidates = Candidate::where('status', 'aktif')
            ->orderBy('nomor_urut')
            ->get();

        $tpsList = Tps::where('status', 'aktif')
            ->withCount('voters')
            ->orderBy('kode_tps')
            ->get();

        $voterAgg = Voter::selectRaw('COUNT(*) as total, SUM(CASE WHEN has_voted = 1 THEN 1 ELSE 0 END) as voted')->first();
        $totalVoters = (int) ($voterAgg->total ?? 0);
        $votedCount = (int) ($voterAgg->voted ?? 0);

        return view('frontend.home', compact('setting', 'candidates', 'tpsList', 'totalVoters', 'votedCount'));
    }

    public function privacy()
    {
        $setting = ElectionSetting::current();
        return view('frontend.privacy', compact('setting'));
    }

    public function terms()
    {
        $setting = ElectionSetting::current();
        return view('frontend.terms', compact('setting'));
    }

    public function help()
    {
        $setting = ElectionSetting::current();
        $tpsList = Tps::where('status', 'aktif')->orderBy('kode_tps')->get();
        return view('frontend.help', compact('setting', 'tpsList'));
    }

    public function quickCount()
    {
        $setting = ElectionSetting::current();
        $electionState = $setting->votingState();

        if ($electionState === 'persiapan') {
            return view('frontend.quick-count', [
                'setting' => $setting,
                'electionState' => $electionState,
                'data' => null,
            ]);
        }

        $data = $this->buildQuickCountData();

        return view('frontend.quick-count', compact('setting', 'electionState', 'data'));
    }

    public function quickCountFeed()
    {
        if (ElectionSetting::current()->votingState() === 'persiapan') {
            return response()->json([
                'message' => 'Quick Count belum tersedia selama mode persiapan.',
            ], 403);
        }

        return response()->json($this->buildQuickCountData());
    }

    private function buildQuickCountData(): array
    {
        $voterAgg = Voter::selectRaw('COUNT(*) as total, SUM(CASE WHEN has_voted = 1 THEN 1 ELSE 0 END) as voted')->first();
        $totalVoters = (int) ($voterAgg->total ?? 0);
        $votedCount = (int) ($voterAgg->voted ?? 0);
        $totalVotes = Vote::count();
        $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 1) : 0;

        $candidates = Candidate::where('status', 'aktif')
            ->withCount('votes')
            ->orderBy('nomor_urut')
            ->get()
            ->map(function ($c) use ($totalVotes) {
                $pct = $totalVotes > 0 ? round(($c->votes_count / $totalVotes) * 100, 1) : 0;
                return [
                    'id' => $c->id,
                    'nomor_urut' => $c->nomor_urut,
                    'kode_paslon' => $c->kode_paslon,
                    'ketua_nama' => $c->ketua_nama,
                    'ketua_kelas' => $c->ketua_kelas,
                    'wakil_nama' => $c->wakil_nama,
                    'wakil_kelas' => $c->wakil_kelas,
                    'foto_url' => $c->foto_path ? asset('uploads/' . $c->foto_path) : null,
                    'votes_count' => $c->votes_count,
                    'percentage' => $pct,
                ];
            })
            ->values();

        $tpsStats = Tps::where('status', 'aktif')
            ->withCount([
                'voters as total_dpt',
                'voters as sudah_memilih' => fn ($q) => $q->where('has_voted', true),
            ])
            ->orderBy('kode_tps')
            ->get()
            ->map(function ($t) {
                $pct = $t->total_dpt > 0 ? round(($t->sudah_memilih / $t->total_dpt) * 100, 1) : 0;
                return [
                    'kode_tps' => $t->kode_tps,
                    'nama_tps' => $t->nama_tps,
                    'total_dpt' => $t->total_dpt,
                    'sudah_memilih' => $t->sudah_memilih,
                    'percentage' => $pct,
                ];
            })
            ->values();

        return [
            'total_voters' => $totalVoters,
            'voted_count' => $votedCount,
            'total_votes' => $totalVotes,
            'participation_rate' => $participationRate,
            'candidates' => $candidates,
            'tps_stats' => $tpsStats,
            'is_finished' => ElectionSetting::current()->votingState() === 'selesai',
            'updated_at' => now()->format('H:i:s'),
        ];
    }
}
