<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Daftar Pemilih Tetap (DPT) — {{ $selectedTps?->kode_tps ?? 'Semua TPS' }}</title>
  @php $faviconUrl = ($faviconLogo = ($setting ?? \App\Models\ElectionSetting::current())->logo_path) ? asset('uploads/' . $faviconLogo) : asset('favicon.ico'); @endphp
  <link rel="icon" href="{{ $faviconUrl }}">
  <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
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
    .tnum { font-variant-numeric: tabular-nums; }
    @media print {
      .no-print { display: none !important; }
      body { background: #ffffff !important; }
      .print-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; }
      tr { break-inside: avoid; page-break-inside: avoid; }
    }
  </style>
</head>
<body class="bg-notion-sidebar text-notion-text antialiased">

  <div class="no-print sticky top-0 z-20 bg-white border-b border-notion-border px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <a href="{{ route('voters.index') }}" class="px-3 py-1.5 rounded-md border border-notion-border text-sm font-medium hover:bg-notion-sidebar inline-flex items-center gap-1.5">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke DPT
      </a>
      <span class="text-sm font-semibold">Cetak Pengumuman DPT ({{ $voters->count() }} pemilih)</span>
    </div>

    <form method="GET" action="{{ route('voters.print-dpt') }}" class="flex flex-wrap items-center gap-2">
      <div class="w-56">
        <select name="tps_id" data-autosubmit="1" class="modern-select w-full">
          <option value="">Semua TPS</option>
          @foreach($tpsList as $tps)
            <option value="{{ $tps->id }}" @selected($tpsId == $tps->id)>{{ $tps->nama_tps }}</option>
          @endforeach
        </select>
      </div>
      <button type="button" onclick="window.print()" class="px-4 py-2 bg-notion-text text-white rounded-md text-sm font-medium hover:bg-black inline-flex items-center gap-1.5">
        <i data-lucide="printer" class="w-4 h-4"></i> Cetak DPT
      </button>
    </form>
  </div>

  <main class="max-w-4xl mx-auto p-4 sm:p-8">
    <div class="print-sheet bg-white border border-notion-border rounded-xl p-6 sm:p-8 shadow-sm space-y-6">

      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-notion-text pb-4">
        <div class="flex items-center gap-3.5">
          <div class="w-12 h-12 flex items-center justify-center shrink-0 {{ $setting->logo_path ? '' : 'rounded-lg bg-notion-text text-white' }}">
            @if($setting->logo_path)
              <img src="{{ asset('uploads/' . $setting->logo_path) }}" alt="Logo" class="w-full h-full object-contain">
            @else
              <i data-lucide="vote" class="w-6 h-6"></i>
            @endif
          </div>
          <div>
            <h1 class="text-xl font-bold uppercase tracking-tight">Daftar Pemilih Tetap (DPT)</h1>
            <p class="text-sm font-medium text-notion-gray">{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
          </div>
        </div>
        <div class="sm:text-right">
          <span class="inline-block px-3 py-1 rounded-md bg-notion-sidebar border border-notion-border font-mono text-sm font-bold text-notion-blue">
            {{ $selectedTps?->nama_tps ?? 'Semua TPS' }}
          </span>
          @if($selectedTps?->lokasi)
            <p class="text-xs text-notion-gray mt-1">Lokasi: {{ $selectedTps->lokasi }}</p>
          @endif
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
          <thead>
            <tr class="bg-notion-sidebar border-y border-notion-border text-notion-text">
              <th class="py-2.5 px-3 font-semibold border border-notion-border text-center w-12">No</th>
              <th class="py-2.5 px-3 font-semibold border border-notion-border w-32">NIS</th>
              <th class="py-2.5 px-3 font-semibold border border-notion-border">Nama Lengkap Siswa</th>
              <th class="py-2.5 px-3 font-semibold border border-notion-border w-28">Kelas</th>
              <th class="py-2.5 px-3 font-semibold border border-notion-border w-28">TPS</th>
            </tr>
          </thead>
          <tbody>
            @forelse($voters as $idx => $v)
              <tr class="{{ $idx % 2 === 1 ? 'bg-notion-sidebar/30' : 'bg-white' }}">
                <td class="py-2 px-3 border border-notion-border text-center font-mono tnum">{{ $idx + 1 }}</td>
                <td class="py-2 px-3 border border-notion-border font-mono tnum">{{ $v->nis }}</td>
                <td class="py-2 px-3 border border-notion-border font-medium">{{ $v->nama }}</td>
                <td class="py-2 px-3 border border-notion-border font-mono">{{ $v->kelas }}</td>
                <td class="py-2 px-3 border border-notion-border font-mono text-notion-blue font-semibold">{{ $v->tps?->kode_tps ?? '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-8 px-4 border border-notion-border text-center text-notion-gray">
                  Belum ada pemilih terdaftar pada TPS ini.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="pt-2 flex items-center justify-between text-xs text-notion-gray">
        <span>Total Pemilih Tercantum: <strong class="text-notion-text tnum">{{ $voters->count() }} siswa</strong></span>
        <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
      </div>
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
              if (selectEl.dataset.autosubmit === '1') {
                selectEl.form?.submit();
              } else {
                renderOptions(searchInput ? searchInput.value : '');
              }
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

    document.addEventListener('DOMContentLoaded', () => {
      initModernSelects();
      lucide.createIcons();
    });
  </script>
</body>
</html>
