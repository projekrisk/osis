@extends('layouts.frontend')

@section('title', 'Kebijakan Privasi & Kerahasiaan Suara')

@section('content')
  <section class="max-w-3xl mx-auto space-y-6">
    <div class="space-y-2">
      <i data-lucide="shield-check" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Kebijakan Privasi &amp; Kerahasiaan Suara</h1>
      <p class="text-sm text-notion-gray">
        Ketentuan perlindungan data pemilih dan kerahasiaan pilihan pada {{ $setting->nama_kegiatan }} di {{ $setting->nama_sekolah }}.
      </p>
    </div>

    <div class="border border-notion-border rounded-2xl bg-white p-6 sm:p-8 shadow-sm divide-y divide-notion-border text-sm leading-relaxed">
      <div class="pb-5 space-y-1.5">
        <h2 class="text-base font-bold text-notion-text">1. Data Pemilih yang Dikumpulkan</h2>
        <p class="text-notion-gray">Sistem hanya menyimpan data dasar Daftar Pemilih Tetap (DPT) berupa Nomor Induk Siswa (NIS), Nama Lengkap, Kelas/Unit, dan penugasan TPS untuk keperluan verifikasi hak pilih.</p>
      </div>

      <div class="py-5 space-y-1.5">
        <h2 class="text-base font-bold text-notion-text">2. Kerahasiaan Pilihan &amp; Token Satu Kali Pakai</h2>
        <p class="text-notion-gray">Token kartu pemilih bersifat rahasia, tidak dapat dilihat oleh panitia TPS sebelum digunakan, dan otomatis hangus setelah pemilih mengirimkan suara. Rekapitulasi publik hanya menampilkan akumulasi suara tanpa mengaitkan identitas pemilih dengan pasangan calon yang dipilih.</p>
      </div>

      <div class="pt-5 space-y-1.5">
        <h2 class="text-base font-bold text-notion-text">3. Verifikasi Kehadiran TPS</h2>
        <p class="text-notion-gray">Setelah selesai memilih, sistem menerbitkan Kode Bukti Pemilih yang hanya digunakan panitia TPS untuk mencocokkan daftar hadir secara langsung.</p>
      </div>
    </div>
  </section>
@endsection
