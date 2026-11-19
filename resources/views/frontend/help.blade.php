@extends('layouts.frontend')

@section('title', 'Bantuan & Layanan Panitia')

@section('content')
  <section class="max-w-3xl mx-auto space-y-6">
    <div class="space-y-2">
      <i data-lucide="life-buoy" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Bantuan &amp; Layanan Panitia</h1>
      <p class="text-sm text-notion-gray">
        Informasi posko panitia dan solusi kendala teknis saat proses pemilihan OSIS di {{ $setting->nama_sekolah }}.
      </p>
    </div>

    <div class="border border-notion-border rounded-2xl bg-white p-6 sm:p-8 shadow-sm divide-y divide-notion-border text-sm leading-relaxed">
      <div class="pb-5 space-y-1.5">
        <h2 class="font-bold text-base text-notion-text flex items-center gap-2">
          <i data-lucide="key-round" class="w-4 h-4 text-notion-blue"></i> Kendala Token atau NIS
        </h2>
        <p class="text-notion-gray">Jika NIS tidak ditemukan, segel token rusak, atau token tidak berfungsi, silakan lapor ke meja verifikasi panitia di TPS Anda dengan membawa kartu pelajar.</p>
      </div>

      <div class="py-5 space-y-1.5">
        <h2 class="font-bold text-base text-notion-text flex items-center gap-2">
          <i data-lucide="smartphone" class="w-4 h-4 text-notion-blue"></i> Kendala Perangkat Ponsel
        </h2>
        <p class="text-notion-gray">Jika ponsel tidak memiliki koneksi atau baterai habis, panitia menyediakan perangkat cadangan di area TPS terdaftar Anda.</p>
      </div>

      <div class="pt-5 space-y-3">
        <h2 class="font-bold text-base text-notion-text flex items-center gap-2">
          <i data-lucide="map-pin" class="w-4 h-4 text-notion-blue"></i> Daftar Posko TPS Aktif
        </h2>
        <ul class="space-y-2.5 text-sm text-notion-gray">
          @foreach($tpsList as $tps)
            <li class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-notion-border/50 pb-2.5 last:border-0 last:pb-0">
              <strong class="font-semibold text-notion-text">{{ $tps->nama_tps }}</strong>
              <span>{{ $tps->lokasi ?? 'Area Sekolah' }}</span>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </section>
@endsection
