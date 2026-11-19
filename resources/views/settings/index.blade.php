@extends('layouts.app')

@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan')

@section('content')

  <div class="space-y-2">
    <i data-lucide="settings" class="w-12 h-12 text-notion-text/80"></i>
    <h1 class="text-3xl font-bold tracking-tight">Pengaturan</h1>
    <p class="text-sm text-notion-gray">
      Konfigurasi identitas sekolah, logo kegiatan, jadwal pemungutan suara, serta kebijakan keamanan bilik suara.
    </p>
  </div>

  <div class="border border-notion-border rounded-lg bg-white overflow-hidden shadow-sm">
    <div class="border-b border-notion-border flex flex-nowrap gap-1 overflow-x-auto whitespace-nowrap px-4 pt-2 bg-notion-sidebar/30">
      <button type="button" onclick="switchSettingsTab(this, 'set-tab-1')" data-tab-group="settings"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-notion-blue text-notion-blue flex items-center gap-2">
        <i data-lucide="building-2" class="w-4 h-4"></i> Profil
      </button>
      <button type="button" onclick="switchSettingsTab(this, 'set-tab-2')" data-tab-group="settings"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="calendar-range" class="w-4 h-4"></i> Jadwal
      </button>
      <button type="button" onclick="switchSettingsTab(this, 'set-tab-3')" data-tab-group="settings"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="shield-check" class="w-4 h-4"></i> Bilik
      </button>
      <button type="button" onclick="switchSettingsTab(this, 'set-tab-4')" data-tab-group="settings"
              class="app-tab shrink-0 whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-notion-gray hover:text-notion-text flex items-center gap-2">
        <i data-lucide="database" class="w-4 h-4"></i> Reset Data
      </button>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
      @csrf
      @method('PUT')

      <div id="set-tab-1" data-tab-group="settings" class="app-tab-panel grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nama Instansi</label>
            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $setting->nama_sekolah) }}" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm bg-white">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Nama Kegiatan</label>
            <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $setting->nama_kegiatan) }}" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm bg-white">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Tahun Periode</label>
            <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $setting->tahun_ajaran) }}" required class="w-full border border-notion-border rounded-md px-3 py-2 text-sm font-mono tnum bg-white">
          </div>
        </div>

        <div class="space-y-4">
          <label class="block text-sm font-medium mb-1.5">Logo</label>
          <div class="mb-2.5 flex items-center justify-between gap-3 p-3 rounded-xl bg-notion-sidebar/50 border border-notion-border">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-12 h-12 flex items-center justify-center shrink-0 {{ $setting->logo_path ? '' : 'rounded-lg bg-white border border-notion-border' }}">
                @if($setting->logo_path)
                  <img src="{{ asset('uploads/' . $setting->logo_path) }}" alt="Logo OSIS" class="w-full h-full object-contain">
                @else
                  <i data-lucide="image" class="w-5 h-5 text-notion-gray"></i>
                @endif
              </div>
              <div class="min-w-0">
                <p class="text-sm font-semibold text-notion-text truncate">{{ $setting->logo_path ? 'Logo Aktif' : 'Belum Ada Logo' }}</p>
                <p class="text-xs text-notion-gray truncate">Tampil pada sidebar &amp; kop berita acara.</p>
              </div>
            </div>
            @if($setting->logo_path)
              <button type="button" onclick="document.getElementById('remove-logo-form').submit()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-red-200 bg-white text-notion-red hover:bg-notion-red hover:text-white transition-all shrink-0">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
              </button>
            @endif
          </div>
          <input type="file" name="logo" accept="image/*" class="modern-file" data-placeholder="Pilih atau seret file logo (PNG/JPG)...">
        </div>
      </div>

      <div id="set-tab-2" data-tab-group="settings" class="app-tab-panel hidden grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
          <label class="block text-sm font-medium mb-1">Status Pelaksanaan</label>
          <select name="status_pemilihan" class="modern-select w-full">
            <option value="persiapan" @selected($setting->status_pemilihan === 'persiapan')>Persiapan</option>
            <option value="berlangsung" @selected($setting->status_pemilihan === 'berlangsung')>Sedang Berlangsung</option>
            <option value="selesai" @selected($setting->status_pemilihan === 'selesai')>Selesai</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Waktu Mulai</label>
          <input type="hidden" name="jadwal_mulai" id="jadwal_mulai" value="{{ old('jadwal_mulai', $setting->jadwal_mulai) }}" required>
          <div class="modern-dtp" data-target="jadwal_mulai"></div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Waktu Selesai</label>
          <input type="hidden" name="jadwal_selesai" id="jadwal_selesai" value="{{ old('jadwal_selesai', $setting->jadwal_selesai) }}" required>
          <div class="modern-dtp" data-target="jadwal_selesai"></div>
        </div>
      </div>

      <div id="set-tab-3" data-tab-group="settings" class="app-tab-panel hidden grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium mb-1.5">Wajibkan Token Bilik Suara saat Voting</label>
          <div class="grid grid-cols-2 gap-2.5">
            <label class="cursor-pointer">
              <input type="radio" name="require_token" value="1" @checked($setting->require_token) class="peer sr-only">
              <span class="w-full py-2 px-3 rounded-md border border-notion-border bg-white text-sm font-medium flex items-center justify-center gap-2 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 transition-all">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Wajib Token
              </span>
            </label>
            <label class="cursor-pointer">
              <input type="radio" name="require_token" value="0" @checked(!$setting->require_token) class="peer sr-only">
              <span class="w-full py-2 px-3 rounded-md border border-notion-border bg-white text-sm font-medium flex items-center justify-center gap-2 peer-checked:bg-notion-redBg peer-checked:border-notion-red peer-checked:text-notion-red transition-all">
                <i data-lucide="x-circle" class="w-4 h-4"></i> Tanpa Token
              </span>
            </label>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1.5">Izinkan Kotak Kosong / Abstain</label>
          <div class="grid grid-cols-2 gap-2.5">
            <label class="cursor-pointer">
              <input type="radio" name="allow_abstain" value="1" @checked($setting->allow_abstain) class="peer sr-only">
              <span class="w-full py-2 px-3 rounded-md border border-notion-border bg-white text-sm font-medium flex items-center justify-center gap-2 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 transition-all">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Izinkan
              </span>
            </label>
            <label class="cursor-pointer">
              <input type="radio" name="allow_abstain" value="0" @checked(!$setting->allow_abstain) class="peer sr-only">
              <span class="w-full py-2 px-3 rounded-md border border-notion-border bg-white text-sm font-medium flex items-center justify-center gap-2 peer-checked:bg-notion-redBg peer-checked:border-notion-red peer-checked:text-notion-red transition-all">
                <i data-lucide="x-circle" class="w-4 h-4"></i> Tidak
              </span>
            </label>
          </div>
        </div>
      </div>

      <div id="settings-save-footer" class="pt-4 border-t border-notion-border flex justify-end">
        <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover transition-colors inline-flex items-center gap-2">
          <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
        </button>
      </div>
    </form>

    <div id="set-tab-4" data-tab-group="settings" class="app-tab-panel hidden p-6 pt-0 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="border border-notion-border rounded-xl p-5 bg-notion-sidebar/40 flex flex-col justify-between gap-4">
          <div class="space-y-1.5">
            <div class="flex items-center gap-2 text-amber-700 font-semibold text-sm">
              <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset Status Pemilih
            </div>
            <p class="text-xs text-notion-gray leading-relaxed">
              Mengembalikan status seluruh pemilih menjadi <strong>Belum Memilih</strong>, menghapus kode bukti, dan mengosongkan seluruh suara masuk tanpa menghapus data siswa (DPT).
            </p>
          </div>
          <button type="button" onclick="openAppModal('reset-votes-modal')" class="w-full sm:w-auto self-start px-4 py-2 bg-white border border-amber-300 text-amber-800 text-sm font-medium rounded-md hover:bg-amber-50 transition-colors inline-flex items-center gap-1.5">
            <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset Status Pemilih
          </button>
        </div>

        <div class="border border-red-200 rounded-xl p-5 bg-notion-redBg/30 flex flex-col justify-between gap-4">
          <div class="space-y-1.5">
            <div class="flex items-center gap-2 text-notion-red font-semibold text-sm">
              <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Semua Data Pemilih (DPT)
            </div>
            <p class="text-xs text-notion-gray leading-relaxed">
              Menghapus seluruh daftar pemilih (DPT) dan suara masuk secara permanen. Gunakan fitur ini saat akan mengimpor data pemilih periode baru.
            </p>
          </div>
          <button type="button" onclick="openAppModal('clear-voters-modal')" class="w-full sm:w-auto self-start px-4 py-2 bg-notion-red text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors inline-flex items-center gap-1.5">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Semua Pemilih
          </button>
        </div>
      </div>
    </div>
  </div>

  <form id="remove-logo-form" action="{{ route('settings.remove-logo') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>
