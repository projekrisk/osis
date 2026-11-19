@extends('layouts.app')

@section('title', 'Pantau TPS Realtime')
@section('breadcrumb', 'Pantau TPS (Realtime)')

@section('content')

  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div class="space-y-2">
      <i data-lucide="radio" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Pantau Aktivitas TPS</h1>
      <p class="text-sm text-notion-gray">
        Pemantauan langsung pemilih yang baru selesai memberikan suara.
      </p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
      @if($stats['is_finished'])
        <span id="monitor-live-badge" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-notion-sidebar border border-notion-border text-notion-gray text-xs font-medium">
          <span class="w-2 h-2 rounded-full bg-notion-gray"></span>
          Selesai · Pemantauan Ditutup
        </span>
      @else
        <span id="monitor-live-badge" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
          <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
          Live · Update <span id="monitor-updated-at" class="font-mono tnum">{{ $stats['updated_at'] }}</span>
        </span>
      @endif
    </div>
  </div>

  <form method="GET" action="{{ route('tps.monitor') }}" class="p-4 border border-notion-border rounded-lg bg-notion-sidebar/40 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
      <div class="sm:w-72 shrink-0">
        <select name="tps_id" id="monitor-tps-select" class="modern-select w-full">
          @foreach($tpsList as $tps)
            <option value="{{ $tps->id }}" @selected($selectedTpsId == $tps->id)>{{ $tps->nama_tps }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="px-3.5 py-2 bg-white border border-notion-border rounded-md text-sm font-medium hover:bg-notion-hover transition-colors whitespace-nowrap shrink-0">
        Tampilkan TPS
      </button>
    </div>
    <a href="{{ route('tps.monitor.pdf', ['tps_id' => $selectedTpsId]) }}" target="_blank" class="px-3.5 py-2 bg-notion-text text-white text-sm font-medium rounded-md hover:bg-black transition-colors inline-flex items-center justify-center gap-1.5 shrink-0 whitespace-nowrap">
      <i data-lucide="printer" class="w-4 h-4"></i> Cetak Daftar Hadir
    </a>
  </form>

  <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 pt-4 border-t border-notion-border">
    <div class="flex flex-col">
      <span class="text-xs font-medium text-notion-gray mb-1">DPT Terdaftar di TPS</span>
      <span id="stat-total-dpt" class="text-2xl font-semibold text-notion-text tnum">{{ $stats['total_dpt'] }}</span>
    </div>
    <div class="flex flex-col sm:border-l border-notion-border sm:pl-5">
      <span class="text-xs font-medium text-notion-gray mb-1">Sudah Memilih</span>
      <span id="stat-sudah" class="text-2xl font-semibold text-emerald-700 tnum">{{ $stats['sudah_memilih'] }}</span>
    </div>
    <div class="flex flex-col border-t sm:border-t-0 sm:border-l border-notion-border pt-3 sm:pt-0 sm:pl-5">
      <span class="text-xs font-medium text-notion-gray mb-1">Belum Memilih</span>
      <span id="stat-belum" class="text-2xl font-semibold text-amber-600 tnum">{{ $stats['belum_memilih'] }}</span>
    </div>
    <div class="flex flex-col border-t sm:border-t-0 sm:border-l border-notion-border pt-3 sm:pt-0 sm:pl-5">
      <span class="text-xs font-medium text-notion-gray mb-1">Partisipasi TPS</span>
      <span id="stat-partisipasi" class="text-2xl font-semibold text-notion-blue tnum">{{ $stats['partisipasi'] }}%</span>
    </div>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">
    <div class="px-4 py-3 border-b border-notion-border bg-notion-sidebar/40 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i data-lucide="user-check" class="w-4 h-4 text-emerald-700"></i>
        <h2 class="text-sm font-semibold">Pemilih Baru Selesai Memilih ({{ $selectedTps?->kode_tps ?? 'TPS' }})</h2>
      </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
          <tr>
            <th class="py-3 px-4 font-medium">NIS</th>
            <th class="py-3 px-4 font-medium">Nama Pemilih</th>
            <th class="py-3 px-4 font-medium">Kelas</th>
            <th class="py-3 px-4 font-medium">Kode Bukti</th>
            <th class="py-3 px-4 font-medium">Status</th>
            <th class="py-3 px-4 font-medium text-right">Waktu Selesai</th>
          </tr>
        </thead>
        <tbody id="monitor-desktop-body" class="divide-y divide-notion-border">
          @forelse($stats['recent'] as $item)
            <tr class="hover:bg-notion-hover/50 transition-colors">
              <td class="py-3 px-4 font-mono text-notion-gray tnum">{{ $item['nis'] }}</td>
              <td class="py-3 px-4 font-semibold text-notion-text">{{ $item['nama'] }}</td>
              <td class="py-3 px-4">{{ $item['kelas'] }}</td>
              <td class="py-3 px-4"><span class="font-mono text-xs font-semibold bg-blue-50 text-notion-blue px-2 py-0.5 rounded border border-blue-200">{{ $item['receipt_code'] }}</span></td>
              <td class="py-3 px-4"><span class="px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Sudah Memilih — Keluar Bilik</span></td>
              <td class="py-3 px-4 text-right font-mono text-xs text-notion-gray tnum">{{ $item['voted_at'] }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-8 px-4 text-center text-notion-gray">Belum ada pemilih yang selesai memilih di TPS ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div id="monitor-mobile-body" class="md:hidden divide-y divide-notion-border">
      @forelse($stats['recent'] as $idx => $item)
        <div class="bg-white flex flex-col">
          <div onclick="toggleMobileRow('mon', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
            <div>
              <p class="font-semibold text-sm text-notion-text">{{ $item['nama'] }}</p>
              <p class="text-xs text-notion-gray font-mono tnum">{{ $item['receipt_code'] }} · {{ $item['voted_at'] }}</p>
            </div>
            <i data-lucide="chevron-down" id="mon-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
          </div>
          <div id="mon-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
            <div class="flex justify-between"><span class="text-notion-gray">NIS / Kelas</span><span class="font-mono tnum">{{ $item['nis'] }} · {{ $item['kelas'] }}</span></div>
            <div class="flex justify-between"><span class="text-notion-gray">Status</span><span class="font-medium text-emerald-700">Sudah Memilih</span></div>
          </div>
        </div>
      @empty
        <div class="p-6 text-center text-sm text-notion-gray">Belum ada pemilih selesai memilih.</div>
      @endforelse
    </div>
  </div>
@endsection

@push('scripts')
<script>
  (function () {
    const tpsId = @json($selectedTpsId);
    const isInitiallyFinished = @json($stats['is_finished']);
    if (!tpsId || isInitiallyFinished) return;

    let lastTopId = @json($stats['recent'][0]['id'] ?? null);
    let pollTimer = null;

    function stopMonitorPolling() {
      if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
      }
      const badge = document.getElementById('monitor-live-badge');
      if (badge) {
        badge.className = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-notion-sidebar border border-notion-border text-notion-gray text-xs font-medium';
        badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-notion-gray"></span> Selesai · Pemantauan Ditutup';
      }
    }

    async function refreshMonitor() {
      try {
        const res = await fetch(`{{ route('tps.monitor.feed') }}?tps_id=${tpsId}`, {
          headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();

        document.getElementById('stat-total-dpt').textContent = data.total_dpt;
        document.getElementById('stat-sudah').textContent = data.sudah_memilih;
        document.getElementById('stat-belum').textContent = data.belum_memilih;
        document.getElementById('stat-partisipasi').textContent = `${data.partisipasi}%`;
        const updatedEl = document.getElementById('monitor-updated-at');
        if (updatedEl) updatedEl.textContent = data.updated_at;

        const desktopBody = document.getElementById('monitor-desktop-body');
        const mobileBody = document.getElementById('monitor-mobile-body');

        if (data.recent.length > 0 && data.recent[0].id !== lastTopId) {
          lastTopId = data.recent[0].id;
          showToast(`${data.recent[0].nama} (${data.recent[0].receipt_code}) baru saja selesai memilih!`, 'success');
        }

        if (data.recent.length === 0) {
          desktopBody.innerHTML = '<tr><td colspan="6" class="py-8 px-4 text-center text-notion-gray">Belum ada pemilih yang selesai memilih di TPS ini.</td></tr>';
          mobileBody.innerHTML = '<div class="p-6 text-center text-sm text-notion-gray">Belum ada pemilih selesai memilih.</div>';
          return;
        }

        desktopBody.innerHTML = data.recent.map((item) => `
          <tr class="hover:bg-notion-hover/50 transition-colors">
            <td class="py-3 px-4 font-mono text-notion-gray tnum">${item.nis}</td>
            <td class="py-3 px-4 font-semibold text-notion-text">${item.nama}</td>
            <td class="py-3 px-4">${item.kelas}</td>
            <td class="py-3 px-4"><span class="font-mono text-xs font-semibold bg-blue-50 text-notion-blue px-2 py-0.5 rounded border border-blue-200">${item.receipt_code}</span></td>
            <td class="py-3 px-4"><span class="px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Sudah Memilih — Keluar Bilik</span></td>
            <td class="py-3 px-4 text-right font-mono text-xs text-notion-gray tnum">${item.voted_at}</td>
          </tr>
        `).join('');

        mobileBody.innerHTML = data.recent.map((item, idx) => `
          <div class="bg-white flex flex-col">
            <div onclick="toggleMobileRow('mon', ${idx})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
              <div>
                <p class="font-semibold text-sm text-notion-text">${item.nama}</p>
                <p class="text-xs text-notion-gray font-mono tnum">${item.receipt_code} · ${item.voted_at}</p>
              </div>
              <i data-lucide="chevron-down" id="mon-icon-${idx}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
            </div>
            <div id="mon-detail-${idx}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
              <div class="flex justify-between"><span class="text-notion-gray">NIS / Kelas</span><span class="font-mono tnum">${item.nis} · ${item.kelas}</span></div>
              <div class="flex justify-between"><span class="text-notion-gray">Status</span><span class="font-medium text-emerald-700">Sudah Memilih</span></div>
            </div>
          </div>
        `).join('');

        lucide.createIcons();

        if (data.is_finished) {
          stopMonitorPolling();
        }
      } catch (e) {}
    }

    pollTimer = setInterval(refreshMonitor, 3000);
  })();
</script>
@endpush
