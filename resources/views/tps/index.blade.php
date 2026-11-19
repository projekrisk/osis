@extends('layouts.app')

@section('title', 'Pengaturan TPS')
@section('breadcrumb', 'Pengaturan TPS')

@section('content')

  <div class="space-y-2">
    <i data-lucide="map-pin" class="w-12 h-12 text-notion-text/80"></i>
    <h1 class="text-3xl font-bold tracking-tight">Pengaturan TPS</h1>
    <p class="text-sm text-notion-gray">
      Kelola daftar Tempat Pemungutan Suara (TPS) sekolah.
    </p>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">
    <form method="GET" action="{{ route('tps.index') }}" class="p-4 border-b border-notion-border flex flex-col lg:flex-row lg:items-center justify-between gap-2.5 bg-notion-sidebar/40">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 w-full">
        <div class="relative flex-1 min-w-[200px]">
          <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
          <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode TPS, nama TPS, atau lokasi..." class="w-full border border-notion-border rounded-md pl-9 pr-3 py-2 text-sm bg-white">
        </div>
        <div class="sm:w-44 shrink-0">
          <select name="status" class="modern-select w-full">
            <option value="">Semua Status</option>
            <option value="aktif" @selected($status === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
          </select>
        </div>
        <button type="submit" class="px-3.5 py-2 bg-white border border-notion-border rounded-md text-sm font-medium hover:bg-notion-hover transition-colors whitespace-nowrap shrink-0">Filter</button>
      </div>
      <button type="button" onclick="openAppModal('create-tps-modal')" class="w-full lg:w-auto px-3.5 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover transition-colors flex items-center justify-center gap-1.5 shrink-0 whitespace-nowrap">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah TPS
      </button>
    </form>

    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
          <tr>
            <th class="py-3 px-4 font-medium">Kode TPS</th>
            <th class="py-3 px-4 font-medium">Nama TPS</th>
            <th class="py-3 px-4 font-medium">Lokasi</th>
            <th class="py-3 px-4 font-medium">Pemilih / Hadir</th>
            <th class="py-3 px-4 font-medium">Status</th>
            <th class="py-3 px-4 font-medium text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-notion-border">
          @forelse($tpsList as $tps)
            <tr class="hover:bg-notion-hover/50 transition-colors">
              <td class="py-3 px-4 font-mono font-semibold text-notion-text">{{ $tps->kode_tps }}</td>
              <td class="py-3 px-4 font-semibold text-notion-text">{{ $tps->nama_tps }}</td>
              <td class="py-3 px-4 text-notion-gray">{{ $tps->lokasi ?? '-' }}</td>
              <td class="py-3 px-4 font-mono tnum">
                <span class="font-semibold text-notion-blue">{{ $tps->voted_voters_count }}</span> / {{ $tps->voters_count }} pemilih
              </td>
              <td class="py-3 px-4">
                @if($tps->status === 'aktif')
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                @else
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-notion-hover text-notion-gray border border-notion-border">Nonaktif</span>
                @endif
              </td>
              <td class="py-3 px-4 text-right space-x-1">
                <a href="{{ route('tps.monitor', ['tps_id' => $tps->id]) }}" class="p-1.5 text-notion-gray hover:text-notion-blue hover:bg-blue-50 rounded inline-flex" title="Pantau Realtime">
                  <i data-lucide="radio" class="w-4 h-4"></i>
                </a>
                <button type="button" onclick='openEditTps(@json($tps))' class="p-1.5 text-notion-gray hover:text-notion-blue hover:bg-blue-50 rounded" title="Edit TPS">
                  <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="openDeleteTps({{ $tps->id }}, '{{ addslashes($tps->kode_tps) }}')" class="p-1.5 text-notion-gray hover:text-notion-red hover:bg-red-50 rounded" title="Hapus TPS">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-8 px-4 text-center text-notion-gray">Belum ada data TPS.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="md:hidden divide-y divide-notion-border">
      @forelse($tpsList as $idx => $tps)
        <div class="bg-white flex flex-col">
          <div onclick="toggleMobileRow('tps', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
            <div class="min-w-0 pr-3">
              <p class="font-semibold text-sm text-notion-text truncate">{{ $tps->nama_tps }}</p>
              <p class="text-xs text-notion-gray font-mono tnum">{{ $tps->kode_tps }} · {{ $tps->voted_voters_count }}/{{ $tps->voters_count }} hadir</p>
            </div>
            <i data-lucide="chevron-down" id="tps-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
          </div>
          <div id="tps-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
            <div class="flex justify-between"><span class="text-notion-gray">Lokasi</span><span class="font-medium">{{ $tps->lokasi ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-notion-gray">Status</span><span class="font-medium">{{ ucfirst($tps->status) }}</span></div>
            <div class="pt-2 border-t border-notion-border flex justify-end gap-2">
              <a href="{{ route('tps.monitor', ['tps_id' => $tps->id]) }}" class="px-3 py-1 bg-blue-50 text-notion-blue border border-blue-200 rounded text-xs font-medium">Pantau</a>
              <button type="button" onclick='openEditTps(@json($tps))' class="px-3 py-1 bg-white border border-notion-border rounded text-xs font-medium">Edit</button>
              <button type="button" onclick="openDeleteTps({{ $tps->id }}, '{{ addslashes($tps->kode_tps) }}')" class="px-3 py-1 bg-notion-redBg text-notion-red rounded text-xs font-medium">Hapus</button>
            </div>
          </div>
        </div>
      @empty
        <div class="p-6 text-center text-sm text-notion-gray">Belum ada data TPS.</div>
      @endforelse
    </div>
  </div>
@endsection

@push('modals')

  <div id="create-tps-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('create-tps-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <h3 class="font-bold text-lg">Tambah TPS Baru</h3>
        <button type="button" onclick="closeAppModal('create-tps-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form action="{{ route('tps.store') }}" method="POST" class="p-6 overflow-y-auto flex-1 space-y-4">
        @csrf
        <div style="margin-top:0">
          <label class="block text-sm font-medium mb-1">Kode TPS</label>
          <input type="text" name="kode_tps" required placeholder="Contoh: TPS-04" class="w-full border border-notion-border rounded-md px-3 py-2 text-sm font-mono">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nama TPS</label>
          <input type="text" name="nama_tps" required placeholder="Contoh: TPS 04 — Lapangan Indoor" class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Lokasi Ruangan</label>
          <input type="text" name="lokasi" placeholder="Contoh: Gedung D Lantai 1" class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Status TPS</label>
          <select name="status" class="modern-select w-full">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeAppModal('create-tps-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Simpan TPS</button>
        </div>
      </form>
    </div>
  </div>

  <div id="edit-tps-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('edit-tps-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <h3 class="font-bold text-lg">Edit Data TPS</h3>
        <button type="button" onclick="closeAppModal('edit-tps-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form id="edit-tps-form" method="POST" class="p-6 overflow-y-auto flex-1 space-y-4">
        @csrf
        @method('PUT')
        <div style="margin-top:0">
          <label class="block text-sm font-medium mb-1">Kode TPS</label>
          <input type="text" name="kode_tps" id="edit-tps-kode" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm font-mono">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nama TPS</label>
          <input type="text" name="nama_tps" id="edit-tps-nama" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Lokasi Ruangan</label>
          <input type="text" name="lokasi" id="edit-tps-lokasi" class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Status TPS</label>
          <select name="status" id="edit-tps-status" class="modern-select w-full">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeAppModal('edit-tps-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Perbarui</button>
        </div>
      </form>
    </div>
  </div>

  <div id="delete-tps-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('delete-tps-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-sm w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="p-6 space-y-4">
        <div class="w-10 h-10 rounded-full bg-notion-redBg text-notion-red flex items-center justify-center">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold text-lg">Hapus TPS?</h3>
          <p class="text-sm text-notion-gray mt-1">TPS <span id="delete-tps-label" class="font-mono font-semibold text-notion-text"></span> akan dihapus dari sistem.</p>
        </div>
        <form id="delete-tps-form" method="POST" class="flex justify-end gap-2 pt-2">
          @csrf
          @method('DELETE')
          <button type="button" onclick="closeAppModal('delete-tps-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-red text-white text-sm font-medium rounded-md hover:bg-red-700">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
  function openEditTps(tps) {
    document.getElementById('edit-tps-form').action = `/tps/${tps.id}`;
    document.getElementById('edit-tps-kode').value = tps.kode_tps;
    document.getElementById('edit-tps-nama').value = tps.nama_tps;
    document.getElementById('edit-tps-lokasi').value = tps.lokasi || '';
    const st = document.getElementById('edit-tps-status');
    st.value = tps.status;
    st.syncModernLabel?.();
    openAppModal('edit-tps-modal');
  }

  function openDeleteTps(id, kode) {
    document.getElementById('delete-tps-form').action = `/tps/${id}`;
    document.getElementById('delete-tps-label').textContent = kode;
    openAppModal('delete-tps-modal');
  }
</script>
@endpush
