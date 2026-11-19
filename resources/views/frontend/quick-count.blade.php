@extends('layouts.frontend')

@section('title', 'Quick Count')
@section('meta_description', 'Pantau hasil perolehan suara sementara dan tingkat partisipasi pemilih ' . $setting->nama_kegiatan . ' ' . $setting->nama_sekolah . ' secara realtime.')

@section('content')
  @if(($electionState ?? $setting->votingState()) === 'persiapan')
    <section class="w-full max-w-xl mx-auto border border-notion-border rounded-2xl bg-white p-6 sm:p-8 shadow-sm text-center space-y-5 my-6">
      <div class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center bg-amber-50 text-amber-700 border border-amber-200">
        <i data-lucide="clock" class="w-7 h-7"></i>
      </div>
      <div class="space-y-2">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
          Mode Persiapan
        </span>
        <h1 class="text-2xl font-bold tracking-tight text-notion-text">Quick Count Belum Dibuka</h1>
        <p class="text-sm text-notion-gray leading-relaxed">
          Pemilihan OSIS masih dalam tahap persiapan. Perolehan suara sementara baru dapat diakses setelah pemungutan suara dimulai pada
          <span class="font-mono text-notion-text">{{ $setting->jadwal_mulai }}</span> s/d <span class="font-mono text-notion-text">{{ $setting->jadwal_selesai }}</span>.
        </p>
      </div>
      <div class="pt-1">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-notion-text text-white text-sm font-medium rounded-lg hover:bg-black transition-colors">
          <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
        </a>
      </div>
    </section>
  @else
  <section class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-notion-border pb-6">
    <div class="space-y-2">
      <i data-lucide="bar-chart-2" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Quick Count</h1>
      <p class="text-sm text-notion-gray">
        Perolehan suara sementara dan tingkat partisipasi pemilih.
      </p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
      @if($data['is_finished'])
        <span id="qc-live-badge" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-notion-sidebar border border-notion-border text-notion-gray text-sm font-medium">
          <span class="w-2 h-2 rounded-full bg-notion-gray"></span>
          Selesai · Hasil Akhir
        </span>
      @else
        <span id="qc-live-badge" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
          <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
          Live · Update <span id="qc-updated-at" class="font-mono tnum">{{ $data['updated_at'] }}</span>
        </span>
      @endif
    </div>
  </section>

  <section class="grid grid-cols-2 sm:grid-cols-4 gap-5">
    <div class="border border-notion-border rounded-lg p-4 bg-white">
      <span class="text-sm font-medium text-notion-gray block mb-1">Total Pemilih (DPT)</span>
      <span id="qc-total-voters" class="text-2xl font-bold text-notion-text tnum">{{ $data['total_voters'] }}</span>
    </div>
    <div class="border border-notion-border rounded-lg p-4 bg-white">
      <span class="text-sm font-medium text-notion-gray block mb-1">Suara Masuk</span>
      <span id="qc-total-votes" class="text-2xl font-bold text-notion-blue tnum">{{ $data['total_votes'] }}</span>
    </div>
    <div class="border border-notion-border rounded-lg p-4 bg-white">
      <span class="text-sm font-medium text-notion-gray block mb-1">Partisipasi</span>
      <span id="qc-participation" class="text-2xl font-bold text-emerald-700 tnum">{{ $data['participation_rate'] }}%</span>
    </div>
    <div class="border border-notion-border rounded-lg p-4 bg-white">
      <span class="text-sm font-medium text-notion-gray block mb-1">Belum Memilih</span>
      <span id="qc-remaining" class="text-2xl font-bold text-amber-600 tnum">{{ max(0, $data['total_voters'] - $data['voted_count']) }}</span>
    </div>
  </section>

  <section class="space-y-4">
    <h2 class="text-lg font-bold border-b border-notion-border pb-2">Perolehan Suara</h2>
    @php
      $qcCandCount = count($data['candidates']);
      $qcCandGrid = $qcCandCount === 1 ? 'md:grid-cols-1' : ($qcCandCount === 2 ? 'md:grid-cols-2' : 'md:grid-cols-3');
    @endphp
    <div id="qc-candidates-grid" class="grid grid-cols-1 {{ $qcCandGrid }} gap-5">
      @foreach($data['candidates'] as $c)
        <div class="border border-notion-border rounded-xl p-5 bg-white shadow-sm space-y-4">
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-11 h-11 rounded-lg bg-notion-sidebar border border-notion-border flex items-center justify-center font-mono font-bold text-sm shrink-0 overflow-hidden">
                @if($c['foto_url'])
                  <img src="{{ $c['foto_url'] }}" alt="{{ $c['kode_paslon'] }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                @else
                  0{{ $c['nomor_urut'] }}
                @endif
              </div>
              <div class="min-w-0">
                <span class="text-sm font-mono text-notion-gray">{{ $c['kode_paslon'] }}</span>
                <p class="font-bold text-sm text-notion-text truncate">{{ $c['ketua_nama'] }}</p>
                <p class="text-sm text-notion-gray truncate">&amp; {{ $c['wakil_nama'] }}</p>
              </div>
            </div>
            <span class="px-2.5 py-0.5 rounded text-sm font-mono font-bold bg-blue-50 text-notion-blue border border-blue-200 tnum shrink-0">
              {{ $c['percentage'] }}%
            </span>
          </div>

          <div class="space-y-1.5">
            <div class="w-full h-2.5 rounded-full bg-notion-hover overflow-hidden">
              <div class="h-full bg-notion-blue rounded-full transition-all duration-500" style="width: {{ $c['percentage'] }}%"></div>
            </div>
            <div class="flex items-center justify-between text-sm text-notion-gray">
              <span>Perolehan sementara</span>
              <span class="font-mono font-semibold text-notion-text tnum">{{ $c['votes_count'] }} suara</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <section class="space-y-4">
    <h2 class="text-lg font-bold border-b border-notion-border pb-2">Partisipasi Pemilih</h2>
    @php
      $qcTpsCount = count($data['tps_stats']);
      $qcTpsGrid = $qcTpsCount === 1 ? 'sm:grid-cols-1' : ($qcTpsCount === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-2 lg:grid-cols-3');
    @endphp
    <div id="qc-tps-grid" class="grid grid-cols-1 {{ $qcTpsGrid }} gap-4">
      @foreach($data['tps_stats'] as $t)
        <div class="border border-notion-border rounded-lg p-4 bg-notion-sidebar/40 space-y-2">
          <div class="flex items-center justify-between gap-2">
            <span class="font-semibold text-sm text-notion-text truncate">{{ $t['nama_tps'] }}</span>
            <span class="font-mono text-sm font-semibold text-notion-blue tnum shrink-0">{{ $t['percentage'] }}%</span>
          </div>
          <div class="w-full h-1.5 rounded-full bg-notion-hover overflow-hidden">
            <div class="h-full bg-emerald-600 rounded-full transition-all duration-500" style="width: {{ $t['percentage'] }}%"></div>
          </div>
          <p class="text-sm font-mono text-notion-gray tnum">{{ $t['sudah_memilih'] }} / {{ $t['total_dpt'] }} pemilih hadir</p>
        </div>
      @endforeach
    </div>
  </section>
  @endif
@endsection

@push('scripts')
@if(($electionState ?? $setting->votingState()) !== 'persiapan')
<script>
  (function () {
    const isInitiallyFinished = @json($data['is_finished']);
    if (isInitiallyFinished) return;

    let pollTimer = null;

    function stopPolling() {
      if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
      }
      const badge = document.getElementById('qc-live-badge');
      if (badge) {
        badge.className = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-notion-sidebar border border-notion-border text-notion-gray text-sm font-medium';
        badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-notion-gray"></span> Selesai · Hasil Akhir';
      }
    }

    async function refreshQuickCount() {
      try {
        const res = await fetch(`{{ route('quick-count.feed') }}`, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const d = await res.json();

        const updatedEl = document.getElementById('qc-updated-at');
        if (updatedEl) updatedEl.textContent = d.updated_at;
        document.getElementById('qc-total-voters').textContent = d.total_voters;
        document.getElementById('qc-total-votes').textContent = d.total_votes;
        document.getElementById('qc-participation').textContent = `${d.participation_rate}%`;
        document.getElementById('qc-remaining').textContent = Math.max(0, d.total_voters - d.voted_count);

        const candGrid = document.getElementById('qc-candidates-grid');
        const candCols = d.candidates.length === 1 ? 'md:grid-cols-1' : (d.candidates.length === 2 ? 'md:grid-cols-2' : 'md:grid-cols-3');
        candGrid.className = `grid grid-cols-1 ${candCols} gap-5`;
        candGrid.innerHTML = d.candidates.map((c) => `
          <div class="border border-notion-border rounded-xl p-5 bg-white shadow-sm space-y-4">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-lg bg-notion-sidebar border border-notion-border flex items-center justify-center font-mono font-bold text-sm shrink-0 overflow-hidden">
                  ${c.foto_url ? `<img src="${c.foto_url}" alt="${c.kode_paslon}" class="w-full h-full object-cover">` : `0${c.nomor_urut}`}
                </div>
                <div class="min-w-0">
                  <span class="text-sm font-mono text-notion-gray">${c.kode_paslon}</span>
                  <p class="font-bold text-sm text-notion-text truncate">${c.ketua_nama}</p>
                  <p class="text-sm text-notion-gray truncate">&amp; ${c.wakil_nama}</p>
                </div>
              </div>
              <span class="px-2.5 py-0.5 rounded text-sm font-mono font-bold bg-blue-50 text-notion-blue border border-blue-200 tnum shrink-0">
                ${c.percentage}%
              </span>
            </div>
            <div class="space-y-1.5">
              <div class="w-full h-2.5 rounded-full bg-notion-hover overflow-hidden">
                <div class="h-full bg-notion-blue rounded-full transition-all duration-500" style="width: ${c.percentage}%"></div>
              </div>
              <div class="flex items-center justify-between text-sm text-notion-gray">
                <span>Perolehan sementara</span>
                <span class="font-mono font-semibold text-notion-text tnum">${c.votes_count} suara</span>
              </div>
            </div>
          </div>
        `).join('');

        const tpsGrid = document.getElementById('qc-tps-grid');
        const tpsCols = d.tps_stats.length === 1 ? 'sm:grid-cols-1' : (d.tps_stats.length === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-2 lg:grid-cols-3');
        tpsGrid.className = `grid grid-cols-1 ${tpsCols} gap-4`;
        tpsGrid.innerHTML = d.tps_stats.map((t) => `
          <div class="border border-notion-border rounded-lg p-4 bg-notion-sidebar/40 space-y-2">
            <div class="flex items-center justify-between gap-2">
              <span class="font-semibold text-sm text-notion-text truncate">${t.nama_tps}</span>
              <span class="font-mono text-sm font-semibold text-notion-blue tnum shrink-0">${t.percentage}%</span>
            </div>
            <div class="w-full h-1.5 rounded-full bg-notion-hover overflow-hidden">
              <div class="h-full bg-emerald-600 rounded-full transition-all duration-500" style="width: ${t.percentage}%"></div>
            </div>
            <p class="text-sm font-mono text-notion-gray tnum">${t.sudah_memilih} / ${t.total_dpt} pemilih hadir</p>
          </div>
        `).join('');

        if (d.is_finished) {
          stopPolling();
        }
      } catch (e) {}
    }

    pollTimer = setInterval(refreshQuickCount, 3000);
  })();
</script>
@endif
@endpush
