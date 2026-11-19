<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Pemilihan OSIS') — {{ $setting->nama_sekolah }}</title>
  @php
    $seoSetting = $setting ?? \App\Models\ElectionSetting::current();
    $faviconUrl = $seoSetting->logo_path ? asset('uploads/' . $seoSetting->logo_path) : asset('favicon.ico');
    $seoTitle = trim($__env->yieldContent('title', 'Pemilihan OSIS')) . ' — ' . $seoSetting->nama_sekolah;
    $seoDescription = trim($__env->yieldContent('meta_description', 'Portal resmi ' . $seoSetting->nama_kegiatan . ' ' . $seoSetting->nama_sekolah . ' Tahun Ajaran ' . $seoSetting->tahun_ajaran . '. Pelajari visi, misi, dan pantau hasil quick count secara langsung.'));
    $seoImage = $seoSetting->logo_path ? asset('uploads/' . $seoSetting->logo_path) : asset('uploads/hero.jpg');
    $twitterCard = $seoSetting->logo_path ? 'summary' : 'summary_large_image';
  @endphp
  <meta name="description" content="{{ $seoDescription }}">
  <meta name="robots" content="@yield('meta_robots', 'index, follow')">
  <meta name="theme-color" content="#2563eb">
  <link rel="canonical" href="{{ url()->current() }}">
  <link rel="image_src" href="{{ $seoImage }}">
  <meta itemprop="image" content="{{ $seoImage }}">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="id_ID">
  <meta property="og:site_name" content="{{ $seoSetting->nama_kegiatan }} — {{ $seoSetting->nama_sekolah }}">
  <meta property="og:title" content="{{ $seoTitle }}">
  <meta property="og:description" content="{{ $seoDescription }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ $seoImage }}">
  <meta property="og:image:secure_url" content="{{ $seoImage }}">
  <meta property="og:image:alt" content="Logo {{ $seoSetting->nama_sekolah }}">
  <meta name="twitter:card" content="{{ $twitterCard }}">
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
              bg: '#ffffff', sidebar: '#f7f7f5', hover: '#efefed',
              text: '#222222', gray: '#666666', border: '#e5e5e5',
              blue: '#2563eb', blueHover: '#1d4ed8', red: '#e03e3e', redBg: '#fbdbdc',
            }
          }
        }
      }
    }
  </script>
  <style>
    html { font-size: 16px; }
    body { background-color: #ffffff; color: #222222; font-size: 14px; }
    .tnum { font-variant-numeric: tabular-nums; }
  </style>
</head>
<body class="antialiased bg-notion-bg text-notion-text flex flex-col min-h-screen selection:bg-notion-blue/20">

  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-notion-border">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between gap-3">
      <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0">
        <div class="w-8 h-8 flex items-center justify-center shrink-0 {{ $setting->logo_path ? '' : 'rounded-lg bg-notion-text text-white' }}">
          @if($setting->logo_path)
            <img src="{{ asset('uploads/' . $setting->logo_path) }}" alt="Logo" class="w-full h-full object-contain">
          @else
            <i data-lucide="vote" class="w-4 h-4"></i>
          @endif
        </div>
        <div class="min-w-0 leading-tight">
          <p class="font-bold text-sm text-notion-text truncate">{{ $setting->nama_kegiatan }}</p>
          <p class="text-sm text-notion-gray truncate">{{ $setting->nama_sekolah }} · {{ $setting->tahun_ajaran }}</p>
        </div>
      </a>

      <nav class="hidden md:flex items-center gap-1.5 whitespace-nowrap">
        <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text' }}">
          Kandidat
        </a>
        <a href="{{ route('quick-count') }}" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors inline-flex items-center gap-1.5 {{ request()->routeIs('quick-count') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text' }}">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> Quick Count
        </a>
        <a href="{{ route('voting.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium bg-notion-blue text-white hover:bg-notion-blueHover transition-colors inline-flex items-center gap-1.5">
          <i data-lucide="smartphone" class="w-4 h-4"></i> Bilik Suara
        </a>
      </nav>

      <button type="button" onclick="toggleFrontendSidebar()" class="md:hidden p-2 rounded-lg text-notion-text hover:bg-notion-hover transition-colors" aria-label="Buka Menu">
        <i data-lucide="menu" class="w-5 h-5"></i>
      </button>
    </div>
  </header>

  <div id="fe-mobile-overlay" onclick="toggleFrontendSidebar()" class="fixed inset-0 bg-black/35 backdrop-blur-[1px] z-50 hidden md:hidden"></div>
  <aside id="fe-mobile-sidebar" class="fixed inset-y-0 left-0 w-[275px] bg-notion-sidebar border-r border-notion-border z-50 md:hidden transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="h-14 px-4 border-b border-notion-border flex items-center justify-between bg-white">
      <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-7 h-7 flex items-center justify-center shrink-0 {{ $setting->logo_path ? '' : 'rounded bg-notion-text text-white' }}">
          @if($setting->logo_path)
            <img src="{{ asset('uploads/' . $setting->logo_path) }}" alt="Logo" class="w-full h-full object-contain">
          @else
            <i data-lucide="vote" class="w-4 h-4"></i>
          @endif
        </div>
        <span class="font-bold text-sm text-notion-text truncate">Menu Navigasi</span>
      </div>
      <button type="button" onclick="toggleFrontendSidebar()" class="p-1.5 rounded-md text-notion-gray hover:bg-notion-hover">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <div class="p-3 flex-1 overflow-y-auto space-y-1">
      <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('home') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
        <i data-lucide="award" class="w-4 h-4 shrink-0"></i> Daftar Kandidat
      </a>
      <a href="{{ route('quick-count') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('quick-count') ? 'bg-notion-hover text-notion-text font-semibold' : 'text-notion-gray hover:bg-notion-hover hover:text-notion-text font-medium' }}">
        <i data-lucide="bar-chart-2" class="w-4 h-4 shrink-0"></i> Quick Count
      </a>
      <a href="{{ route('voting.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm bg-notion-blue text-white font-semibold mt-2">
        <i data-lucide="smartphone" class="w-4 h-4 shrink-0"></i> Bilik Suara Mobile
      </a>
    </div>
  </aside>

  @yield('hero')

  <main class="flex-1 w-full lg:min-w-[1000px] max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-12 space-y-12">
    @yield('content')
  </main>

  <footer class="border-t border-notion-border bg-notion-sidebar/50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-notion-gray text-center sm:text-left">
      <p>{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
      <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
        <a href="{{ route('legal.privacy') }}" class="hover:text-notion-text transition-colors">Kebijakan Privasi</a>
        <a href="{{ route('legal.terms') }}" class="hover:text-notion-text transition-colors">Tata Tertib Pemilihan</a>
        <a href="{{ route('legal.help') }}" class="hover:text-notion-text transition-colors">Bantuan Panitia</a>
      </div>
    </div>
  </footer>

  @stack('modals')

  <div id="toast-container" class="fixed bottom-4 right-4 left-4 sm:left-auto z-[130] flex flex-col gap-2 max-w-sm mx-auto sm:mx-0 pointer-events-none"></div>

  <script>
    function showToast(message, type = 'info') {
      const container = document.getElementById('toast-container');
      if (!container) return;
      const toast = document.createElement('div');
      const colors = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        error: 'bg-notion-redBg border-red-200 text-notion-red',
        info: 'bg-white border-notion-border text-notion-text',
      };
      toast.className = `pointer-events-auto border rounded-xl px-4 py-3 shadow-lg text-sm flex items-center justify-between gap-3 ${colors[type] || colors.info}`;
      toast.innerHTML = `<span>${message}</span>`;
      container.appendChild(toast);
      setTimeout(() => toast.remove(), 3500);
    }

    function toggleFrontendSidebar() {
      const sb = document.getElementById('fe-mobile-sidebar');
      const ov = document.getElementById('fe-mobile-overlay');
      if (!sb || !ov) return;
      const isClosed = sb.classList.contains('-translate-x-full');
      sb.classList.toggle('-translate-x-full', !isClosed);
      ov.classList.toggle('hidden', !isClosed);
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

    document.addEventListener('DOMContentLoaded', () => {
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
      @if(session('success')) showToast(@json(session('success')), 'success'); @endif
      @if(session('error')) showToast(@json(session('error')), 'error'); @endif
      @if(session('info')) showToast(@json(session('info')), 'info'); @endif
      @if($errors->any()) showToast(@json($errors->first()), 'error'); @endif
    });
  </script>
  @stack('scripts')
</body>
</html>
