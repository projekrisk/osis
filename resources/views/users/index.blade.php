@extends('layouts.app')

@section('title', 'Pengguna')
@section('breadcrumb', 'Pengguna')

@section('content')

  <div class="space-y-2">
    <i data-lucide="shield" class="w-12 h-12 text-notion-text/80"></i>
    <h1 class="text-3xl font-bold tracking-tight">Pengguna</h1>
    <p class="text-sm text-notion-gray">
      Kelola akun pengguna sistem pemilihan OSIS.
    </p>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">
    <form method="GET" action="{{ route('users.index') }}" class="p-4 border-b border-notion-border flex flex-col lg:flex-row lg:items-center justify-between gap-2.5 bg-notion-sidebar/40">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 w-full">
        <div class="relative flex-1 min-w-[200px]">
          <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
          <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau email pengguna..." class="w-full border border-notion-border rounded-md pl-9 pr-3 py-2 text-sm bg-white">
        </div>
        <div class="sm:w-44 shrink-0">
          <select name="role" class="modern-select w-full">
            <option value="">Semua Role</option>
            <option value="admin" @selected($role === 'admin')>Admin</option>
            <option value="panitia" @selected($role === 'panitia')>Panitia</option>
          </select>
        </div>
        <button type="submit" class="px-3.5 py-2 bg-white border border-notion-border rounded-md text-sm font-medium hover:bg-notion-hover transition-colors whitespace-nowrap shrink-0">Filter</button>
      </div>
      <button type="button" onclick="openAppModal('create-user-modal')" class="w-full lg:w-auto px-3.5 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover transition-colors flex items-center justify-center gap-1.5 shrink-0 whitespace-nowrap">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah
      </button>
    </form>

    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-notion-sidebar text-notion-gray border-b border-notion-border">
          <tr>
            <th class="py-3 px-4 font-medium">Nama Pengguna</th>
            <th class="py-3 px-4 font-medium">Email</th>
            <th class="py-3 px-4 font-medium">Role</th>
            <th class="py-3 px-4 font-medium">Tugas TPS</th>
            <th class="py-3 px-4 font-medium text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-notion-border">
          @forelse($users as $u)
            <tr class="hover:bg-notion-hover/50 transition-colors">
              <td class="py-3 px-4 font-semibold text-notion-text">{{ $u->name }}</td>
              <td class="py-3 px-4 font-mono text-xs text-notion-gray">{{ $u->email }}</td>
              <td class="py-3 px-4">
                @if($u->role === 'admin')
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-blue-50 text-notion-blue border border-blue-200">Admin</span>
                @else
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Panitia</span>
                @endif
              </td>
              <td class="py-3 px-4 text-notion-gray">{{ $u->tps?->nama_tps ?? ($u->role === 'admin' ? 'Semua TPS' : '-') }}</td>
              <td class="py-3 px-4 text-right space-x-1">
                <button type="button" onclick='openEditUser(@json($u))' class="p-1.5 text-notion-gray hover:text-notion-blue hover:bg-blue-50 rounded" title="Edit Pengguna">
                  <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="openDeleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')" class="p-1.5 text-notion-gray hover:text-notion-red hover:bg-red-50 rounded" title="Hapus Pengguna">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="py-8 px-4 text-center text-notion-gray">Belum ada data pengguna.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="md:hidden divide-y divide-notion-border">
      @forelse($users as $idx => $u)
        <div class="bg-white flex flex-col">
          <div onclick="toggleMobileRow('usr', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
            <div class="min-w-0 pr-3">
              <p class="font-semibold text-sm text-notion-text truncate">{{ $u->name }}</p>
              <p class="text-xs text-notion-gray font-mono truncate">{{ $u->email }} · {{ strtoupper($u->role) }}</p>
            </div>
            <i data-lucide="chevron-down" id="usr-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
          </div>
          <div id="usr-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
            <div class="flex justify-between"><span class="text-notion-gray">Tugas TPS</span><span>{{ $u->tps?->nama_tps ?? 'Semua TPS' }}</span></div>
            <div class="pt-2 border-t border-notion-border flex justify-end gap-2">
              <button type="button" onclick='openEditUser(@json($u))' class="px-3 py-1 bg-white border border-notion-border rounded text-xs font-medium">Edit</button>
              <button type="button" onclick="openDeleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')" class="px-3 py-1 bg-notion-redBg text-notion-red rounded text-xs font-medium">Hapus</button>
            </div>
          </div>
        </div>
      @empty
        <div class="p-6 text-center text-sm text-notion-gray">Belum ada pengguna.</div>
      @endforelse
    </div>
  </div>
