<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Kartu Pemilih OSIS — {{ $setting->nama_sekolah }}</title>
  @php $faviconUrl = ($faviconLogo = ($setting ?? \App\Models\ElectionSetting::current())->logo_path) ? asset('uploads/' . $faviconLogo) : asset('favicon.ico'); @endphp
  <link rel="icon" href="{{ $faviconUrl }}">
  <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Bricolage Grotesque"', 'sans-serif'],
            mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace'],
          },
          colors: {
            notion: { bg: '#ffffff', sidebar: '#f7f7f5', hover: '#efefed', text: '#222222', gray: '#666666', border: '#e5e5e5', blue: '#2563eb' }
          }
        }
      }
    }
  </script>
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #ffffff !important; }
      .print-card { break-inside: avoid; page-break-inside: avoid; }
    }
  </style>
</head>
<body class="bg-notion-sidebar text-notion-text antialiased">

  <div class="no-print sticky top-0 z-20 bg-white border-b border-notion-border px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <a href="{{ route('voters.index') }}" class="px-3 py-1.5 rounded-md border border-notion-border text-sm font-medium hover:bg-notion-sidebar inline-flex items-center gap-1.5">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke DPT
      </a>
      <span class="text-sm font-semibold">Cetak Kartu Pemilih ({{ $voters->count() }} kartu)</span>
    </div>

    <form method="GET" action="{{ route('voters.print-cards') }}" class="flex flex-wrap items-center gap-2">
      <div class="w-52">
        <select name="tps_id" class="modern-select w-full">
          <option value="">Semua TPS</option>
          @foreach($tpsList as $tps)
            <option value="{{ $tps->id }}" @selected($tpsId == $tps->id)>{{ $tps->nama_tps }}</option>
          @endforeach
        </select>
      </div>
      <div class="w-40">
        <select name="kelas" class="modern-select w-full">
          <option value="">Semua Kelas</option>
          @foreach($kelasList as $k)
            <option value="{{ $k }}" @selected($kelas === $k)>{{ $k }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="px-3.5 py-2 bg-white border border-notion-border rounded-md text-sm font-medium hover:bg-notion-sidebar">Terapkan</button>
      <button type="button" onclick="window.print()" class="px-4 py-2 bg-notion-text text-white rounded-md text-sm font-medium hover:bg-black inline-flex items-center gap-1.5">
        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Kartu
      </button>
    </form>
  </div>

  <main class="max-w-5xl mx-auto p-4 sm:p-6">
    @if(!auth()->user()?->isAdmin())
      <div class="no-print mb-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs">
        <strong>Catatan Role Panitia:</strong> Token pemilih disembunyikan pada pratinjau Panitia. Hanya Administrator yang dapat mencetak kartu dengan token terlihat.
      </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      @foreach($voters as $v)
        <div class="print-card bg-white border border-notion-border rounded-xl p-4 flex flex-col justify-between gap-3">
          <div class="flex items-center justify-between border-b border-notion-border pb-2.5 gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
              <div class="w-9 h-9 flex items-center justify-center shrink-0 {{ $setting->logo_path ? '' : 'rounded bg-notion-text text-white' }}">
                @if($setting->logo_path)
                  <img src="{{ asset('uploads/' . $setting->logo_path) }}" alt="Logo" class="w-full h-full object-contain">
                @else
                  <i data-lucide="vote" class="w-4 h-4"></i>
                @endif
              </div>
              <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-notion-text truncate">Kartu Pemilih OSIS</p>
                <p class="text-[10px] text-notion-gray truncate">{{ $setting->nama_sekolah }} · {{ $setting->tahun_ajaran }}</p>
              </div>
            </div>
            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-notion-sidebar border border-notion-border shrink-0">
              {{ $v->tps?->kode_tps ?? 'TPS' }}
            </span>
          </div>

          <div class="flex items-center gap-3 text-xs">
            <div class="flex-1 min-w-0 space-y-2">
              <div class="min-w-0">
                <span class="text-[10px] text-notion-gray block">Nama Pemilih</span>
                <span class="font-bold text-sm text-notion-text block truncate">{{ $v->nama }}</span>
              </div>
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                  <div>
                    <span class="text-[10px] text-notion-gray block">NIS</span>
                    <span class="font-mono font-medium">{{ $v->nis }}</span>
                  </div>
                  <div>
                    <span class="text-[10px] text-notion-gray block">Kelas</span>
                    <span class="font-medium">{{ $v->kelas }}</span>
                  </div>
                </div>

                <div class="border border-dashed border-notion-border rounded-lg min-w-[116px] px-4 py-1.5 bg-notion-sidebar/60 flex flex-col items-center justify-center text-center shrink-0">
                  <span class="text-[10px] text-notion-gray leading-tight mb-1.5">Token</span>
                  @if($v->has_voted)
                    <span class="font-mono text-xs font-bold line-through text-red-600">{{ $v->token }}</span>
                  @elseif(auth()->user()?->isAdmin())
                    <span class="font-mono text-sm font-bold tracking-wider text-notion-blue">{{ $v->token }}</span>
                  @else
                    <span class="font-mono text-xs font-bold tracking-widest text-notion-gray">••••••</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="shrink-0 flex flex-col items-center justify-center text-center">
              <div class="qr-box p-1 bg-white border border-notion-border rounded-lg"
                   data-url="{{ route('voting.index') }}"></div>
              <span class="text-[9px] text-notion-gray mt-0.5">Scan Bilik</span>
            </div>
          </div>

          <div class="pt-2 border-t border-notion-border flex items-center justify-center sm:justify-end text-[10px] text-notion-gray">
            <span>Pastikan token masih tersegel sebelum digunakan</span>
          </div>
        </div>
      @endforeach
    </div>
  </main>

  <script>
    function initModernSelects() {
      document.querySelectorAll('select.modern-select').forEach((selectEl) => {
        if (selectEl.dataset.upgraded) return;
        selectEl.dataset.upgraded = '1';
        selectEl.classList.add('hidden');

        const wrapper = document.createElement('div');
        wrapper.className = 'relative w-full';
        selectEl.parentNode.insertBefore(wrapper, selectEl.nextSibling);

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'w-full flex items-center justify-between border border-notion-border rounded-md px-3 py-2 text-sm bg-white hover:bg-notion-sidebar transition-colors text-left';
        const labelSpan = document.createElement('span');
        labelSpan.className = 'truncate text-notion-text';
        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', 'chevron-down');
        icon.className = 'w-4 h-4 text-notion-gray shrink-0 ml-2 transition-transform duration-150';
        trigger.append(labelSpan, icon);

        const menu = document.createElement('div');
        menu.className = 'hidden modern-select-menu fixed z-50 bg-white border border-notion-border rounded-lg shadow-lg p-1.5';
        const options = Array.from(selectEl.options);
        const listBox = document.createElement('div');
        listBox.className = 'max-h-52 overflow-y-auto space-y-0.5';

        let searchInput = null;
        if (options.length > 3) {
          const searchWrap = document.createElement('div');
          searchWrap.className = 'relative mb-1.5';
          searchWrap.innerHTML = '<i data-lucide="search" class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>';
          searchInput = document.createElement('input');
          searchInput.type = 'text';
          searchInput.placeholder = 'Cari...';
          searchInput.className = 'w-full bg-notion-sidebar border border-notion-border rounded-md pl-8 pr-2.5 py-1.5 text-sm';
          searchInput.addEventListener('input', () => renderOptions(searchInput.value));
          searchInput.addEventListener('click', (e) => e.stopPropagation());
          searchWrap.appendChild(searchInput);
          menu.appendChild(searchWrap);
        }
        menu.appendChild(listBox);

        function renderOptions(filter = '') {
          listBox.innerHTML = '';
          const q = filter.trim().toLowerCase();
          options.forEach((opt) => {
            if (q && !opt.text.toLowerCase().includes(q)) return;
            const btn = document.createElement('button');
            btn.type = 'button';
            const isSelected = opt.value === selectEl.value;
            btn.className = `w-full text-left px-2.5 py-1.5 text-sm rounded-md flex items-center justify-between ${isSelected ? 'bg-notion-hover text-notion-text font-medium' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text'}`;
            btn.innerHTML = `<span class="truncate">${opt.text}</span>` + (isSelected ? '<i data-lucide="check" class="w-4 h-4 shrink-0 ml-2"></i>' : '');
            btn.addEventListener('click', (e) => {
              e.stopPropagation();
              selectEl.value = opt.value;
              labelSpan.textContent = opt.text;
              menu.classList.add('hidden');
              icon.classList.remove('rotate-180');
              renderOptions(searchInput ? searchInput.value : '');
            });
            listBox.appendChild(btn);
          });
          lucide.createIcons();
        }

        labelSpan.textContent = selectEl.options[selectEl.selectedIndex]?.text || 'Pilih...';
        renderOptions();

        trigger.addEventListener('click', (e) => {
          e.stopPropagation();
          document.querySelectorAll('.modern-select-menu').forEach((m) => { if (m !== menu) m.classList.add('hidden'); });
          const willOpen = menu.classList.contains('hidden');
          if (willOpen) {
            const rect = trigger.getBoundingClientRect();
            menu.style.width = `${Math.max(rect.width, 180)}px`;
            menu.style.left = `${rect.left}px`;
            menu.style.top = `${rect.bottom + 4}px`;
            menu.classList.remove('hidden');
          } else {
            menu.classList.add('hidden');
          }
          icon.classList.toggle('rotate-180', !menu.classList.contains('hidden'));
        });

        wrapper.append(trigger);
        document.body.appendChild(menu);
      });
    }

    document.addEventListener('click', () => {
      document.querySelectorAll('.modern-select-menu').forEach((m) => m.classList.add('hidden'));
    });

    function renderModernQr(el, url, size = 60) {
      const temp = document.createElement('div');
      const qr = new QRCode(temp, {
        text: url,
        width: size,
        height: size,
        colorDark: '#222222',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
      });

      const model = qr._oQRCode;
      if (!model) {
        el.innerHTML = temp.innerHTML;
        return;
      }

      const n = model.getModuleCount();
      const isFinder = (r, c) =>
        (r < 7 && c < 7) ||
        (r < 7 && c >= n - 7) ||
        (r >= n - 7 && c < 7);

      const parts = [];

      const finders = [[0, 0], [0, n - 7], [n - 7, 0]];
      finders.forEach(([fr, fc]) => {
        parts.push(`<rect x="${fc + 0.5}" y="${fr + 0.5}" width="6" height="6" rx="1.5" fill="none" stroke="#222222" stroke-width="1"/>`);
        parts.push(`<rect x="${fc + 2}" y="${fr + 2}" width="3" height="3" rx="0.9" fill="#222222"/>`);
      });

      for (let r = 0; r < n; r++) {
        let c = 0;
        while (c < n) {
          if (!isFinder(r, c) && model.isDark(r, c)) {
            const start = c;
            while (c < n && !isFinder(r, c) && model.isDark(r, c)) {
              c++;
            }
            const len = c - start;
            parts.push(`<rect x="${start + 0.07}" y="${r + 0.07}" width="${len - 0.14}" height="0.86" rx="0.43" fill="#222222"/>`);
          } else {
            c++;
          }
        }
      }

      el.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${n} ${n}" width="${size}" height="${size}" class="block" shape-rendering="geometricPrecision">${parts.join('')}</svg>`;
    }

    document.addEventListener('DOMContentLoaded', () => {
      initModernSelects();
      lucide.createIcons();
      document.querySelectorAll('.qr-box').forEach((el) => {
        renderModernQr(el, el.dataset.url, 60);
      });
    });
  </script>
</body>
</html>
