@extends('layouts.app')

@section('title', 'Kandidat Paslon OSIS')
@section('breadcrumb', 'Kandidat Paslon')

@section('content')

  <div class="space-y-2">
    <i data-lucide="award" class="w-12 h-12 text-notion-text/80"></i>
    <h1 class="text-3xl font-bold tracking-tight">Kandidat Pasangan Calon</h1>
    <p class="text-sm text-notion-gray">
      Pengelolaan daftar pasangan calon Ketua dan Wakil Ketua OSIS.
    </p>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">

    <form method="GET" action="{{ route('candidates.index') }}" class="p-4 border-b border-notion-border flex flex-col lg:flex-row lg:items-center justify-between gap-2.5 bg-notion-sidebar/40">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 w-full">
        <div class="relative flex-1 min-w-[200px]">
          <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
          <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama ketua, wakil, kode paslon, atau kelas..." class="w-full border border-notion-border rounded-md pl-9 pr-3 py-2 text-sm bg-white">
        </div>
        <div class="sm:w-48 shrink-0">
          <select name="status" class="modern-select w-full">
            <option value="">Semua Status</option>
            <option value="aktif" @selected($status === 'aktif')>Aktif (Tampil di Bilik)</option>
            <option value="verifikasi" @selected($status === 'verifikasi')>Perlu Verifikasi</option>
            <option value="draft" @selected($status === 'draft')>Draft</option>
          </select>
        </div>
        <button type="submit" class="px-3.5 py-2 bg-white border border-notion-border rounded-md text-sm font-medium hover:bg-notion-hover transition-colors whitespace-nowrap shrink-0">Filter</button>
      </div>
      <a href="{{ route('candidates.create') }}" class="w-full lg:w-auto px-3.5 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover transition-colors flex items-center justify-center gap-1.5 shrink-0 whitespace-nowrap">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah
      </a>
    </form>

    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
          <tr>
            <th class="py-3 px-4 font-medium">No. Urut</th>
            <th class="py-3 px-4 font-medium">Pasangan Calon</th>
            <th class="py-3 px-4 font-medium">Kode</th>
            <th class="py-3 px-4 font-medium">Perolehan</th>
            <th class="py-3 px-4 font-medium">Status</th>
            <th class="py-3 px-4 font-medium text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-notion-border">
          @forelse($candidates as $cand)
            <tr class="hover:bg-notion-hover/50 transition-colors">
              <td class="py-3 px-4 font-mono font-bold text-notion-text tnum">0{{ $cand->nomor_urut }}</td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-lg bg-notion-sidebar border border-notion-border flex items-center justify-center shrink-0 overflow-hidden">
                    @if($cand->foto_path)
                      <img src="{{ asset('uploads/' . $cand->foto_path) }}" alt="{{ $cand->kode_paslon }}" class="w-full h-full object-cover">
                    @else
                      <i data-lucide="user-check" class="w-4 h-4 text-notion-gray"></i>
                    @endif
                  </div>
                  <div>
                    <p class="font-semibold text-notion-text">{{ $cand->ketua_nama }} <span class="text-xs font-normal text-notion-gray">({{ $cand->ketua_kelas }})</span></p>
                    <p class="text-xs text-notion-gray">Wakil: {{ $cand->wakil_nama }} ({{ $cand->wakil_kelas }})</p>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4 font-mono text-notion-gray">{{ $cand->kode_paslon }}</td>
              <td class="py-3 px-4 font-mono font-semibold text-notion-text tnum">{{ $cand->votes_count }} suara</td>
              <td class="py-3 px-4">
                @if($cand->status === 'aktif')
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                @elseif($cand->status === 'verifikasi')
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">Verifikasi</span>
                @else
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-notion-hover text-notion-gray border border-notion-border">Draft</span>
                @endif
              </td>
              <td class="py-3 px-4 text-right space-x-1">
                <a href="{{ route('candidates.edit', $cand) }}" class="p-1.5 text-notion-gray hover:text-notion-blue hover:bg-blue-50 rounded inline-flex" title="Edit Paslon">
                  <i data-lucide="edit-2" class="w-4 h-4"></i>
                </a>
                <button type="button" onclick="openDeleteCandidate({{ $cand->id }}, '{{ addslashes($cand->kode_paslon) }}')" class="p-1.5 text-notion-gray hover:text-notion-red hover:bg-red-50 rounded" title="Hapus Paslon">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-8 px-4 text-center text-notion-gray">Belum ada kandidat paslon yang sesuai filter.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="md:hidden divide-y divide-notion-border">
      @forelse($candidates as $idx => $cand)
        <div class="bg-white flex flex-col">
          <div onclick="toggleMobileRow('cand', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
            <div class="min-w-0 pr-3">
              <p class="font-semibold text-sm text-notion-text truncate">0{{ $cand->nomor_urut }} · {{ $cand->ketua_nama }}</p>
              <p class="text-xs text-notion-gray font-mono">{{ $cand->kode_paslon }} · {{ $cand->votes_count }} suara</p>
            </div>
            <i data-lucide="chevron-down" id="cand-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
          </div>
          <div id="cand-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
            <div class="flex justify-between"><span class="text-notion-gray">Wakil Ketua</span><span class="font-medium">{{ $cand->wakil_nama }} ({{ $cand->wakil_kelas }})</span></div>
            <div class="flex justify-between"><span class="text-notion-gray">Status</span><span class="font-medium">{{ ucfirst($cand->status) }}</span></div>
            <div class="pt-2 border-t border-notion-border flex justify-end gap-2">
              <a href="{{ route('candidates.edit', $cand) }}" class="px-3 py-1 bg-white border border-notion-border rounded text-xs font-medium">Edit</a>
              <button type="button" onclick="openDeleteCandidate({{ $cand->id }}, '{{ addslashes($cand->kode_paslon) }}')" class="px-3 py-1 bg-notion-redBg text-notion-red rounded text-xs font-medium">Hapus</button>
            </div>
          </div>
        </div>
      @empty
        <div class="p-6 text-center text-sm text-notion-gray">Belum ada kandidat paslon.</div>
      @endforelse
    </div>
  </div>
@endsection

@push('modals')

  <div id="delete-candidate-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('delete-candidate-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-sm w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="p-6 space-y-4">
        <div class="w-10 h-10 rounded-full bg-notion-redBg text-notion-red flex items-center justify-center">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold text-lg">Hapus Pasangan Calon?</h3>
          <p class="text-sm text-notion-gray mt-1">Data kandidat <span id="delete-cand-label" class="font-mono font-semibold text-notion-text"></span> beserta suara terkait akan dihapus permanen.</p>
        </div>
        <form id="delete-candidate-form" method="POST" class="flex justify-end gap-2 pt-2">
          @csrf
          @method('DELETE')
          <button type="button" onclick="closeAppModal('delete-candidate-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-red text-white text-sm font-medium rounded-md hover:bg-red-700">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
  function openDeleteCandidate(id, label) {
    document.getElementById('delete-candidate-form').action = `/kandidat/${id}`;
    document.getElementById('delete-cand-label').textContent = label;
    openAppModal('delete-candidate-modal');
  }
</script>
@endpush
