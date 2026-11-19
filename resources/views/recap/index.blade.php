@extends('layouts.app')

@section('title', 'Rekapitulasi & Daftar Hadir TPS')
@section('breadcrumb', 'Rekapitulasi & Daftar Hadir')

@section('content')

  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div class="space-y-2">
      <i data-lucide="pie-chart" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Rekapitulasi</h1>
      <p class="text-sm text-notion-gray">
        Laporan resmi hasil perolehan suara pasangan calon, tingkat partisipasi per kelas.
      </p>
    </div>
    <div class="flex flex-wrap items-center gap-2 shrink-0">
      <a href="{{ route('recap.pdf') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-notion-text text-white text-sm font-medium rounded-md hover:bg-black transition-colors">
        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Rekap
      </a>
    </div>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">
    <div class="border-b border-notion-border flex flex-nowrap gap-1 overflow-x-auto whitespace-nowrap px-4 pt-2 bg-notion-sidebar/30">
      <button type="button" onclick="switchAppTab(this, 'recap-tab-1', 'recap')" data-tab-group="recap"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-notion-blue text-notion-blue flex items-center gap-2">
        <i data-lucide="award" class="w-4 h-4"></i> Perolehan
        <span class="px-1.5 py-0.5 text-xs rounded bg-notion-hover text-notion-text tnum">{{ $candidates->count() }}</span>
      </button>
      <button type="button" onclick="switchAppTab(this, 'recap-tab-2', 'recap')" data-tab-group="recap"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="graduation-cap" class="w-4 h-4"></i> Partisipasi
        <span class="px-1.5 py-0.5 text-xs rounded bg-notion-hover text-notion-text tnum">{{ $kelasStats->count() }}</span>
      </button>
      <button type="button" onclick="switchAppTab(this, 'recap-tab-3', 'recap')" data-tab-group="recap"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="map-pin" class="w-4 h-4"></i> Rekap TPS
        <span class="px-1.5 py-0.5 text-xs rounded bg-notion-hover text-notion-text tnum">{{ $tpsList->count() }}</span>
      </button>
      <button type="button" onclick="switchAppTab(this, 'recap-tab-4', 'recap')" data-tab-group="recap"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="clipboard-list" class="w-4 h-4"></i> Daftar Hadir
        <span class="px-1.5 py-0.5 text-xs rounded bg-notion-hover text-notion-text tnum">{{ $attendanceVoters->count() }}</span>
      </button>
    </div>

    <div id="recap-tab-1" data-tab-group="recap" class="app-tab-panel">
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
            <tr>
              <th class="py-3 px-4 font-medium">Peringkat</th>
              <th class="py-3 px-4 font-medium">Pasangan Calon</th>
              <th class="py-3 px-4 font-medium">Kode</th>
              <th class="py-3 px-4 font-medium">Jumlah Suara</th>
              <th class="py-3 px-4 font-medium text-right">Persentase</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-notion-border">
            @foreach($candidates as $rank => $c)
              @php $pct = $totalVotes > 0 ? round(($c->votes_count / $totalVotes) * 100, 1) : 0; @endphp
              <tr class="hover:bg-notion-hover/50 transition-colors">
                <td class="py-3 px-4 font-mono font-bold tnum">#{{ $rank + 1 }}</td>
                <td class="py-3 px-4">
                  <p class="font-semibold text-notion-text">{{ $c->ketua_nama }} &amp; {{ $c->wakil_nama }}</p>
                  <p class="text-xs text-notion-gray">Nomor Urut 0{{ $c->nomor_urut }}</p>
                </td>
                <td class="py-3 px-4 font-mono text-notion-gray">{{ $c->kode_paslon }}</td>
                <td class="py-3 px-4 font-mono font-semibold tnum">{{ $c->votes_count }}</td>
                <td class="py-3 px-4 text-right font-mono font-semibold text-notion-blue tnum">{{ $pct }}%</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="md:hidden divide-y divide-notion-border">
        @foreach($candidates as $idx => $c)
          @php $pct = $totalVotes > 0 ? round(($c->votes_count / $totalVotes) * 100, 1) : 0; @endphp
          <div class="bg-white flex flex-col">
            <div onclick="toggleMobileRow('recap1', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
              <div>
                <p class="font-semibold text-sm text-notion-text">#{{ $idx + 1 }} · {{ $c->ketua_nama }}</p>
                <p class="text-xs text-notion-gray font-mono tnum">{{ $c->votes_count }} suara ({{ $pct }}%)</p>
              </div>
              <i data-lucide="chevron-down" id="recap1-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
            </div>
            <div id="recap1-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
              <div class="flex justify-between"><span class="text-notion-gray">Wakil</span><span class="font-medium">{{ $c->wakil_nama }}</span></div>
              <div class="flex justify-between"><span class="text-notion-gray">Kode Paslon</span><span class="font-mono">{{ $c->kode_paslon }}</span></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div id="recap-tab-2" data-tab-group="recap" class="app-tab-panel hidden">
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
            <tr>
              <th class="py-3 px-4 font-medium">Kelas / Unit</th>
              <th class="py-3 px-4 font-medium">Total DPT</th>
              <th class="py-3 px-4 font-medium">Sudah Memilih</th>
              <th class="py-3 px-4 font-medium">Belum Memilih</th>
              <th class="py-3 px-4 font-medium text-right">Partisipasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-notion-border">
            @foreach($kelasStats as $ks)
              @php $kpct = $ks->total_dpt > 0 ? round(($ks->sudah_memilih / $ks->total_dpt) * 100, 1) : 0; @endphp
              <tr class="hover:bg-notion-hover/50 transition-colors">
                <td class="py-3 px-4 font-semibold text-notion-text">{{ $ks->kelas }}</td>
                <td class="py-3 px-4 font-mono tnum">{{ $ks->total_dpt }}</td>
                <td class="py-3 px-4 font-mono text-emerald-700 font-medium tnum">{{ $ks->sudah_memilih }}</td>
                <td class="py-3 px-4 font-mono text-amber-600 tnum">{{ $ks->total_dpt - $ks->sudah_memilih }}</td>
                <td class="py-3 px-4 text-right font-mono font-semibold text-notion-blue tnum">{{ $kpct }}%</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="md:hidden divide-y divide-notion-border">
        @foreach($kelasStats as $idx => $ks)
          @php $kpct = $ks->total_dpt > 0 ? round(($ks->sudah_memilih / $ks->total_dpt) * 100, 1) : 0; @endphp
          <div class="bg-white flex flex-col">
            <div onclick="toggleMobileRow('recap2', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
              <div>
                <p class="font-semibold text-sm text-notion-text">{{ $ks->kelas }}</p>
                <p class="text-xs text-notion-gray font-mono tnum">{{ $ks->sudah_memilih }}/{{ $ks->total_dpt }} ({{ $kpct }}%)</p>
              </div>
              <i data-lucide="chevron-down" id="recap2-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
            </div>
            <div id="recap2-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
              <div class="flex justify-between"><span class="text-notion-gray">Sudah Memilih</span><span class="font-mono text-emerald-700 tnum">{{ $ks->sudah_memilih }}</span></div>
              <div class="flex justify-between"><span class="text-notion-gray">Belum Memilih</span><span class="font-mono text-amber-600 tnum">{{ $ks->total_dpt - $ks->sudah_memilih }}</span></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div id="recap-tab-3" data-tab-group="recap" class="app-tab-panel hidden">
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
            <tr>
              <th class="py-3 px-4 font-medium">TPS</th>
              <th class="py-3 px-4 font-medium">Lokasi</th>
              <th class="py-3 px-4 font-medium">Total DPT</th>
              <th class="py-3 px-4 font-medium">Hadir / Memilih</th>
              <th class="py-3 px-4 font-medium text-right">Partisipasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-notion-border">
            @foreach($tpsList as $tps)
              @php $tpct = $tps->total_dpt > 0 ? round(($tps->sudah_memilih / $tps->total_dpt) * 100, 1) : 0; @endphp
              <tr class="hover:bg-notion-hover/50 transition-colors">
                <td class="py-3 px-4 font-semibold text-notion-text">{{ $tps->nama_tps }}</td>
                <td class="py-3 px-4 text-notion-gray">{{ $tps->lokasi ?? '-' }}</td>
                <td class="py-3 px-4 font-mono tnum">{{ $tps->total_dpt }}</td>
                <td class="py-3 px-4 font-mono text-emerald-700 font-medium tnum">{{ $tps->sudah_memilih }}</td>
                <td class="py-3 px-4 text-right font-mono font-semibold text-notion-blue tnum">{{ $tpct }}%</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="md:hidden divide-y divide-notion-border">
        @foreach($tpsList as $idx => $tps)
          @php $tpct = $tps->total_dpt > 0 ? round(($tps->sudah_memilih / $tps->total_dpt) * 100, 1) : 0; @endphp
          <div class="bg-white flex flex-col">
            <div onclick="toggleMobileRow('recap3', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
              <div>
                <p class="font-semibold text-sm text-notion-text">{{ $tps->nama_tps }}</p>
                <p class="text-xs text-notion-gray font-mono tnum">{{ $tps->sudah_memilih }}/{{ $tps->total_dpt }} ({{ $tpct }}%)</p>
              </div>
              <i data-lucide="chevron-down" id="recap3-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
            </div>
            <div id="recap3-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
              <div class="flex justify-between"><span class="text-notion-gray">Lokasi</span><span>{{ $tps->lokasi ?? '-' }}</span></div>
              <div class="flex justify-between"><span class="text-notion-gray">Hadir</span><span class="font-mono text-emerald-700 tnum">{{ $tps->sudah_memilih }}</span></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div id="recap-tab-4" data-tab-group="recap" class="app-tab-panel hidden">
      <form method="GET" action="{{ route('recap.index') }}" class="p-4 border-b border-notion-border flex flex-col lg:flex-row lg:items-center justify-between gap-2.5 bg-notion-sidebar/40">
        <input type="hidden" name="tab" value="daftar-hadir">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 w-full">
          <div class="relative flex-1 min-w-[180px]">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari NIS, nama, atau kode bukti..." class="w-full border border-notion-border rounded-md pl-9 pr-3 py-2 text-sm bg-white">
          </div>
          <div class="sm:w-48 shrink-0">
            <select name="tps_id" class="modern-select w-full">
              <option value="">Semua TPS</option>
              @foreach($tpsList as $tps)
                <option value="{{ $tps->id }}" @selected($filterTpsId == $tps->id)>{{ $tps->nama_tps }}</option>
              @endforeach
            </select>
          </div>
          <div class="sm:w-44 shrink-0">
            <select name="hadir" class="modern-select w-full">
              <option value="">Semua Kehadiran</option>
              <option value="sudah" @selected($filterHadir === 'sudah')>Hadir / Memilih</option>
              <option value="belum" @selected($filterHadir === 'belum')>Belum Hadir</option>
            </select>
          </div>
          <button type="submit" class="px-3.5 py-2 bg-white border border-notion-border rounded-md text-sm font-medium hover:bg-notion-hover transition-colors whitespace-nowrap shrink-0">Filter</button>
        </div>
      </form>

      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
            <tr>
              <th class="py-3 px-4 font-medium">NIS</th>
              <th class="py-3 px-4 font-medium">Nama Pemilih</th>
              <th class="py-3 px-4 font-medium">Kelas</th>
              <th class="py-3 px-4 font-medium">TPS</th>
              <th class="py-3 px-4 font-medium">Kode Bukti</th>
              <th class="py-3 px-4 font-medium">Kehadiran</th>
              <th class="py-3 px-4 font-medium text-right">Waktu Hadir</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-notion-border">
            @forelse($attendanceVoters as $av)
              <tr class="hover:bg-notion-hover/50 transition-colors">
                <td class="py-3 px-4 font-mono text-notion-gray tnum">{{ $av->nis }}</td>
                <td class="py-3 px-4 font-semibold text-notion-text">{{ $av->nama }}</td>
                <td class="py-3 px-4">{{ $av->kelas }}</td>
                <td class="py-3 px-4 font-mono text-xs">{{ $av->tps?->kode_tps ?? '-' }}</td>
                <td class="py-3 px-4 font-mono text-xs text-notion-blue">{{ $av->receipt_code ?? '-' }}</td>
                <td class="py-3 px-4">
                  @if($av->has_voted)
                    <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Hadir / Memilih</span>
                  @else
                    <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-notion-hover text-notion-gray border border-notion-border">Belum Hadir</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-right font-mono text-xs text-notion-gray tnum">{{ $av->voted_at?->format('d/m/Y H:i') ?? '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="7" class="py-8 px-4 text-center text-notion-gray">Data daftar hadir tidak ditemukan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="md:hidden divide-y divide-notion-border">
        @forelse($attendanceVoters as $idx => $av)
          <div class="bg-white flex flex-col">
            <div onclick="toggleMobileRow('recap4', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
              <div>
                <p class="font-semibold text-sm text-notion-text">{{ $av->nama }}</p>
                <p class="text-xs text-notion-gray font-mono tnum">{{ $av->nis }} · {{ $av->tps?->kode_tps ?? '-' }}</p>
              </div>
              <i data-lucide="chevron-down" id="recap4-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
            </div>
            <div id="recap4-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
              <div class="flex justify-between"><span class="text-notion-gray">Kehadiran</span><span class="font-medium {{ $av->has_voted ? 'text-emerald-700' : 'text-notion-gray' }}">{{ $av->has_voted ? 'Hadir / Memilih' : 'Belum Hadir' }}</span></div>
              <div class="flex justify-between"><span class="text-notion-gray">Kode Bukti</span><span class="font-mono text-xs">{{ $av->receipt_code ?? '-' }}</span></div>
              <div class="flex justify-between"><span class="text-notion-gray">Waktu</span><span class="font-mono text-xs tnum">{{ $av->voted_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
            </div>
          </div>
        @empty
          <div class="p-6 text-center text-sm text-notion-gray">Data daftar hadir kosong.</div>
        @endforelse
      </div>

      <div class="px-4 py-3 border-t border-notion-border bg-notion-sidebar/30 flex items-center justify-between gap-3 text-sm">
        <span class="text-xs sm:text-sm text-notion-gray tnum">
          @if($attendanceVoters->total() > 0)
            Menampilkan <strong class="text-notion-text">{{ $attendanceVoters->firstItem() }}–{{ $attendanceVoters->lastItem() }}</strong> dari <strong class="text-notion-text">{{ $attendanceVoters->total() }}</strong> data
          @else
            Menampilkan 0 data
          @endif
        </span>
        <div class="flex items-center gap-1.5">
          @if($attendanceVoters->onFirstPage())
            <span class="p-1.5 rounded-md border border-notion-border bg-notion-sidebar text-notion-gray/40 cursor-not-allowed" aria-disabled="true">
              <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </span>
          @else
            <a href="{{ $attendanceVoters->previousPageUrl() }}" class="p-1.5 rounded-md border border-notion-border bg-white text-notion-text hover:bg-notion-hover transition-colors" title="Sebelumnya">
              <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
          @endif

          @if($attendanceVoters->hasMorePages())
            <a href="{{ $attendanceVoters->nextPageUrl() }}" class="p-1.5 rounded-md border border-notion-border bg-white text-notion-text hover:bg-notion-hover transition-colors" title="Berikutnya">
              <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
          @else
            <span class="p-1.5 rounded-md border border-notion-border bg-notion-sidebar text-notion-gray/40 cursor-not-allowed" aria-disabled="true">
              <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'daftar-hadir' || params.has('tps_id') || params.has('hadir') || params.has('page')) {
      const btn = document.querySelectorAll('.app-tab[data-tab-group="recap"]')[3];
      if (btn) switchAppTab(btn, 'recap-tab-4', 'recap');
    }
  });
</script>
@endpush