@endsection

@push('modals')

  <div id="reset-votes-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('reset-votes-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-sm w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="p-6 space-y-4">
        <div class="w-10 h-10 rounded-full bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center">
          <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold text-lg">Reset Status Pemilih?</h3>
          <p class="text-sm text-notion-gray mt-1">Seluruh perolehan suara akan dikosongkan dan semua pemilih dapat memilih ulang.</p>
        </div>
        <form action="{{ route('settings.reset-votes') }}" method="POST" class="flex justify-end gap-2 pt-2">
          @csrf
          <button type="button" onclick="closeAppModal('reset-votes-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-md hover:bg-amber-700">Ya, Reset Status</button>
        </form>
      </div>
    </div>
  </div>

  <div id="clear-voters-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('clear-voters-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-sm w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="p-6 space-y-4">
        <div class="w-10 h-10 rounded-full bg-notion-redBg text-notion-red flex items-center justify-center">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold text-lg">Hapus Semua Data Pemilih?</h3>
          <p class="text-sm text-notion-gray mt-1">Seluruh data DPT dan suara masuk akan dihapus permanen dan tidak dapat dikembalikan.</p>
        </div>
        <form action="{{ route('settings.clear-voters') }}" method="POST" class="flex justify-end gap-2 pt-2">
          @csrf
          @method('DELETE')
          <button type="button" onclick="closeAppModal('clear-voters-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 bg-notion-red text-white text-sm font-medium rounded-md hover:bg-red-700">Ya, Hapus Semua</button>
        </form>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
  function switchSettingsTab(btn, panelId) {
    switchAppTab(btn, panelId, 'settings');
    const footer = document.getElementById('settings-save-footer');
    if (footer) {
      footer.classList.toggle('hidden', panelId === 'set-tab-4');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    document.querySelectorAll('.modern-dtp').forEach((host) => {
      const hiddenInput = document.getElementById(host.dataset.target);
      if (!hiddenInput) return;

      const parseVal = (val) => {
        const m = (val || '').match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})/);
        if (m) return new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5]);
        return new Date();
      };

      let selected = parseVal(hiddenInput.value);
      let viewYear = selected.getFullYear();
      let viewMonth = selected.getMonth();

      host.className = 'relative w-full';

      const trigger = document.createElement('button');
      trigger.type = 'button';
      trigger.className = 'w-full flex items-center justify-between border border-notion-border rounded-md px-3 py-2 text-sm bg-white hover:bg-notion-sidebar transition-colors text-left';

      const leftWrap = document.createElement('span');
      leftWrap.className = 'flex items-center gap-2 min-w-0';
      const calIcon = document.createElement('i');
      calIcon.setAttribute('data-lucide', 'calendar-clock');
      calIcon.className = 'w-4 h-4 text-notion-gray shrink-0';
      const labelSpan = document.createElement('span');
      labelSpan.className = 'truncate font-medium text-notion-text tnum';
      leftWrap.append(calIcon, labelSpan);

      const chev = document.createElement('i');
      chev.setAttribute('data-lucide', 'chevron-down');
      chev.className = 'w-4 h-4 text-notion-gray shrink-0 ml-2 transition-transform duration-150';
      trigger.append(leftWrap, chev);

      const menu = document.createElement('div');
      menu.className = 'hidden modern-dtp-menu fixed z-[150] w-[300px] bg-white border border-notion-border rounded-xl shadow-xl p-3 space-y-3';

      const pad = (n) => String(n).padStart(2, '0');
      const syncValue = () => {
        const y = selected.getFullYear();
        const m = pad(selected.getMonth() + 1);
        const d = pad(selected.getDate());
        const h = pad(selected.getHours());
        const min = pad(selected.getMinutes());
        hiddenInput.value = `${y}-${m}-${d} ${h}:${min}`;
        labelSpan.textContent = `${d} ${shortMonths[selected.getMonth()]} ${y} · ${h}:${min}`;
      };

      const renderMenu = () => {
        const firstDay = new Date(viewYear, viewMonth, 1);
        const startOffset = (firstDay.getDay() + 6) % 7;
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        let cellsHtml = '';
        for (let i = 0; i < startOffset; i++) {
          cellsHtml += '<span></span>';
        }
        for (let d = 1; d <= daysInMonth; d++) {
          const isSelected = selected.getFullYear() === viewYear && selected.getMonth() === viewMonth && selected.getDate() === d;
          cellsHtml += `
            <button type="button" data-day="${d}" class="dtp-day h-8 rounded-md text-xs font-medium tnum transition-colors ${isSelected ? 'bg-notion-blue text-white font-semibold' : 'text-notion-text hover:bg-notion-hover'}">
              ${d}
            </button>
          `;
        }

        menu.innerHTML = `
          <div class="flex items-center justify-between px-1">
            <button type="button" class="dtp-prev p-1.5 rounded-md hover:bg-notion-hover text-notion-gray hover:text-notion-text">
              <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <span class="text-sm font-semibold text-notion-text">${months[viewMonth]} ${viewYear}</span>
            <button type="button" class="dtp-next p-1.5 rounded-md hover:bg-notion-hover text-notion-gray hover:text-notion-text">
              <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
          </div>

          <div class="grid grid-cols-7 gap-1 text-center">
            ${days.map(dn => `<span class="text-[11px] font-medium text-notion-gray py-1">${dn}</span>`).join('')}
            ${cellsHtml}
          </div>

          <div class="pt-2.5 border-t border-notion-border flex items-center justify-between gap-2">
            <div class="flex items-center gap-1.5">
              <i data-lucide="clock" class="w-3.5 h-3.5 text-notion-gray"></i>
              <input type="number" min="0" max="23" value="${pad(selected.getHours())}" class="dtp-hour w-12 border border-notion-border rounded-md px-1.5 py-1 text-xs font-mono text-center tnum bg-notion-sidebar">
              <span class="text-xs font-bold text-notion-gray">:</span>
              <input type="number" min="0" max="59" value="${pad(selected.getMinutes())}" class="dtp-min w-12 border border-notion-border rounded-md px-1.5 py-1 text-xs font-mono text-center tnum bg-notion-sidebar">
            </div>
            <button type="button" class="dtp-done px-3 py-1.5 bg-notion-text text-white text-xs font-medium rounded-md hover:bg-black transition-colors">
              Terapkan
            </button>
          </div>
        `;

        menu.querySelector('.dtp-prev').addEventListener('click', (e) => {
          e.stopPropagation();
          viewMonth--;
          if (viewMonth < 0) { viewMonth = 11; viewYear--; }
          renderMenu();
        });
        menu.querySelector('.dtp-next').addEventListener('click', (e) => {
          e.stopPropagation();
          viewMonth++;
          if (viewMonth > 11) { viewMonth = 0; viewYear++; }
          renderMenu();
        });
        menu.querySelectorAll('.dtp-day').forEach((btn) => {
          btn.addEventListener('click', (e) => {
            e.stopPropagation();
            selected.setFullYear(viewYear, viewMonth, +btn.dataset.day);
            syncValue();
            renderMenu();
          });
        });

        const hourInput = menu.querySelector('.dtp-hour');
        const minInput = menu.querySelector('.dtp-min');
        [hourInput, minInput].forEach((inp) => inp.addEventListener('click', (e) => e.stopPropagation()));
        hourInput.addEventListener('change', () => {
          const v = Math.max(0, Math.min(23, parseInt(hourInput.value || '0', 10)));
          selected.setHours(v);
          hourInput.value = pad(v);
          syncValue();
        });
        minInput.addEventListener('change', () => {
          const v = Math.max(0, Math.min(59, parseInt(minInput.value || '0', 10)));
          selected.setMinutes(v);
          minInput.value = pad(v);
          syncValue();
        });
        menu.querySelector('.dtp-done').addEventListener('click', (e) => {
          e.stopPropagation();
          menu.classList.add('hidden');
          chev.classList.remove('rotate-180');
        });

        lucide.createIcons();
      };

      trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        document.querySelectorAll('.modern-dtp-menu, .modern-select-menu').forEach((m) => {
          if (m !== menu) m.classList.add('hidden');
        });
        const willOpen = menu.classList.contains('hidden');
        if (willOpen) {
          renderMenu();
          const rect = trigger.getBoundingClientRect();
          menu.style.left = `${Math.min(rect.left, window.innerWidth - 316)}px`;
          menu.classList.remove('hidden');
          const menuH = menu.offsetHeight || 320;
          if (window.innerHeight - rect.bottom < menuH + 12 && rect.top > menuH + 12) {
            menu.style.top = `${rect.top - menuH - 4}px`;
          } else {
            menu.style.top = `${rect.bottom + 4}px`;
          }
        } else {
          menu.classList.add('hidden');
        }
        chev.classList.toggle('rotate-180', !menu.classList.contains('hidden'));
      });

      menu.addEventListener('click', (e) => e.stopPropagation());
      document.addEventListener('click', () => {
        menu.classList.add('hidden');
        chev.classList.remove('rotate-180');
      });

      syncValue();
      host.appendChild(trigger);
      document.body.appendChild(menu);
    });

    lucide.createIcons();
  });
</script>
@endpush
