<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk Panel Pemilihan OSIS</title>
  <meta name="robots" content="noindex, nofollow">
  @php
    $seoSetting = $setting ?? \App\Models\ElectionSetting::current();
    $faviconUrl = $seoSetting->logo_path ? asset('uploads/' . $seoSetting->logo_path) : asset('favicon.ico');
    $seoImage = $seoSetting->logo_path ? asset('uploads/' . $seoSetting->logo_path) : asset('uploads/hero.jpg');
    $seoDescription = 'Panel Pemilihan OSIS — ' . $seoSetting->nama_sekolah . ' (' . $seoSetting->tahun_ajaran . ')';
  @endphp
  <link rel="image_src" href="{{ $seoImage }}">
  <meta itemprop="image" content="{{ $seoImage }}">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="id_ID">
  <meta property="og:title" content="Masuk Panel Pemilihan OSIS — {{ $seoSetting->nama_sekolah }}">
  <meta property="og:description" content="{{ $seoDescription }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ $seoImage }}">
  <meta property="og:image:secure_url" content="{{ $seoImage }}">
  <meta property="og:image:alt" content="Logo {{ $seoSetting->nama_sekolah }}">
  <meta name="twitter:card" content="{{ $seoSetting->logo_path ? 'summary' : 'summary_large_image' }}">
  <meta name="twitter:title" content="Masuk Panel Pemilihan OSIS — {{ $seoSetting->nama_sekolah }}">
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
</head>
<body class="antialiased bg-notion-sidebar min-h-screen flex flex-col justify-center items-center p-4 text-notion-text">
  <div class="w-full max-w-md space-y-6">
    <div class="text-center space-y-2">
      <div class="w-12 h-12 flex items-center justify-center mx-auto {{ $setting->logo_path ? '' : 'rounded-xl bg-notion-text text-white' }}">
        @if($setting->logo_path)
          <img src="{{ asset('uploads/' . $setting->logo_path) }}" alt="Logo" class="w-full h-full object-contain">
        @else
          <i data-lucide="vote" class="w-6 h-6"></i>
        @endif
      </div>
      <h1 class="text-2xl font-bold tracking-tight">Panel Pemilihan OSIS</h1>
      <p class="text-sm text-notion-gray">{{ $setting->nama_sekolah }} · {{ $setting->tahun_ajaran }}</p>
    </div>

    <div class="bg-white border border-notion-border rounded-xl p-6 shadow-sm">
      <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input type="email" name="email" id="login-email" value="{{ old('email') }}" required autofocus placeholder="xxx@email.sch.id" class="w-full border border-notion-border rounded-md px-3 py-2 text-sm bg-white focus:outline-none focus:border-notion-blue">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <div class="relative">
            <input type="password" name="password" id="login-password" required placeholder="Masukkan password..." class="w-full border border-notion-border rounded-md pl-3 pr-10 py-2 text-sm bg-white focus:outline-none focus:border-notion-blue">
            <button type="button" onclick="togglePw()" class="absolute inset-y-0 right-0 px-3 flex items-center text-notion-gray hover:text-notion-text">
              <i id="pw-eye-icon" data-lucide="eye" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="w-full py-2.5 px-4 bg-notion-text text-white text-sm font-medium rounded-md hover:bg-black transition-colors">
          Masuk ke Dasbor
        </button>
      </form>
    </div>
  </div>

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
      toast.textContent = message;
      container.appendChild(toast);
      setTimeout(() => toast.remove(), 4000);
    }

    function togglePw() {
      const inp = document.getElementById('login-password');
      const isHidden = inp.type === 'password';
      inp.type = isHidden ? 'text' : 'password';
      document.getElementById('pw-eye-icon').setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
      lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
      @if(session('error'))
        showToast(@json(session('error')), 'error');
      @endif
      @if(session('info'))
        showToast(@json(session('info')), 'info');
      @endif
      @if(session('success'))
        showToast(@json(session('success')), 'success');
      @endif
      @if($errors->any())
        showToast(@json($errors->first()), 'error');
      @endif
    });
  </script>
</body>
</html>
