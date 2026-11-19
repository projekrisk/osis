@extends('layouts.app')

@section('title', 'Data Pemilih Tetap (DPT)')
@section('breadcrumb', 'Data Pemilih (DPT)')

@section('content')

  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div class="space-y-2">
      <i data-lucide="users" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Data Pemilih Tetap (DPT)</h1>
      <p class="text-sm text-notion-gray">
        Manajemen daftar siswa dan pemilih sah beserta penugasan TPS.
      </p>
    </div>
    <div class="flex flex-wrap items-center gap-2 shrink-0">
      <div class="relative" id="print-dropdown-wrap">
        <button type="button" onclick="togglePrintDropdown(event)" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-notion-border text-notion-text text-sm font-medium rounded-md hover:bg-notion-hover transition-colors">
          <i data-lucide="printer" class="w-4 h-4 text-notion-gray"></i>
          <span>Cetak</span>
          <i data-lucide="chevron-down" class="w-4 h-4 text-notion-gray"></i>
        </button>
        <div id="print-dropdown-menu" class="hidden absolute right-0 top-full mt-1 w-48 bg-white border border-notion-border rounded-lg shadow-lg p-1.5 z-40">
          <a href="{{ route('voters.print-dpt', array_filter(['tps_id' => $tpsId])) }}" target="_blank" class="w-full px-3 py-2 text-sm text-notion-text hover:bg-notion-hover rounded-md flex items-center gap-2">
            <i data-lucide="file-text" class="w-4 h-4 text-notion-gray"></i> Cetak DPT
          </a>
          @if(auth()->user()?->isAdmin())
            <a href="{{ route('voters.print-cards') }}" target="_blank" class="w-full px-3 py-2 text-sm text-notion-text hover:bg-notion-hover rounded-md flex items-center gap-2">
              <i data-lucide="credit-card" class="w-4 h-4 text-notion-gray"></i> Cetak Kartu
            </a>
          @endif
        </div>
      </div>

      @if(auth()->user()?->isAdmin())
        <button type="button" onclick="openAppModal('import-voter-modal')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-notion-border text-notion-text text-sm font-medium rounded-md hover:bg-notion-hover transition-colors">
          <i data-lucide="file-spreadsheet" class="w-4 h-4 text-notion-gray"></i> Impor
        </button>
        <button type="button" onclick="openAppModal('create-voter-modal')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover transition-colors">
          <i data-lucide="plus" class="w-4 h-4"></i> Tambah
        </button>
      @endif
    </div>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">

    <form method="GET" action="{{ route('voters.index') }}" class="p-4 border-b border-notion-border flex flex-col lg:flex-row lg:items-center justify-between gap-2.5 bg-notion-sidebar/40">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 w-full">
        <div class="relative flex-1 min-w-[180px]">
          <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
          <input type="text" name="q" value="{{ $search }}" placeholder="Cari NIS, nama siswa, atau token..." class="w-full border border-notion-border rounded-md pl-9 pr-3 py-2 text-sm bg-white">
        </div>
        <div class="sm:w-40 shrink-0">
          <select name="kelas" class="modern-select w-full">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
              <option value="{{ $k }}" @selected($kelas === $k)>{{ $k }}</option>
            @endforeach
          </select>
        </div>
        <div class="sm:w-44 shrink-0">
          <select name="tps_id" class="modern-select w-full">
            <option value="">Semua TPS</option>
            @foreach($tpsList as $tps)
              <option value="{{ $tps->id }}" @selected($tpsId == $tps->id)>{{ $tps->kode_tps }}</option>
            @endforeach
          </select>
        </div>
        <div class="sm:w-40 shrink-0">
          <select name="status_pilih" class="modern-select w-full">
            <option value="">Semua Status</option>
            <option value="sudah" @selected($statusPilih === 'sudah')>Sudah Memilih</option>
            <option value="belum" @selected($statusPilih === 'belum')>Belum Memilih</option>
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
            <th class="py-3 px-4 font-medium">TPS Terdaftar</th>
            <th class="py-3 px-4 font-medium">Token</th>
            <th class="py-3 px-4 font-medium">Hak Pilih</th>
            @if(auth()->user()?->isAdmin())
              <th class="py-3 px-4 font-medium text-right">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody class="divide-y divide-notion-border">
          @forelse($voters as $voter)
            <tr class="hover:bg-notion-hover/50 transition-colors">
              <td class="py-3 px-4 font-mono text-notion-gray tnum">{{ $voter->nis }}</td>
              <td class="py-3 px-4 font-semibold text-notion-text">
                {{ $voter->nama }}
                @if($voter->status === 'belum_aktivasi')
                  <span class="ml-1.5 px-1.5 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">Belum Aktivasi</span>
                @endif
              </td>
              <td class="py-3 px-4">{{ $voter->kelas }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded text-xs font-mono bg-notion-hover text-notion-text border border-notion-border">{{ $voter->tps?->kode_tps ?? 'Belum diatur' }}</span>
              </td>
              <td class="py-3 px-4">
                @if($voter->has_voted)
                  <span class="font-mono text-xs bg-notion-redBg/60 text-notion-red line-through px-2 py-0.5 rounded border border-red-200 tnum" title="Token sudah terpakai">{{ $voter->token }}</span>
                @elseif(auth()->user()?->isAdmin())
                  <div class="inline-flex items-center gap-1.5">
                    <span class="font-mono text-xs bg-notion-hover px-2 py-0.5 rounded border border-notion-border tnum">{{ $voter->token }}</span>
                    <form action="{{ route('voters.reset-token', $voter) }}" method="POST" class="inline">
                      @csrf
                      <button type="submit" class="p-1 text-notion-gray hover:text-notion-blue rounded" title="Acak Ulang Token">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                      </button>
                    </form>
                  </div>
                @else
                  <span class="inline-flex items-center gap-1 font-mono text-xs text-notion-gray bg-notion-sidebar px-2 py-0.5 rounded border border-notion-border" title="Token disembunyikan untuk Panitia sebelum digunakan">
                    <i data-lucide="lock" class="w-3 h-3"></i> ••••••
                  </span>
                @endif
              </td>
              <td class="py-3 px-4">
                @if($voter->has_voted)
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Sudah Memilih</span>
                @else
                  <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-notion-hover text-notion-gray border border-notion-border">Belum Memilih</span>
                @endif
              </td>
              @if(auth()->user()?->isAdmin())
                <td class="py-3 px-4 text-right space-x-1">
                  @php
                    $voterPayload = $voter->only(['id', 'nis', 'nama', 'kelas', 'tps_id', 'status']);
                    $auditPayload = [
                        'nama' => $voter->nama,
                        'nis' => $voter->nis,
                        'kelas' => $voter->kelas,
                        'tps' => $voter->tps?->nama_tps ?? '-',
                        'voted_at' => $voter->voted_at?->format('d/m/Y H:i:s') ?? '-',
                        'receipt_code' => $voter->receipt_code ?? '-',
                        'ip_address' => $voter->ip_address ?? '-',
                        'device_type' => $voter->device_type ?? '-',
                        'os' => $voter->os ?? '-',
                        'browser' => $voter->browser ?? '-',
                        'user_agent' => $voter->user_agent ?? '-',
                    ];
                  @endphp
                  @if($voter->has_voted)
                    <button type="button" onclick='openAuditVoter(@json($auditPayload))' class="p-1.5 text-notion-gray hover:text-emerald-700 hover:bg-emerald-50 rounded" title="Detail Perangkat &amp; Audit">
                      <i data-lucide="monitor-smartphone" class="w-4 h-4"></i>
                    </button>
                  @endif
                  <button type="button" onclick='openEditVoter(@json($voterPayload))' class="p-1.5 text-notion-gray hover:text-notion-blue hover:bg-blue-50 rounded" title="Edit Pemilih">
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                  </button>
                  <button type="button" onclick="openDeleteVoter({{ $voter->id }}, '{{ addslashes($voter->nama) }}')" class="p-1.5 text-notion-gray hover:text-notion-red hover:bg-red-50 rounded" title="Hapus Pemilih">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ auth()->user()?->isAdmin() ? 7 : 6 }}" class="py-8 px-4 text-center text-notion-gray">Data pemilih tidak ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="md:hidden divide-y divide-notion-border">
      @forelse($voters as $idx => $voter)
        <div class="bg-white flex flex-col">
          <div onclick="toggleMobileRow('voter', {{ $idx }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-notion-hover/50">
            <div class="min-w-0 pr-3">
              <p class="font-semibold text-sm text-notion-text truncate">{{ $voter->nama }}</p>
              <p class="text-xs text-notion-gray font-mono tnum">{{ $voter->nis }} · {{ $voter->kelas }} · {{ $voter->tps?->kode_tps ?? '-' }}</p>
            </div>
            <i data-lucide="chevron-down" id="voter-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0"></i>
          </div>
          <div id="voter-detail-{{ $idx }}" class="hidden px-4 pb-4 space-y-2 bg-notion-sidebar/30 border-t border-notion-border pt-3 text-sm">
            <div class="flex justify-between"><span class="text-notion-gray">TPS</span><span class="font-mono font-medium">{{ $voter->tps?->nama_tps ?? '-' }}</span></div>
            <div class="flex justify-between">
              <span class="text-notion-gray">Token</span>
              @if($voter->has_voted)
                <span class="font-mono text-xs text-notion-red line-through">{{ $voter->token }}</span>
              @elseif(auth()->user()?->isAdmin())
                <span class="font-mono font-medium">{{ $voter->token }}</span>
              @else
                <span class="font-mono text-notion-gray">•••••• (Tersembunyi)</span>
              @endif
            </div>
            <div class="flex justify-between"><span class="text-notion-gray">Hak Pilih</span>
              <span class="font-medium {{ $voter->has_voted ? 'text-emerald-700' : 'text-notion-gray' }}">{{ $voter->has_voted ? 'Sudah Memilih' : 'Belum Memilih' }}</span>
            </div>
            @if(auth()->user()?->isAdmin())
              <div class="pt-2 border-t border-notion-border flex justify-end gap-2">
                @if($voter->has_voted)
                  <button type="button" onclick='openAuditVoter(@json($auditPayload))' class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-xs font-medium">Audit</button>
                @endif
                <button type="button" onclick='openEditVoter(@json($voterPayload))' class="px-3 py-1 bg-white border border-notion-border rounded text-xs font-medium">Edit</button>
                <button type="button" onclick="openDeleteVoter({{ $voter->id }}, '{{ addslashes($voter->nama) }}')" class="px-3 py-1 bg-notion-redBg text-notion-red rounded text-xs font-medium">Hapus</button>
              </div>
            @endif
          </div>
        </div>
      @empty
        <div class="p-6 text-center text-sm text-notion-gray">Belum ada data pemilih.</div>
      @endforelse
    </div>

    <div class="px-4 py-3 border-t border-notion-border bg-notion-sidebar/30 flex items-center justify-between gap-3 text-sm">
      <span class="text-xs sm:text-sm text-notion-gray tnum">
        @if($voters->total() > 0)
          Menampilkan <strong class="text-notion-text">{{ $voters->firstItem() }}–{{ $voters->lastItem() }}</strong> dari <strong class="text-notion-text">{{ $voters->total() }}</strong> data
        @else
          Menampilkan 0 data
        @endif
      </span>
      <div class="flex items-center gap-1.5">
        @if($voters->onFirstPage())
          <span class="p-1.5 rounded-md border border-notion-border bg-notion-sidebar text-notion-gray/40 cursor-not-allowed" aria-disabled="true">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
          </span>
        @else
          <a href="{{ $voters->previousPageUrl() }}" class="p-1.5 rounded-md border border-notion-border bg-white text-notion-text hover:bg-notion-hover transition-colors" title="Sebelumnya">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
          </a>
        @endif

        @if($voters->hasMorePages())
          <a href="{{ $voters->nextPageUrl() }}" class="p-1.5 rounded-md border border-notion-border bg-white text-notion-text hover:bg-notion-hover transition-colors" title="Berikutnya">
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
@endsection

