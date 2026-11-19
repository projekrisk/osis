<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Vote;
use App\Models\Voter;

class DashboardController extends Controller
{
    public function index()
    {
        $setting = ElectionSetting::current();

        $totalVoters = Voter::count();
        $votedCount = Voter::where('has_voted', true)->count();
        $notVotedCount = max(0, $totalVoters - $votedCount);
        $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 1) : 0;

        $candidates = Candidate::withCount('votes')
            ->orderBy('nomor_urut')
            ->get();

        $totalVotes = Vote::count();
        $activeCandidates = $candidates->where('status', 'aktif')->count();

        return view('dashboard.index', compact(
            'setting',
            'totalVoters',
            'votedCount',
            'notVotedCount',
            'participationRate',
            'candidates',
            'totalVotes',
            'activeCandidates'
        ));
    }
}
