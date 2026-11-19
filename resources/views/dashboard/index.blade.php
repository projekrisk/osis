@extends('layouts.app')

@section('title', 'Dasbor')
@section('breadcrumb', 'Dasbor')

@section('content')

  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div class="space-y-2">
      <i data-lucide="bar-chart-2" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Dasbor Pemilihan OSIS</h1>
      <p class="text-sm text-notion-gray">
        Ringkasan pelaksanaan {{ $setting->nama_kegiatan }} Periode {{ $setting->tahun_ajaran }} di {{ $setting->nama_sekolah }}.
      </p>
    </div>
    <div class="flex flex-wrap items-center gap-2 shrink-0">
      <a href="{{ route('voting.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-notion-border text-notion-text text-sm font-medium rounded-md hover:bg-notion-hover transition-colors">
        <i data-lucide="vote" class="w-4 h-4 text-notion-gray"></i> Bilik Suara
      </a>
      <a href="{{ route('recap.pdf') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-notion-text text-white text-sm font-medium rounded-md hover:bg-black transition-colors">
        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Rekap
      </a>
    </div>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 pt-5 border-t border-notion-border">
    <div class="flex flex-col">
      <span class="text-xs font-medium text-notion-gray mb-1">Total Pemilih (DPT)</span>
      <span class="text-2xl font-semibold text-notion-text tnum">{{ number_format($totalVoters, 0, ',', '.') }}</span>
    </div>
    <div class="flex flex-col sm:border-l border-notion-border sm:pl-5">
      <span class="text-xs font-medium text-notion-gray mb-1">Paslon Aktif</span>
      <span class="text-2xl font-semibold text-notion-text tnum">{{ $activeCandidates }}</span>
    </div>
    <div class="flex flex-col border-t sm:border-t-0 sm:border-l border-notion-border pt-3 sm:pt-0 sm:pl-5">
      <span class="text-xs font-medium text-notion-gray mb-1">Suara Masuk ({{ $participationRate }}%)</span>
      <span class="text-2xl font-semibold text-notion-blue tnum">{{ number_format($votedCount, 0, ',', '.') }}</span>
    </div>
    <div class="flex flex-col border-t sm:border-t-0 sm:border-l border-notion-border pt-3 sm:pt-0 sm:pl-5">
      <span class="text-xs font-medium text-notion-gray mb-1">Belum Memilih</span>
      <span class="text-2xl font-semibold text-amber-600 tnum">{{ number_format($notVotedCount, 0, ',', '.') }}</span>
    </div>
  </div>

  <section class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-notion-border pb-2">
      <div class="flex items-center gap-2">
        <i data-lucide="award" class="w-4 h-4 text-notion-gray shrink-0"></i>
        <h2 class="text-lg font-semibold">Perolehan Suara Sementara</h2>
      </div>
      <span class="text-xs font-mono text-notion-gray tnum">Total: {{ $totalVotes }} suara</span>
    </div>

    @php
      $dashCandCount = $candidates->count();
      $dashCandGrid = $dashCandCount === 1 ? 'md:grid-cols-1' : ($dashCandCount === 2 ? 'md:grid-cols-2' : 'md:grid-cols-3');
    @endphp
    <div class="grid grid-cols-1 {{ $dashCandGrid }} gap-4">
      @forelse($candidates as $cand)
        @php
          $pct = $totalVotes > 0 ? round(($cand->votes_count / $totalVotes) * 100, 1) : 0;
        @endphp
        <div class="border border-notion-border rounded-lg p-4 bg-white space-y-3 hover:border-notion-gray/50 transition-colors">
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-lg bg-notion-sidebar border border-notion-border flex items-center justify-center font-mono font-bold text-sm shrink-0 overflow-hidden">
                @if($cand->foto_path)
                  <img src="{{ asset('uploads/' . $cand->foto_path) }}" alt="{{ $cand->kode_paslon }}" class="w-full h-full object-cover">
                @else
                  0{{ $cand->nomor_urut }}
                @endif
              </div>
              <div class="min-w-0">
                <span class="text-xs font-mono text-notion-gray">{{ $cand->kode_paslon }}</span>
                <p class="font-semibold text-sm text-notion-text truncate">{{ $cand->ketua_nama }}</p>
                <p class="text-xs text-notion-gray truncate">&amp; {{ $cand->wakil_nama }}</p>
              </div>
            </div>
            <span class="px-2 py-0.5 rounded text-xs font-mono font-semibold bg-blue-50 text-notion-blue border border-blue-200 tnum shrink-0">
              {{ $pct }}%
            </span>
          </div>

          <div class="space-y-1.5">
            <div class="w-full h-2 rounded-full bg-notion-hover overflow-hidden">
              <div class="h-full bg-notion-blue rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
            </div>
            <div class="flex items-center justify-between text-xs text-notion-gray">
              <span>Perolehan suara</span>
              <span class="font-mono font-medium text-notion-text tnum">{{ $cand->votes_count }} suara</span>
            </div>
          </div>
        </div>
      @empty
        <div class="col-span-full border border-notion-border rounded-lg p-6 text-center text-sm text-notion-gray">
          Belum ada kandidat paslon terdaftar.
        </div>
      @endforelse
    </div>
  </section>
@endsection
