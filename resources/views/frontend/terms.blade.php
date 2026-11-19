@extends('layouts.frontend')

@section('title', 'Tata Tertib Pemilihan OSIS')

@section('content')
  <section class="max-w-3xl mx-auto space-y-6">
    <div class="space-y-2">
      <i data-lucide="file-text" class="w-12 h-12 text-notion-text/80"></i>
      <h1 class="text-3xl font-bold tracking-tight">Syarat &amp; Tata Tertib Pemilihan</h1>
      <p class="text-sm text-notion-gray">
        Pedoman resmi pelaksanaan pemungutan suara {{ $setting->nama_kegiatan }} Tahun Ajaran {{ $setting->tahun_ajaran }}.
      </p>
    </div>

    <div class="border border-notion-border rounded-2xl bg-white p-6 sm:p-8 shadow-sm divide-y divide-notion-border text-sm leading-relaxed">
      <div class="pb-5 flex items-start gap-3.5">
        <span class="font-mono font-bold text-notion-blue">01</span>
        <div>
          <h2 class="font-bold text-notion-text">Wajib Terdaftar di DPT</h2>
          <p class="text-notion-gray mt-1">Pemilih harus tercatat pada Daftar Pemilih Tetap (DPT) dan membawa kartu pemilih resmi yang berisi NIS serta Token tersegel.</p>
        </div>
      </div>

      <div class="py-5 flex items-start gap-3.5">
        <span class="font-mono font-bold text-notion-blue">02</span>
        <div>
          <h2 class="font-bold text-notion-text">Sesuai Lokasi TPS</h2>
          <p class="text-notion-gray mt-1">Setiap pemilih hanya dapat memberikan suara di TPS yang tercantum pada kartu pemilihnya selama jadwal berlangsung (<span class="font-mono text-notion-text">{{ $setting->jadwal_mulai }}</span> s/d <span class="font-mono text-notion-text">{{ $setting->jadwal_selesai }}</span>).</p>
        </div>
      </div>

      <div class="py-5 flex items-start gap-3.5">
        <span class="font-mono font-bold text-notion-blue">03</span>
        <div>
          <h2 class="font-bold text-notion-text">Satu Pemilih Satu Suara</h2>
          <p class="text-notion-gray mt-1">Hak pilih hanya dapat digunakan satu kali. Setelah suara dikirim, token langsung hangus dan tidak dapat digunakan kembali.</p>
        </div>
      </div>

      <div class="pt-5 flex items-start gap-3.5">
        <span class="font-mono font-bold text-notion-blue">04</span>
        <div>
          <h2 class="font-bold text-notion-text">Menunjukkan Kode Bukti ke Panitia</h2>
          <p class="text-notion-gray mt-1">Pemilih wajib memperlihatkan layar Bukti Telah Memilih kepada panitia TPS sebelum meninggalkan area pemungutan suara.</p>
        </div>
      </div>
    </div>
  </section>
@endsection
