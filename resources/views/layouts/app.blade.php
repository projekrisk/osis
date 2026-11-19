<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Pemilihan OSIS') — Sistem E-Voting OSIS</title>
  <meta name="robots" content="noindex, nofollow">
  @php
    $seoSetting = $appSetting ?? $setting ?? \App\Models\ElectionSetting::current();
    $faviconUrl = $seoSetting->logo_path ? asset('uploads/' . $seoSetting->logo_path) : asset('favicon.ico');
    $seoImage = $seoSetting->logo_path ? asset('uploads/' . $seoSetting->logo_path) : asset('uploads/hero.jpg');
    $seoTitle = trim($__env->yieldContent('title', 'Pemilihan OSIS')) . ' — Sistem E-Voting OSIS';
    $seoDescription = $seoSetting->nama_kegiatan . ' — ' . $seoSetting->nama_sekolah . ' (' . $seoSetting->tahun_ajaran . ')';
  @endphp
  <link rel="image_src" href="{{ $seoImage }}">
  <meta itemprop="image" content="{{ $seoImage }}">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="id_ID">
  <meta property="og:title" content="{{ $seoTitle }}">
  <meta property="og:description" content="{{ $seoDescription }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ $seoImage }}">
  <meta property="og:image:secure_url" content="{{ $seoImage }}">
  <meta property="og:image:alt" content="Logo {{ $seoSetting->nama_sekolah }}">
  <meta name="twitter:card" content="{{ $seoSetting->logo_path ? 'summary' : 'summary_large_image' }}">
  <meta name="twitter:title" content="{{ $seoTitle }}">
  <meta name="twitter:description" content="{{ $seoDescription }}">
  <meta name="twitter:image" content="{{ $seoImage }}">
  <link rel="icon" href="{{ $faviconUrl }}">
  <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

  <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
  <link rel="dns-prefetch" href="https://unpkg.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest" defer></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Bricolage Grotesque"', 'sans-serif'],
            mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace'],
          },
          colors: {
            notion: {
              bg: '#ffffff',
              sidebar: '#f7f7f5',
              hover: '#efefed',
              text: '#222222',
              gray: '#666666',
              border: '#e5e5e5',
              blue: '#2563eb',
              blueHover: '#1d4ed8',
              red: '#e03e3e',
              redBg: '#fbdbdc',
              green: '#0f7b6c',
              greenBg: '#ebeced'
            }
          }
        }
      }
    }
  </script>

  <style>
    html { font-size: 16px; }
    body { background-color: #ffffff; color: #222222; font-size: 14px; }

    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #d3d1cb; border-radius: 4px; border: 2px solid transparent; background-clip: padding-box; }
    ::-webkit-scrollbar-thumb:hover { background: #aeaca6; border: 2px solid transparent; background-clip: padding-box; }

    .tnum { font-variant-numeric: tabular-nums; }
    .sidebar-transition { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s cubic-bezier(0.4, 0, 0.2, 1); }
    .sidebar-label { transition: opacity 0.2s ease, width 0.2s ease; white-space: nowrap; }

    @media (min-width: 768px) {
      #app-sidebar.sidebar-collapsed { width: 68px !important; }
      #app-sidebar.sidebar-collapsed .sidebar-label,
      #app-sidebar.sidebar-collapsed .sidebar-heading { display: none; }
      #app-sidebar.sidebar-collapsed .sidebar-brand { justify-content: center; padding-left: 0; padding-right: 0; margin-left: 0.5rem; margin-right: 0.5rem; }
      #app-sidebar.sidebar-collapsed .sidebar-brand-icon { margin-right: 0; }
      #app-sidebar.sidebar-collapsed .sidebar-link { justify-content: center; padding-left: 0; padding-right: 0; gap: 0; }
    }

    @keyframes smoothReveal {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-reveal { animation: smoothReveal 0.22s cubic-bezier(0.4, 0, 0.2, 1); }

    @keyframes toastSlideIn {
      from { opacity: 0; transform: translateY(14px) scale(0.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-toast { animation: toastSlideIn 0.25s cubic-bezier(0.4, 0, 0.2, 1); }

    input:not([type="checkbox"]):not([type="radio"]):focus,
    textarea:focus,
    select:focus {
      outline: none !important;
      border-color: #2563eb !important;
      box-shadow: 0 0 0 1px #2563eb !important;
    }
  </style>
</head>
<body class="antialiased selection:bg-notion-blue/20 bg-notion-bg text-notion-text">

@php
  $appSetting = \App\Models\ElectionSetting::current();
@endphp

<div class="min-h-screen flex bg-notion-bg relative">

  <div id="app-mobile-overlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/25 z-30 hidden md:hidden backdrop-blur-[1px]"></div>

  <aside id="app-sidebar" class="sidebar-transition fixed md:sticky top-0 inset-y-0 left-0 w-[285px] bg-notion-sidebar border-r border-notion-border flex flex-col z-40 transform -translate-x-full md:translate-x-0 h-screen shrink-0 overflow-hidden">
    <a href="{{ route('dashboard') }}" class="sidebar-brand h-12 flex items-center px-3.5 hover:bg-notion-hover transition-colors mt-2 mx-2 rounded-md">
      <div class="sidebar-brand-icon w-8 h-8 flex items-center justify-center mr-2.5 shrink-0 {{ $appSetting->logo_path ? '' : 'rounded bg-notion-text text-white' }}">
        @if($appSetting->logo_path)
          <img src="{{ asset('uploads/' . $appSetting->logo_path) }}" alt="Logo" class="w-full h-full object-contain">
        @else
          <i data-lucide="vote" class="w-4 h-4"></i>
        @endif
      </div>
      <div class="sidebar-label flex-1 min-w-0">
        <p class="font-semibold text-sm text-notion-text truncate">Pemilihan OSIS</p>
        <p class="text-[11px] text-notion-gray truncate">{{ $appSetting->nama_sekolah }}</p>
      </div>
    </a>

    <div class="mt-4 px-2 flex-1 overflow-y-auto space-y-5">
      <div>
        <p class="sidebar-heading px-3 text-xs font-semibold text-notion-gray mb-1.5">Menu Utama</p>
        <nav class="space-y-0.5">
          <a href="{{ route('dashboard') }}" title="Dasbor"
             class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
            <i data-lucide="bar-chart-2" class="w-4 h-4 shrink-0"></i>
            <span class="sidebar-label truncate">Dasbor</span>
          </a>
          @if(auth()->user()?->isAdmin())
            <a href="{{ route('candidates.index') }}" title="Kandidat Paslon"
               class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('candidates.*') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
              <i data-lucide="award" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label truncate">Kandidat</span>
            </a>
          @endif
          <a href="{{ route('voting.index') }}" title="Bilik Suara"
             class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('voting.*') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
            <i data-lucide="smartphone" class="w-4 h-4 shrink-0"></i>
            <span class="sidebar-label truncate">Bilik Suara</span>
          </a>
          <a href="{{ route('tps.monitor') }}" title="Pemantauan TPS"
             class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('tps.monitor*') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
            <i data-lucide="radio" class="w-4 h-4 shrink-0"></i>
            <span class="sidebar-label truncate">Pemantauan TPS</span>
          </a>
        </nav>
      </div>

      <div>
        <p class="sidebar-heading px-3 text-xs font-semibold text-notion-gray mb-1.5">Master Data &amp; Laporan</p>
        <nav class="space-y-0.5">
          <a href="{{ route('voters.index') }}" title="Data Pemilih (DPT)"
             class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('voters.*') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
            <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
            <span class="sidebar-label truncate">Data Pemilih (DPT)</span>
          </a>
          @if(auth()->user()?->isAdmin())
            <a href="{{ route('tps.index') }}" title="Pengaturan TPS"
               class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('tps.index') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
              <i data-lucide="map-pin" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label truncate">Pengaturan TPS</span>
            </a>
          @endif
          <a href="{{ route('recap.index') }}" title="Rekapitulasi"
             class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('recap.*') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
            <i data-lucide="pie-chart" class="w-4 h-4 shrink-0"></i>
            <span class="sidebar-label truncate">Rekapitulasi</span>
          </a>
          @if(auth()->user()?->isAdmin())
            <a href="{{ route('users.index') }}" title="Pengguna"
               class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('users.*') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
              <i data-lucide="shield" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label truncate">Pengguna</span>
            </a>
            <a href="{{ route('settings.index') }}" title="Pengaturan"
               class="sidebar-link flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-md transition-colors {{ request()->routeIs('settings.*') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
              <i data-lucide="settings" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label truncate">Pengaturan</span>
            </a>
          @endif
        </nav>
      </div>
    </div>

    <div class="p-3 border-t border-notion-border">
      <button type="button" onclick="openAppModal('password-modal')" title="Ganti Password"
              class="sidebar-link w-full flex items-center gap-2 px-2.5 py-1.5 text-sm text-notion-gray hover:bg-notion-hover hover:text-notion-text rounded-md transition-colors">
        <i data-lucide="key-round" class="w-4 h-4 shrink-0"></i>
        <span class="sidebar-label truncate">Ganti Password</span>
      </button>
    </div>
  </aside>

  <div class="flex-1 flex flex-col min-w-0 bg-notion-bg">

    <header class="sticky top-0 z-20 h-12 flex items-center px-4 sm:px-6 justify-between shrink-0 border-b border-notion-border bg-white/90 backdrop-blur-md">
      <div class="flex items-center gap-2 min-w-0">
        <button type="button" onclick="toggleMobileSidebar()" class="md:hidden p-1.5 text-notion-gray hover:bg-notion-hover rounded-md" aria-label="Buka Menu">
          <i data-lucide="menu" class="w-4 h-4"></i>
        </button>
        <button type="button" onclick="toggleDesktopSidebar()" class="hidden md:inline-flex p-1.5 text-notion-gray hover:text-notion-text hover:bg-notion-hover rounded-md transition-colors" title="Tutup / Buka Sidebar">
          <i data-lucide="panel-left" class="w-4 h-4"></i>
        </button>
        <div class="flex items-center text-sm text-notion-gray min-w-0">
          <span class="truncate">Pemilihan OSIS</span>
          <span class="mx-1.5">/</span>
          <span class="text-notion-text font-medium truncate">@yield('breadcrumb', 'Dasbor')</span>
        </div>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        @php $appState = $appSetting->votingState(); @endphp
        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-medium {{ $appState === 'berlangsung' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($appState === 'selesai' ? 'bg-notion-hover text-notion-gray border border-notion-border' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
          <span class="w-1.5 h-1.5 rounded-full {{ $appState === 'berlangsung' ? 'bg-emerald-600' : ($appState === 'selesai' ? 'bg-notion-gray' : 'bg-amber-600') }}"></span>
          {{ ucfirst($appState) }}
        </span>
        @auth
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full border border-notion-border bg-notion-sidebar flex items-center justify-center text-xs font-bold text-notion-text">
              {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="hidden sm:flex flex-col leading-tight">
              <span class="text-xs font-semibold text-notion-text">{{ auth()->user()->name }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="p-1.5 text-notion-gray hover:text-notion-red hover:bg-red-50 rounded-md transition-colors" title="Keluar">
                <i data-lucide="log-out" class="w-4 h-4"></i>
              </button>
            </form>
          </div>
        @endauth
      </div>
    </header>

    <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-8 py-8 space-y-8 animate-reveal">
      @yield('content')
    </main>
  </div>
</div>

@stack('modals')

<div id="password-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('password-modal')">
  <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
    <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white">
      <div class="w-10 h-1.5 bg-notion-border rounded-full"></div>
    </div>
    <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
      <h3 class="font-bold text-lg">Ganti Password Panitia</h3>
      <button type="button" onclick="closeAppModal('password-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>
    <form action="{{ route('settings.password') }}" method="POST" class="p-6 overflow-y-auto flex-1 space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Password Saat Ini</label>
        <div class="relative">
          <input type="password" name="current_password" id="global-pw-current" required placeholder="Masukkan password saat ini..." class="w-full border border-notion-border rounded-md pl-3 pr-10 py-2 text-sm bg-white">
          <button type="button" onclick="togglePassword('global-pw-current', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-notion-gray hover:text-notion-text">
            <i data-lucide="eye" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Password Baru</label>
        <div class="relative">
          <input type="password" name="new_password" id="global-pw-new" required minlength="6" placeholder="Minimal 6 karakter..." class="w-full border border-notion-border rounded-md pl-3 pr-10 py-2 text-sm bg-white">
          <button type="button" onclick="togglePassword('global-pw-new', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-notion-gray hover:text-notion-text">
            <i data-lucide="eye" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
        <div class="relative">
          <input type="password" name="new_password_confirmation" id="global-pw-confirm" required minlength="6" placeholder="Ulangi password baru..." class="w-full border border-notion-border rounded-md pl-3 pr-10 py-2 text-sm bg-white">
          <button type="button" onclick="togglePassword('global-pw-confirm', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-notion-gray hover:text-notion-text">
            <i data-lucide="eye" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeAppModal('password-modal')" class="px-4 py-2 text-sm text-notion-gray hover:bg-notion-hover rounded-md">Batal</button>
        <button type="submit" class="px-4 py-2 bg-notion-blue text-white text-sm font-medium rounded-md hover:bg-notion-blueHover">Simpan Password</button>
      </div>
    </form>
  </div>
</div>

<div id="toast-container" class="fixed bottom-4 right-4 z-[130] flex flex-col gap-2 max-w-sm w-[calc(100%-2rem)] sm:w-auto pointer-events-none"></div>

<script>
  function toggleDesktopSidebar() {
    const sb = document.getElementById('app-sidebar');
    if (!sb) return;
    const collapsed = sb.classList.toggle('sidebar-collapsed');
    localStorage.setItem('osis_sidebar_collapsed', collapsed ? '1' : '0');
  }

  function toggleMobileSidebar() {
    const sb = document.getElementById('app-sidebar');
    const ov = document.getElementById('app-mobile-overlay');
    if (!sb || !ov) return;
    const isClosed = sb.classList.contains('-translate-x-full');
    sb.classList.toggle('-translate-x-full', !isClosed);
    ov.classList.toggle('hidden', !isClosed);
  }

  function switchAppTab(btn, panelId, group = 'default') {
    document.querySelectorAll(`.app-tab[data-tab-group="${group}"]`).forEach((t) => {
      t.classList.remove('border-notion-blue', 'text-notion-blue');
      t.classList.add('border-transparent', 'text-notion-gray');
    });
    btn.classList.add('border-notion-blue', 'text-notion-blue');
    btn.classList.remove('border-transparent', 'text-notion-gray');
    document.querySelectorAll(`.app-tab-panel[data-tab-group="${group}"]`).forEach((p) => p.classList.add('hidden'));
    document.getElementById(panelId)?.classList.remove('hidden');
  }

  function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.innerHTML = `<i data-lucide="${isHidden ? 'eye-off' : 'eye'}" class="w-4 h-4"></i>`;
    lucide.createIcons();
  }

  let currentMobRowOpen = null;
  function toggleMobileRow(prefix, idx) {
    const rowId = `${prefix}-${idx}`;
    const detail = document.getElementById(`${prefix}-detail-${idx}`);
    const icon = document.getElementById(`${prefix}-icon-${idx}`);
    if (!detail) return;
    if (currentMobRowOpen && currentMobRowOpen !== rowId) {
      const [p, i] = currentMobRowOpen.split('-');
      document.getElementById(`${p}-detail-${i}`)?.classList.add('hidden');
      document.getElementById(`${p}-icon-${i}`)?.classList.remove('rotate-180');
    }
    const isOpen = !detail.classList.contains('hidden');
    detail.classList.toggle('hidden', isOpen);
    icon?.classList.toggle('rotate-180', !isOpen);
    currentMobRowOpen = isOpen ? null : rowId;
  }

  function openAppModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const panel = modal.firstElementChild;
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
      modal.classList.remove('opacity-0');
      panel?.classList.remove('translate-y-6', 'sm:scale-95');
    });
  }

  function closeAppModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const panel = modal.firstElementChild;
    modal.classList.add('opacity-0');
    panel?.classList.add('translate-y-6', 'sm:scale-95');
    setTimeout(() => modal.classList.add('hidden'), 180);
  }

  function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    const colors = {
      success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
      error: 'bg-notion-redBg border-red-200 text-notion-red',
      info: 'bg-white border-notion-border text-notion-text',
    };
    toast.className = `pointer-events-auto animate-toast border rounded-lg px-4 py-3 shadow-lg text-sm flex items-center justify-between gap-3 ${colors[type] || colors.info}`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
  }

  function initModernSelects() {
    document.querySelectorAll('select.modern-select').forEach((selectEl) => {
      if (selectEl.dataset.upgraded) return;
      selectEl.dataset.upgraded = '1';
      selectEl.classList.add('hidden');

      const wrapper = document.createElement('div');
      wrapper.className = 'relative w-full modern-select-wrapper';
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
      menu.className = 'hidden modern-select-menu fixed z-[150] bg-white border border-notion-border rounded-lg shadow-lg p-1.5';
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
            selectEl.dispatchEvent(new Event('change', { bubbles: true }));
            labelSpan.textContent = opt.text;
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180');
            renderOptions(searchInput ? searchInput.value : '');
          });
          listBox.appendChild(btn);
        });
        lucide.createIcons();
      }

      selectEl.syncModernLabel = () => {
        labelSpan.textContent = selectEl.options[selectEl.selectedIndex]?.text || 'Pilih...';
        renderOptions(searchInput ? searchInput.value : '');
      };

      labelSpan.textContent = selectEl.options[selectEl.selectedIndex]?.text || 'Pilih...';
      renderOptions();

      trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        document.querySelectorAll('.modern-select-menu').forEach((m) => {
          if (m !== menu) m.classList.add('hidden');
        });
        const willOpen = menu.classList.contains('hidden');
        if (willOpen) {
          const rect = trigger.getBoundingClientRect();
          menu.style.width = `${Math.max(rect.width, 180)}px`;
          menu.style.left = `${rect.left}px`;
          menu.classList.remove('hidden');
          const menuHeight = menu.offsetHeight || 200;
          const spaceBelow = window.innerHeight - rect.bottom;
          if (spaceBelow < menuHeight + 12 && rect.top > menuHeight + 12) {
            menu.style.top = `${rect.top - menuHeight - 4}px`;
          } else {
            menu.style.top = `${rect.bottom + 4}px`;
          }
        } else {
          menu.classList.add('hidden');
        }
        icon.classList.toggle('rotate-180', !menu.classList.contains('hidden'));
      });

      wrapper.append(trigger);
      document.body.appendChild(menu);
    });
  }

  function initModernFileUploads() {
    document.querySelectorAll('input[type="file"].modern-file').forEach((fileEl) => {
      if (fileEl.dataset.upgraded) return;
      fileEl.dataset.upgraded = '1';
      fileEl.classList.add('hidden');
      const placeholder = fileEl.dataset.placeholder || 'Pilih atau seret file ke sini...';
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'w-full flex items-center justify-between gap-3 px-3.5 py-2.5 bg-notion-sidebar/60 border border-dashed border-notion-border rounded-lg text-sm hover:bg-notion-hover/60 transition-all text-left';
      const update = () => {
        const file = fileEl.files?.[0];
        btn.innerHTML = file
          ? `<span class="flex items-center gap-2 min-w-0"><i data-lucide="file-check-2" class="w-4 h-4 text-emerald-600 shrink-0"></i><span class="truncate font-medium">${file.name}</span></span><span class="text-xs text-notion-blue font-medium shrink-0">Ganti</span>`
          : `<span class="flex items-center gap-2 min-w-0"><i data-lucide="upload-cloud" class="w-4 h-4 text-notion-gray shrink-0"></i><span class="truncate text-notion-gray">${placeholder}</span></span><span class="px-2.5 py-1 bg-white border border-notion-border rounded text-xs font-medium shrink-0">Pilih File</span>`;
        lucide.createIcons();
      };
      btn.addEventListener('click', () => fileEl.click());
      fileEl.addEventListener('change', update);
      fileEl.parentNode.insertBefore(btn, fileEl.nextSibling);
      update();
    });
  }

  document.addEventListener('click', () => {
    document.querySelectorAll('.modern-select-menu').forEach((m) => m.classList.add('hidden'));
  });

  document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('osis_sidebar_collapsed') === '1') {
      document.getElementById('app-sidebar')?.classList.add('sidebar-collapsed');
    }
    initModernSelects();
    initModernFileUploads();
    lucide.createIcons();

    @if(session('success'))
      showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
      showToast(@json(session('error')), 'error');
    @endif
    @if(session('info'))
      showToast(@json(session('info')), 'info');
    @endif
    @if($errors->any())
      showToast(@json($errors->first()), 'error');
    @endif
  });
</script>
@stack('scripts')
</body>
</html>