@endsection

@push('modals')

  <div id="create-user-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('create-user-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <h3 class="font-bold text-lg">Tambah Pengguna Baru</h3>
        <button type="button" onclick="closeAppModal('create-user-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form action="{{ route('users.store') }}" method="POST" class="p-6 overflow-y-auto flex-1 space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
          <input type="text" name="name" required placeholder="Nama panitia / admin..." class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Email Login</label>
          <input type="email" name="email" required placeholder="panitia2@osis.sch.id" class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Role Pengguna</label>
          <select name="role" class="modern-select w-full">
            <option value="panitia">Panitia TPS</option>
            <option value="admin">Administrator</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Penugasan TPS (Khusus Panitia)</label>
          <select name="tps_id" class="modern-select w-full">
            <option value="">Tidak Terikat TPS</option>
            @foreach($tpsList as $tps)
              <option value="{{ $tps->id }}">{{ $tps->nama_tps }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <div class="relative">
            <input type="password" name="password" id="create-user-pw" required minlength="6" placeholder="Minimal 6 karakter..." class="w-full border border-notion-border rounded-md pl-3 pr-10 py-2 text-sm bg-white">
            <button type="button" onclick="togglePassword('create-user-pw', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-notion-gray hover:text-notion-text">
              <i data-lucide="eye" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeAppModal('create-user-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Simpan Pengguna</button>
        </div>
      </form>
    </div>
  </div>

  <div id="edit-user-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('edit-user-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <h3 class="font-bold text-lg">Edit Data Pengguna</h3>
        <button type="button" onclick="closeAppModal('edit-user-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form id="edit-user-form" method="POST" class="p-6 overflow-y-auto flex-1 space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
          <input type="text" name="name" id="edit-user-name" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Email Login</label>
          <input type="email" name="email" id="edit-user-email" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Role Pengguna</label>
          <select name="role" id="edit-user-role" class="modern-select w-full">
            <option value="panitia">Panitia TPS</option>
            <option value="admin">Administrator</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Penugasan TPS</label>
          <select name="tps_id" id="edit-user-tps" class="modern-select w-full">
            <option value="">Tidak Terikat TPS</option>
            @foreach($tpsList as $tps)
              <option value="{{ $tps->id }}">{{ $tps->nama_tps }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Password Baru (Opsional)</label>
          <div class="relative">
            <input type="password" name="password" id="edit-user-pw" minlength="6" placeholder="Kosongkan jika tidak diubah" class="w-full border border-notion-border rounded-md pl-3 pr-10 py-2 text-sm bg-white">
            <button type="button" onclick="togglePassword('edit-user-pw', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-notion-gray hover:text-notion-text">
              <i data-lucide="eye" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeAppModal('edit-user-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Perbarui</button>
        </div>
      </form>
    </div>
  </div>

  <div id="delete-user-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('delete-user-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-sm w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="p-6 space-y-4">
        <div class="w-10 h-10 rounded-full bg-notion-redBg text-notion-red flex items-center justify-center">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold text-lg">Hapus Pengguna?</h3>
          <p class="text-sm text-notion-gray mt-1">Akun <span id="delete-user-label" class="font-semibold text-notion-text"></span> akan dihapus dari sistem.</p>
        </div>
        <form id="delete-user-form" method="POST" class="flex justify-end gap-2 pt-2">
          @csrf
          @method('DELETE')
          <button type="button" onclick="closeAppModal('delete-user-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-red text-white text-sm font-medium rounded-md hover:bg-red-700">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
  function openEditUser(u) {
    document.getElementById('edit-user-form').action = `/pengguna/${u.id}`;
    document.getElementById('edit-user-name').value = u.name;
    document.getElementById('edit-user-email').value = u.email;
    const roleSel = document.getElementById('edit-user-role');
    roleSel.value = u.role;
    roleSel.syncModernLabel?.();
    const tpsSel = document.getElementById('edit-user-tps');
    tpsSel.value = u.tps_id || '';
    tpsSel.syncModernLabel?.();
    openAppModal('edit-user-modal');
  }

  function openDeleteUser(id, name) {
    document.getElementById('delete-user-form').action = `/pengguna/${id}`;
    document.getElementById('delete-user-label').textContent = name;
    openAppModal('delete-user-modal');
  }
</script>
@endpush