@if(auth()->user()?->isAdmin())
@push('modals')

  <div id="create-voter-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('create-voter-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <h3 class="font-bold text-lg">Tambah Pemilih Baru</h3>
        <button type="button" onclick="closeAppModal('create-voter-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form action="{{ route('voters.store') }}" method="POST" class="p-6 overflow-y-auto flex-1 space-y-4">
        @csrf
        <div style="margin-top:0">
          <label class="block text-sm font-medium mb-1">NIS / NIP</label>
          <input type="text" name="nis" required placeholder="Contoh: 202604001" class="w-full border border-notion-border rounded-md px-3 py-2 text-sm font-mono tnum">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
          <input type="text" name="nama" required placeholder="Masukkan nama lengkap pemilih..." class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Kelas / Unit</label>
          <select name="kelas" class="modern-select w-full">
            <option value="X-E1">X-E1</option>
            <option value="X-E2">X-E2</option>
            <option value="XI-F1">XI-F1</option>
            <option value="XI-F2">XI-F2</option>
            <option value="XI-F3">XI-F3</option>
            <option value="XII-A1">XII-A1</option>
            <option value="XII-A2">XII-A2</option>
            <option value="Guru/Staf">Guru/Staf</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">TPS Terdaftar</label>
          <select name="tps_id" class="modern-select w-full">
            @foreach($tpsList as $tps)
              <option value="{{ $tps->id }}">{{ $tps->nama_tps }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Status Aktivasi</label>
          <select name="status" class="modern-select w-full">
            <option value="terverifikasi">Terverifikasi (Siap Memilih)</option>
            <option value="belum_aktivasi">Belum Aktivasi</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeAppModal('create-voter-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <div id="edit-voter-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('edit-voter-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <h3 class="font-bold text-lg">Edit Data Pemilih</h3>
        <button type="button" onclick="closeAppModal('edit-voter-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form id="edit-voter-form" method="POST" class="p-6 overflow-y-auto flex-1 space-y-4">
        @csrf
        @method('PUT')
        <div style="margin-top:0">
          <label class="block text-sm font-medium mb-1">NIS / NIP</label>
          <input type="text" name="nis" id="edit-voter-nis" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm font-mono tnum">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
          <input type="text" name="nama" id="edit-voter-nama" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Kelas / Unit</label>
          <input type="text" name="kelas" id="edit-voter-kelas" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">TPS Terdaftar</label>
          <select name="tps_id" id="edit-voter-tps" class="modern-select w-full">
            @foreach($tpsList as $tps)
              <option value="{{ $tps->id }}">{{ $tps->nama_tps }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Status Aktivasi</label>
          <select name="status" id="edit-voter-status" class="modern-select w-full">
            <option value="terverifikasi">Terverifikasi</option>
            <option value="belum_aktivasi">Belum Aktivasi</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeAppModal('edit-voter-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Perbarui</button>
        </div>
      </form>
    </div>
  </div>

  <div id="import-voter-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('import-voter-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <h3 class="font-bold text-lg">Impor Data Pemilih</h3>
        <button type="button" onclick="closeAppModal('import-voter-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form action="{{ route('voters.import') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto flex-1 space-y-4">
        @csrf
        <div class="p-3 rounded-lg bg-notion-sidebar border border-notion-border space-y-1.5" style="margin-top:0">
          <p class="text-xs text-notion-gray leading-relaxed">
            Unggah file Excel <span class="font-mono text-notion-text">.xlsx</span> dengan urutan kolom: <span class="font-mono text-notion-text">nis, nama, kelas, kode_tps</span>.
          </p>
          <a href="{{ route('voters.template-xlsx') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-notion-blue hover:underline">
            <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh Template Excel (.xlsx)
          </a>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">TPS Default (jika kolom kode_tps kosong)</label>
          <select name="default_tps_id" class="modern-select w-full">
            @foreach($tpsList as $tps)
              <option value="{{ $tps->id }}">{{ $tps->nama_tps }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">File Excel (.xlsx)</label>
          <input type="file" name="file_dpt" accept=".xlsx" required class="modern-file" data-placeholder="Pilih atau seret file .xlsx DPT...">
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeAppModal('import-voter-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Mulai Impor XLSX</button>
        </div>
      </form>
    </div>
  </div>

  <div id="delete-voter-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('delete-voter-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-sm w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="p-6 space-y-4">
        <div class="w-10 h-10 rounded-full bg-notion-redBg text-notion-red flex items-center justify-center">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold text-lg">Hapus Data Pemilih?</h3>
          <p class="text-sm text-notion-gray mt-1">Pemilih <span id="delete-voter-label" class="font-semibold text-notion-text"></span> akan dihapus dari Daftar Pemilih Tetap.</p>
        </div>
        <form id="delete-voter-form" method="POST" class="flex justify-end gap-2 pt-2">
          @csrf
          @method('DELETE')
          <button type="button" onclick="closeAppModal('delete-voter-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-red text-white text-sm font-medium rounded-md hover:bg-red-700">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>

  <div id="audit-voter-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('audit-voter-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
          <i data-lucide="monitor-smartphone" class="w-5 h-5 text-notion-blue"></i>
          <h3 class="font-bold text-lg">Audit Pemilih</h3>
        </div>
        <button type="button" onclick="closeAppModal('audit-voter-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="p-6 overflow-y-auto flex-1 space-y-4 text-sm">
        <div class="p-3.5 rounded-xl bg-notion-sidebar/60 border border-notion-border flex items-center justify-between gap-3">
          <div class="min-w-0">
            <p id="audit-nama" class="font-bold text-notion-text truncate"></p>
            <p id="audit-meta" class="text-xs font-mono text-notion-gray tnum"></p>
          </div>
          <span id="audit-receipt" class="px-2.5 py-0.5 rounded bg-blue-50 text-notion-blue border border-blue-200 font-mono text-xs font-semibold shrink-0"></span>
        </div>

        <div class="border border-notion-border rounded-xl divide-y divide-notion-border bg-white">
          <div class="px-4 py-2.5 flex justify-between gap-3"><span class="text-notion-gray">TPS</span><span id="audit-tps" class="font-medium text-right"></span></div>
          <div class="px-4 py-2.5 flex justify-between gap-3"><span class="text-notion-gray">Waktu Memilih</span><span id="audit-time" class="font-mono text-xs tnum text-right"></span></div>
          <div class="px-4 py-2.5 flex justify-between gap-3"><span class="text-notion-gray">Alamat IP</span><span id="audit-ip" class="font-mono font-semibold text-notion-blue text-right"></span></div>
          <div class="px-4 py-2.5 flex justify-between gap-3"><span class="text-notion-gray">Perangkat</span><span id="audit-device" class="font-medium text-right"></span></div>
          <div class="px-4 py-2.5 flex justify-between gap-3"><span class="text-notion-gray">Sistem Operasi</span><span id="audit-os" class="font-medium text-right"></span></div>
          <div class="px-4 py-2.5 flex justify-between gap-3"><span class="text-notion-gray">Browser</span><span id="audit-browser" class="font-medium text-right"></span></div>
        </div>

        <div class="space-y-1">
          <p class="text-xs font-medium text-notion-gray">User-Agent Lengkap</p>
          <p id="audit-ua" class="p-3 rounded-lg bg-notion-sidebar border border-notion-border font-mono text-xs text-notion-text break-all"></p>
        </div>

        <div class="flex justify-end pt-1">
          <button type="button" onclick="closeAppModal('audit-voter-modal')" class="px-4 py-2 bg-notion-text text-white text-sm font-medium rounded-md hover:bg-black">Tutup</button>
        </div>
      </div>
    </div>
  </div>
@endpush
@endif

@push('scripts')
<script>
  function togglePrintDropdown(e) {
    e.stopPropagation();
    document.getElementById('print-dropdown-menu')?.classList.toggle('hidden');
  }
  document.addEventListener('click', (e) => {
    const wrap = document.getElementById('print-dropdown-wrap');
    if (wrap && !wrap.contains(e.target)) {
      document.getElementById('print-dropdown-menu')?.classList.add('hidden');
    }
  });
</script>
@if(auth()->user()?->isAdmin())
<script>
  function openEditVoter(voter) {
    const form = document.getElementById('edit-voter-form');
    form.action = `/pemilih/${voter.id}`;
    document.getElementById('edit-voter-nis').value = voter.nis;
    document.getElementById('edit-voter-nama').value = voter.nama;
    document.getElementById('edit-voter-kelas').value = voter.kelas;
    const tpsSel = document.getElementById('edit-voter-tps');
    if (tpsSel && voter.tps_id) {
      tpsSel.value = voter.tps_id;
      tpsSel.syncModernLabel?.();
    }
    const statusSel = document.getElementById('edit-voter-status');
    statusSel.value = voter.status;
    statusSel.syncModernLabel?.();
    openAppModal('edit-voter-modal');
  }

  function openDeleteVoter(id, nama) {
    document.getElementById('delete-voter-form').action = `/pemilih/${id}`;
    document.getElementById('delete-voter-label').textContent = nama;
    openAppModal('delete-voter-modal');
  }

  function openAuditVoter(data) {
    document.getElementById('audit-nama').textContent = data.nama;
    document.getElementById('audit-meta').textContent = `${data.nis} · ${data.kelas}`;
    document.getElementById('audit-receipt').textContent = data.receipt_code;
    document.getElementById('audit-tps').textContent = data.tps;
    document.getElementById('audit-time').textContent = data.voted_at;
    document.getElementById('audit-ip').textContent = data.ip_address;
    document.getElementById('audit-device').textContent = data.device_type;
    document.getElementById('audit-os').textContent = data.os;
    document.getElementById('audit-browser').textContent = data.browser;
    document.getElementById('audit-ua').textContent = data.user_agent;
    openAppModal('audit-voter-modal');
  }
</script>
@endif
@endpush
