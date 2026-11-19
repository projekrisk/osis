@extends('layouts.app')

@section('title', $isEdit ? 'Edit Pasangan Calon' : 'Tambah Pasangan Calon')
@section('breadcrumb', $isEdit ? 'Edit Kandidat Paslon' : 'Tambah Kandidat Paslon')

@section('content')

  <div class="space-y-3">
    <a href="{{ route('candidates.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-notion-gray hover:text-notion-text transition-colors">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
    <div class="space-y-2">
      <i data-lucide="award" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">{{ $isEdit ? 'Edit Pasangan Calon' : 'Tambah Pasangan Calon' }}</h1>
      <p class="text-sm text-notion-gray">
        Lengkapi biodata pasangan calon, visi &amp; misi serta foto profil resmi.
      </p>
    </div>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">

    <div class="border-b border-notion-border flex flex-nowrap gap-1 overflow-x-auto whitespace-nowrap px-4 pt-2 bg-notion-sidebar/30">
      <button type="button" onclick="switchAppTab(this, 'cand-tab-1', 'cand-form')" data-tab-group="cand-form"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-notion-blue text-notion-blue flex items-center gap-2">
        <i data-lucide="users" class="w-4 h-4"></i> Kandidat
      </button>
      <button type="button" onclick="switchAppTab(this, 'cand-tab-2', 'cand-form')" data-tab-group="cand-form"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="file-edit" class="w-4 h-4"></i> Visi &amp; Misi
      </button>
      <button type="button" onclick="switchAppTab(this, 'cand-tab-3', 'cand-form')" data-tab-group="cand-form"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="image" class="w-4 h-4"></i> Foto Paslon
      </button>
    </div>

    <form id="candidate-main-form"
          action="{{ $isEdit ? route('candidates.update', $candidate) : route('candidates.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="p-6 space-y-6">
      @csrf
      @if($isEdit)
        @method('PUT')
      @endif

      <div id="cand-tab-1" data-tab-group="cand-form" class="app-tab-panel space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nomor Urut Paslon</label>
            <input type="number" name="nomor_urut" min="1" max="99" value="{{ old('nomor_urut', $candidate->nomor_urut) }}" required placeholder="Contoh: 1"
                   class="w-full border border-notion-border rounded-md px-3 py-2 text-sm font-mono tnum bg-white">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Kode Paslon</label>
            <input type="text" name="kode_paslon" value="{{ old('kode_paslon', $candidate->kode_paslon) }}" required placeholder="Contoh: PASLON-01"
                   class="w-full border border-notion-border rounded-md px-3 py-2 text-sm font-mono bg-white">
          </div>
        </div>

        <div class="p-4 rounded-xl bg-notion-sidebar/40 border border-notion-border space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-notion-blue">Calon Ketua OSIS</p>
            <span class="text-xs text-notion-gray">Cari dari DPT</span>
          </div>
          <div class="relative">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
            <input type="text" id="search-dpt-ketua" autocomplete="off" placeholder="Ketik nama calon ketua..."
                   class="w-full border border-notion-border rounded-md pl-9 pr-3 py-2 text-sm bg-white">
            <div id="results-dpt-ketua" class="hidden absolute z-40 left-0 right-0 top-full mt-1 bg-white border border-notion-border rounded-lg shadow-lg max-h-56 overflow-y-auto divide-y divide-notion-border"></div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium mb-1">Nama Lengkap Calon Ketua</label>
              <input type="text" name="ketua_nama" id="ketua-nama-input" value="{{ old('ketua_nama', $candidate->ketua_nama) }}" required readonly placeholder="Terisi otomatis..."
                     class="w-full border border-notion-border rounded-md px-3 py-2 text-sm bg-notion-sidebar/60">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Kelas</label>
              <input type="text" name="ketua_kelas" id="ketua-kelas-input" value="{{ old('ketua_kelas', $candidate->ketua_kelas) }}" required readonly placeholder="-"
                     class="w-full border border-notion-border rounded-md px-3 py-2 text-sm bg-notion-sidebar/60">
            </div>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-notion-sidebar/40 border border-notion-border space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-notion-gray">Calon Wakil Ketua OSIS</p>
            <span class="text-xs text-notion-gray">Cari dari DPT</span>
          </div>
          <div class="relative">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
            <input type="text" id="search-dpt-wakil" autocomplete="off" placeholder="Ketik nama calon wakil..."
                   class="w-full border border-notion-border rounded-md pl-9 pr-3 py-2 text-sm bg-white">
            <div id="results-dpt-wakil" class="hidden absolute z-40 left-0 right-0 top-full mt-1 bg-white border border-notion-border rounded-lg shadow-lg max-h-56 overflow-y-auto divide-y divide-notion-border"></div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium mb-1">Nama Lengkap Calon Wakil</label>
              <input type="text" name="wakil_nama" id="wakil-nama-input" value="{{ old('wakil_nama', $candidate->wakil_nama) }}" required readonly placeholder="Terisi otomatis..."
                     class="w-full border border-notion-border rounded-md px-3 py-2 text-sm bg-notion-sidebar/60">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Kelas</label>
              <input type="text" name="wakil_kelas" id="wakil-kelas-input" value="{{ old('wakil_kelas', $candidate->wakil_kelas) }}" required readonly placeholder="-"
                     class="w-full border border-notion-border rounded-md px-3 py-2 text-sm bg-notion-sidebar/60">
            </div>
          </div>
        </div>
      </div>

      <div id="cand-tab-2" data-tab-group="cand-form" class="app-tab-panel hidden space-y-6">
        <div>
          <label class="block text-sm font-medium mb-1.5">Visi Pasangan Calon</label>
          <p class="text-xs text-notion-gray mb-2">Gunakan tombol format di bawah untuk menebalkan atau merapikan teks visi.</p>
          <div class="border border-notion-border rounded-lg overflow-hidden bg-white">
            <div id="visi-editor" class="min-h-[110px] text-sm"></div>
          </div>
          <textarea name="visi" id="visi-hidden" class="hidden">{{ old('visi', $candidate->visi) }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1.5">Misi &amp; Program Kerja</label>
          <p class="text-xs text-notion-gray mb-2">Gunakan ikon daftar bernomor (<span class="font-mono">1, 2, 3</span>) atau bullet (<span class="font-mono">•</span>) pada toolbar editor agar poin misi rapi.</p>
          <div class="border border-notion-border rounded-lg overflow-hidden bg-white">
            <div id="misi-editor" class="min-h-[180px] text-sm"></div>
          </div>
          <textarea name="misi" id="misi-hidden" class="hidden">{{ old('misi', $candidate->misi) }}</textarea>
        </div>
      </div>

      <div id="cand-tab-3" data-tab-group="cand-form" class="app-tab-panel hidden space-y-5 max-w-2xl">
        <div>
          <label class="block text-sm font-medium mb-1.5">Status Kandidat</label>
          <select name="status" class="modern-select w-full">
            <option value="aktif" @selected(old('status', $candidate->status) === 'aktif')>Aktif (Tampil di Bilik &amp; Beranda)</option>
            <option value="verifikasi" @selected(old('status', $candidate->status) === 'verifikasi')>Perlu Verifikasi</option>
            <option value="draft" @selected(old('status', $candidate->status) === 'draft')>Draft (Disembunyikan)</option>
          </select>
          <p class="text-xs text-notion-gray mt-1.5">
            Pastikan status disetel ke <strong>Aktif</strong> agar pasangan calon tampil pada halaman utama publik dan bilik suara peserta.
          </p>
        </div>

        <div class="pt-4 border-t border-notion-border space-y-3">
          <label class="block text-sm font-medium">Foto Pasangan Calon</label>
          @if($candidate->foto_path)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-notion-sidebar/50 border border-notion-border">
              <img src="{{ asset('uploads/' . $candidate->foto_path) }}" alt="{{ $candidate->kode_paslon }}" class="w-16 h-16 rounded-lg object-cover border border-notion-border shrink-0">
              <div class="min-w-0">
                <p class="text-sm font-semibold text-notion-text">Foto Saat Ini</p>
                <p class="text-xs text-notion-gray truncate">Unggah file baru di bawah jika ingin mengganti.</p>
              </div>
            </div>
          @endif
          <input type="file" name="foto" accept="image/*" class="modern-file" data-placeholder="Pilih atau seret foto paslon (JPG/PNG)...">
        </div>
      </div>

      <div class="pt-5 border-t border-notion-border flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <a href="{{ route('candidates.index') }}" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md text-center">Batal</a>
        <button type="submit" class="px-5 py-2.5 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover transition-colors inline-flex items-center justify-center gap-2">
          <i data-lucide="save" class="w-4 h-4"></i> {{ $isEdit ? 'Perbarui' : 'Simpan' }}
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<style>
  .ql-toolbar.ql-snow { border: none !important; border-bottom: 1px solid #e5e5e5 !important; background: #f7f7f5; font-family: "Bricolage Grotesque", sans-serif; }
  .ql-container.ql-snow { border: none !important; font-family: "Bricolage Grotesque", sans-serif; font-size: 14px; }
  .ql-editor { min-height: 120px; }
</style>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const toolbarOptions = [
      ['bold', 'italic', 'underline'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      ['clean']
    ];

    const visiQuill = new Quill('#visi-editor', {
      theme: 'snow',
      placeholder: 'Tuliskan visi pasangan calon...',
      modules: { toolbar: toolbarOptions }
    });

    const misiQuill = new Quill('#misi-editor', {
      theme: 'snow',
      placeholder: 'Tuliskan poin-poin misi pasangan calon...',
      modules: { toolbar: toolbarOptions }
    });

    const initialVisi = document.getElementById('visi-hidden').value || '';
    const initialMisi = document.getElementById('misi-hidden').value || '';

    if (initialVisi.includes('<')) {
      visiQuill.clipboard.dangerouslyPasteHTML(initialVisi);
    } else if (initialVisi.trim()) {
      visiQuill.setText(initialVisi);
    }

    if (initialMisi.includes('<')) {
      misiQuill.clipboard.dangerouslyPasteHTML(initialMisi);
    } else if (initialMisi.trim()) {
      misiQuill.setText(initialMisi);
    }

    const form = document.getElementById('candidate-main-form');
    form.addEventListener('submit', (e) => {
      const visiText = visiQuill.getText().trim();
      const misiText = misiQuill.getText().trim();

      if (!visiText || !misiText) {
        e.preventDefault();
        const tab2 = document.querySelectorAll('.app-tab[data-tab-group="cand-form"]')[1];
        if (tab2) switchAppTab(tab2, 'cand-tab-2', 'cand-form');
        showToast('Mohon lengkapi Visi dan Misi pasangan calon pada tab Visi & Misi.', 'error');
        return;
      }

      document.getElementById('visi-hidden').value = visiQuill.getSemanticHTML();
      document.getElementById('misi-hidden').value = misiQuill.getSemanticHTML();
    });

    function setupDptSearch(inputId, resultsId, namaTargetId, kelasTargetId) {
      const input = document.getElementById(inputId);
      const box = document.getElementById(resultsId);
      if (!input || !box) return;

      let timer = null;
      input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) {
          box.classList.add('hidden');
          box.innerHTML = '';
          return;
        }
        timer = setTimeout(async () => {
          try {
            const res = await fetch(`{{ route('candidates.search-dpt') }}?q=${encodeURIComponent(q)}`, {
              headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const list = await res.json();
            if (list.length === 0) {
              box.innerHTML = '<div class="p-3 text-xs text-notion-gray">Siswa tidak ditemukan di DPT.</div>';
            } else {
              box.innerHTML = list.map((v) => `
                <button type="button" data-nama="${v.nama.replace(/"/g, '&quot;')}" data-kelas="${v.kelas.replace(/"/g, '&quot;')}"
                        class="w-full text-left px-3.5 py-2.5 hover:bg-notion-hover flex items-center justify-between gap-2 text-sm">
                  <span class="font-medium text-notion-text truncate">${v.nama}</span>
                  <span class="font-mono text-xs text-notion-gray shrink-0">${v.nis} · ${v.kelas}</span>
                </button>
              `).join('');

              box.querySelectorAll('button').forEach((btn) => {
                btn.addEventListener('click', () => {
                  document.getElementById(namaTargetId).value = btn.dataset.nama;
                  document.getElementById(kelasTargetId).value = btn.dataset.kelas;
                  input.value = `${btn.dataset.nama} (${btn.dataset.kelas})`;
                  box.classList.add('hidden');
                });
              });
            }
            box.classList.remove('hidden');
          } catch (e) {}
        }, 220);
      });

      document.addEventListener('click', (e) => {
        if (!box.contains(e.target) && e.target !== input) {
          box.classList.add('hidden');
        }
      });
    }

    setupDptSearch('search-dpt-ketua', 'results-dpt-ketua', 'ketua-nama-input', 'ketua-kelas-input');
    setupDptSearch('search-dpt-wakil', 'results-dpt-wakil', 'wakil-nama-input', 'wakil-kelas-input');
  });
</script>
@endpush
