@extends('layouts.frontend')

@section('title', 'Beranda Pemilihan OSIS')
@section('meta_description', 'Portal resmi ' . $setting->nama_kegiatan . ' ' . $setting->nama_sekolah . ' (' . $setting->tahun_ajaran . '). Kenali pasangan calon, visi, misi, serta lokasi TPS resmi.')

@section('hero')

  <section class="relative w-full min-h-[calc(100vh-3.5rem)] flex items-center border-b border-notion-border bg-notion-text text-white overflow-hidden"
           style="background-image: url('{{ asset('uploads/hero.jpg') }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-gradient-to-br from-black/75 via-black/60 to-black/50 backdrop-blur-[1px]"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 py-12 w-full">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-7 space-y-6">
          @php $currentState = $setting->votingState(); @endphp
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-sm font-medium text-white">
            <span class="w-2 h-2 rounded-full {{ $currentState === 'berlangsung' ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
            <span>{{ ucfirst($currentState) }}</span>
            <span class="text-white/40">•</span>
            <span class="font-mono tnum">{{ $setting->tahun_ajaran }}</span>
          </div>

          <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white leading-[1.1]">
            Suaramu Menentukan Arah OSIS {{ $setting->nama_sekolah }}
          </h1>

          <p class="text-sm sm:text-base text-white/85 leading-relaxed max-w-xl">
            Portal resmi <strong>{{ $setting->nama_kegiatan }}</strong>. Pelajari gagasan, visi, dan misi setiap pasangan calon sebelum memberikan suara di TPS terdaftar Anda.
          </p>

          <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="button" onclick="openAppModal('panduan-modal')" class="px-5 py-3 bg-notion-blue text-white text-sm font-semibold rounded-lg hover:bg-notion-blueHover transition-colors inline-flex items-center gap-2 shadow-lg">
              <i data-lucide="compass" class="w-4 h-4"></i> Panduan
            </button>
            <a href="{{ route('quick-count') }}" class="px-5 py-3 bg-white text-notion-text text-sm font-semibold rounded-lg hover:bg-notion-hover transition-colors inline-flex items-center gap-2">
              <i data-lucide="bar-chart-2" class="w-4 h-4 text-notion-gray"></i> Quick Count
            </a>
          </div>
        </div>

        <div class="lg:col-span-5 bg-white/95 backdrop-blur-md border border-white/30 text-notion-text rounded-2xl p-6 shadow-xl space-y-4">
          <div class="flex items-center justify-between border-b border-notion-border pb-3">
            <span class="text-sm font-semibold text-notion-gray">Informasi Pelaksanaan</span>
            <i data-lucide="calendar-clock" class="w-4 h-4 text-notion-gray"></i>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-3.5 rounded-xl bg-notion-sidebar/80 border border-notion-border">
              <span class="text-sm text-notion-gray block">Total Pemilih</span>
              <span class="text-2xl font-bold text-notion-text tnum">{{ number_format($totalVoters, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200">
              <span class="text-sm text-notion-blue block">Suara Masuk</span>
              <span class="text-2xl font-bold text-notion-blue tnum">{{ number_format($votedCount, 0, ',', '.') }}</span>
            </div>
          </div>

          <div class="space-y-2 text-sm pt-1">
            <div class="flex items-center justify-between text-notion-gray">
              <span class="inline-flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4"></i> Jadwal Voting</span>
              <span class="font-mono text-notion-text tnum">{{ $setting->jadwal_mulai }}</span>
            </div>
            <div class="flex items-center justify-between text-notion-gray">
              <span class="inline-flex items-center gap-1.5"><i data-lucide="flag" class="w-4 h-4"></i> Batas Akhir</span>
              <span class="font-mono text-notion-text tnum">{{ $setting->jadwal_selesai }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('content')

  <section id="kandidat" class="space-y-5 scroll-mt-20">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 border-b border-notion-border pb-3">
      <div>
        <h2 class="text-2xl font-bold tracking-tight flex items-center gap-2">
          <i data-lucide="award" class="w-6 h-6 text-notion-blue"></i> Pasangan Calon Resmi
        </h2>
        <p class="text-sm text-notion-gray mt-1">Klik tombol Visi &amp; Misi pada masing-masing kandidat untuk membaca program kerja secara lengkap.</p>
      </div>
      <span class="px-3 py-1 rounded-md bg-notion-sidebar border border-notion-border text-sm font-mono text-notion-text tnum self-start sm:self-auto">
        {{ $candidates->count() }} Paslon Terdaftar
      </span>
    </div>

    @php
      $candCount = $candidates->count();
      $candGridCols = $candCount === 1 ? 'md:grid-cols-1' : ($candCount === 2 ? 'md:grid-cols-2' : 'md:grid-cols-3');
    @endphp
    <div class="grid grid-cols-1 {{ $candGridCols }} gap-6">
      @forelse($candidates as $cand)
        <article class="group border border-notion-border rounded-2xl bg-white overflow-hidden shadow-xs hover:shadow-md hover:border-notion-text/30 transition-all flex flex-col">
          <div class="h-56 bg-notion-sidebar border-b border-notion-border relative flex items-center justify-center overflow-hidden">
            @if($cand->foto_path)
              <img src="{{ asset('uploads/' . $cand->foto_path) }}" alt="{{ $cand->kode_paslon }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300">
            @else
              <div class="text-center space-y-1">
                <span class="text-7xl font-bold font-mono text-notion-text/15 tnum">0{{ $cand->nomor_urut }}</span>
                <p class="text-sm font-mono text-notion-gray">{{ $cand->kode_paslon }}</p>
              </div>
            @endif
            <div class="absolute top-3 left-3 flex items-center gap-1.5">
              <span class="px-3 py-1 rounded-full bg-notion-text text-white font-mono text-sm font-bold tnum shadow-sm">
                PASLON 0{{ $cand->nomor_urut }}
              </span>
            </div>
          </div>

          <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
            <div class="p-4 rounded-xl bg-notion-sidebar/60 border border-notion-border space-y-3">
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <span class="text-sm font-semibold text-notion-blue block">Calon Ketua OSIS</span>
                  <h3 class="text-base font-bold text-notion-text truncate">{{ $cand->ketua_nama }}</h3>
                </div>
                <span class="px-2.5 py-0.5 rounded bg-white border border-notion-border text-sm font-mono text-notion-gray shrink-0">{{ $cand->ketua_kelas }}</span>
              </div>

              <div class="border-t border-notion-border pt-3 flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <span class="text-sm font-semibold text-notion-gray block">Calon Wakil Ketua</span>
                  <p class="text-base font-bold text-notion-text truncate">{{ $cand->wakil_nama }}</p>
                </div>
                <span class="px-2.5 py-0.5 rounded bg-white border border-notion-border text-sm font-mono text-notion-gray shrink-0">{{ $cand->wakil_kelas }}</span>
              </div>
            </div>

            <button type="button" onclick='openVisiMisiModal(@json($cand))'
                    class="w-full py-2.5 px-4 bg-notion-text text-white text-sm font-medium rounded-lg hover:bg-notion-blue transition-colors flex items-center justify-center gap-2">
              <i data-lucide="book-open" class="w-4 h-4"></i> Lihat Visi &amp; Misi
            </button>
          </div>
        </article>
      @empty
        <div class="col-span-full border border-notion-border rounded-xl p-8 text-center text-sm text-notion-gray">
          Belum ada pasangan calon aktif yang dipublikasikan.
        </div>
      @endforelse
    </div>
  </section>

  <section id="lokasi-tps" class="space-y-5 pt-6 border-t border-notion-border">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
      <div>
        <h2 class="text-xl font-bold tracking-tight flex items-center gap-2">
          <i data-lucide="map-pin" class="w-5 h-5 text-notion-blue"></i> Lokasi TPS Resmi
        </h2>
        <p class="text-sm text-notion-gray mt-1">Pastikan Anda hadir di Tempat Pemungutan Suara (TPS) yang sesuai dengan kartu pemilih Anda.</p>
      </div>
      <span class="text-sm font-mono text-notion-gray tnum">{{ $tpsList->count() }} TPS Aktif</span>
    </div>

    @php
      $tpsCount = $tpsList->count();
      $tpsGridCols = $tpsCount === 1 ? 'sm:grid-cols-1' : ($tpsCount === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-2 lg:grid-cols-3');
    @endphp
    <div class="grid grid-cols-1 {{ $tpsGridCols }} gap-4">
      @forelse($tpsList as $tps)
        @php
          $tpsLabel = preg_replace('/^TPS\s*\d+\s*[—-]\s*/iu', '', $tps->nama_tps);
        @endphp
        <div class="border border-notion-border rounded-xl p-4 bg-notion-sidebar/40 space-y-2">
          <div class="flex items-center justify-between gap-2">
            <span class="px-2.5 py-0.5 rounded bg-white border border-notion-border font-mono text-sm font-semibold text-notion-blue">{{ $tps->kode_tps }}</span>
            <span class="text-sm font-mono text-notion-gray tnum">{{ $tps->voters_count }} DPT</span>
          </div>
          <h3 class="font-bold text-base text-notion-text">{{ $tpsLabel ?: $tps->nama_tps }}</h3>
          <p class="text-sm text-notion-gray flex items-center gap-1.5">
            <svg class="w-4 h-4 shrink-0 text-notion-gray" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>{{ $tps->lokasi ?? 'Area Sekolah' }}</span>
          </p>
        </div>
      @empty
        <div class="col-span-full border border-notion-border rounded-xl p-6 text-center text-sm text-notion-gray">
          Belum ada lokasi TPS yang diaktifkan.
        </div>
      @endforelse
    </div>
  </section>

  <section id="faq" class="space-y-5 pt-6 border-t border-notion-border">
    <div class="space-y-1">
      <h2 class="text-xl font-bold tracking-tight flex items-center gap-2">
        <i data-lucide="help-circle" class="w-5 h-5 text-notion-gray"></i> Pertanyaan Umum (FAQ)
      </h2>
      <p class="text-sm text-notion-gray">Klik pertanyaan di bawah ini untuk melihat penjelasan selengkapnya.</p>
    </div>

    <div class="border border-notion-border rounded-xl bg-white divide-y divide-notion-border overflow-hidden">
      @php
        $faqs = [
          [
            'q' => 'Apakah saya bisa memilih di TPS lain?',
            'a' => 'Tidak. Sistem membatasi pemilih hanya dapat masuk melalui bilik suara pada TPS yang tercatat di kartu pemilih (DPT) masing-masing.'
          ],
          [
            'q' => 'Apakah pilihan saya bersifat rahasia?',
            'a' => 'Ya. Suara yang masuk hanya dicatat perolehan dan kehadiran TPS-nya; panitia hanya memverifikasi kode bukti selesai memilih.'
          ],
          [
            'q' => 'Bagaimana jika segel token rusak atau data kartu tidak sesuai?',
            'a' => 'Segera lapor ke panitia TPS sebelum masuk ke bilik suara untuk dilakukan pengecekan ulang dan penggantian token oleh Administrator.'
          ],
          [
            'q' => 'Apa yang harus dilakukan setelah mengirim suara?',
            'a' => 'Simpan atau screenshot layar Bukti Telah Memilih beserta Kode Bukti, lalu tunjukkan kepada Panitia TPS sebelum meninggalkan area pemungutan suara.'
          ],
        ];
      @endphp

      @foreach($faqs as $idx => $faq)
        <div class="flex flex-col">
          <button type="button" onclick="toggleFaq({{ $idx }})" class="w-full p-4 text-left flex items-center justify-between gap-3 hover:bg-notion-hover/50 transition-colors">
            <span class="text-sm font-semibold text-notion-text">{{ $faq['q'] }}</span>
            <i data-lucide="chevron-down" id="faq-icon-{{ $idx }}" class="w-4 h-4 text-notion-gray transition-transform duration-200 shrink-0 {{ $idx === 0 ? 'rotate-180' : '' }}"></i>
          </button>
          <div id="faq-body-{{ $idx }}" class="{{ $idx === 0 ? '' : 'hidden' }} px-4 pb-4 pt-2 text-sm text-notion-gray leading-relaxed bg-notion-sidebar/30 border-t border-notion-border/60">
            {{ $faq['a'] }}
          </div>
        </div>
      @endforeach
    </div>
  </section>
@endsection

@push('modals')

  <div id="panduan-modal" class="hidden fixed inset-0 h-[100dvh] w-full z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('panduan-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-2xl max-w-lg w-full max-h-[85dvh] sm:max-h-[90dvh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
          <i data-lucide="compass" class="w-5 h-5 text-notion-blue"></i>
          <h3 class="font-bold text-lg text-notion-text">Panduan Pemilihan OSIS</h3>
        </div>
        <button type="button" onclick="closeAppModal('panduan-modal')" class="p-1.5 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="p-6 overflow-y-auto flex-1 min-h-0 space-y-3">
        @php
          $alur = [
            ['01', 'Datang & Terima Kartu', 'Hadir di TPS terdaftar, terima kartu pemilih, lalu pastikan data sesuai dan token masih tersegel.'],
            ['02', 'Masuk Bilik & Scan QR', 'Masuk ke bilik suara yang kosong dan scan QR Code pada kartu pemilih Anda.'],
            ['03', 'Input NIS, Token & Pilih', 'Buka segel token, masukkan NIS beserta token, lalu pilih pasangan calon OSIS.'],
            ['04', 'Tunjukkan Bukti ke Panitia', 'Simpan layar Bukti Telah Memilih dan tunjukkan kepada panitia TPS sebelum keluar.'],
          ];
        @endphp

        @foreach($alur as [$no, $judul, $desk])
          <div class="border border-notion-border rounded-xl p-4 bg-notion-sidebar/50 flex items-start gap-3">
            <div class="w-7 h-7 rounded-lg bg-white border border-notion-border flex items-center justify-center font-mono font-bold text-sm text-notion-blue shrink-0">{{ $no }}</div>
            <div>
              <h4 class="text-sm font-bold text-notion-text">{{ $judul }}</h4>
              <p class="text-sm text-notion-gray mt-0.5 leading-relaxed">{{ $desk }}</p>
            </div>
          </div>
        @endforeach
      </div>
      <div class="sticky bottom-0 z-10 bg-white px-6 py-4 pb-[max(1rem,env(safe-area-inset-bottom))] border-t border-notion-border flex items-center justify-between gap-2 shrink-0">
        <a href="{{ route('voting.index') }}" class="text-sm font-semibold text-notion-blue hover:underline inline-flex items-center gap-1">
          Buka Bilik Suara <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
        <button type="button" onclick="closeAppModal('panduan-modal')" class="px-4 py-2 bg-notion-text text-white text-sm font-medium rounded-lg hover:bg-black">Mengerti</button>
      </div>
    </div>
  </div>

  <div id="visi-misi-modal" class="hidden fixed inset-0 h-[100dvh] w-full z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('visi-misi-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-2xl max-w-lg w-full max-h-[85dvh] sm:max-h-[90dvh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="sticky top-0 z-10 bg-white px-6 py-4 border-b border-notion-border flex items-center justify-between shrink-0">
        <div>
          <span id="vm-kode" class="text-sm font-mono text-notion-blue font-semibold"></span>
          <h3 id="vm-title" class="font-bold text-lg text-notion-text"></h3>
          <p id="vm-kelas" class="text-sm text-notion-gray"></p>
        </div>
        <button type="button" onclick="closeAppModal('visi-misi-modal')" class="p-1.5 text-notion-gray hover:text-notion-text rounded-md hover:bg-notion-hover"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="p-6 overflow-y-auto overflow-x-hidden flex-1 min-h-0 space-y-5">
        <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200 space-y-1.5 min-w-0">
          <p class="text-sm font-bold text-notion-blue">Visi</p>
          <div id="vm-visi" class="text-sm text-notion-text leading-relaxed font-medium break-words whitespace-normal min-w-0 [overflow-wrap:anywhere]"></div>
        </div>
        <div class="space-y-2.5 min-w-0">
          <p class="text-sm font-bold text-notion-gray flex items-center gap-1.5">
            <i data-lucide="list-checks" class="w-4 h-4"></i> Misi &amp; Program
          </p>
          <div id="vm-misi" class="text-sm text-notion-text leading-relaxed space-y-2 break-words whitespace-normal min-w-0 [overflow-wrap:anywhere]"></div>
        </div>
      </div>
      <div class="sticky bottom-0 z-10 bg-white px-6 py-4 pb-[max(1rem,env(safe-area-inset-bottom))] border-t border-notion-border flex items-center justify-between gap-2 shrink-0">
        <a href="{{ route('voting.index') }}" class="text-sm font-semibold text-notion-blue hover:underline inline-flex items-center gap-1">
          Lanjut ke Bilik Suara <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
        <button type="button" onclick="closeAppModal('visi-misi-modal')" class="px-4 py-2 bg-notion-text text-white text-sm font-medium rounded-lg hover:bg-black">Tutup</button>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
  let openFaqIdx = 0;
  function toggleFaq(idx) {
    const body = document.getElementById(`faq-body-${idx}`);
    const icon = document.getElementById(`faq-icon-${idx}`);
    if (!body) return;

    if (openFaqIdx !== null && openFaqIdx !== idx) {
      document.getElementById(`faq-body-${openFaqIdx}`)?.classList.add('hidden');
      document.getElementById(`faq-icon-${openFaqIdx}`)?.classList.remove('rotate-180');
    }

    const isOpen = !body.classList.contains('hidden');
    body.classList.toggle('hidden', isOpen);
    icon?.classList.toggle('rotate-180', !isOpen);
    openFaqIdx = isOpen ? null : idx;
  }

  function openVisiMisiModal(cand) {
    const escHtml = (s) => String(s || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    document.getElementById('vm-kode').textContent = cand.kode_paslon || `PASLON 0${cand.nomor_urut}`;
    document.getElementById('vm-title').innerHTML = `<span class="block leading-snug">${escHtml(cand.ketua_nama)}</span><span class="block text-sm font-semibold text-notion-gray mt-0.5 leading-snug">&amp; ${escHtml(cand.wakil_nama)}</span>`;
    document.getElementById('vm-kelas').textContent = `Ketua (${cand.ketua_kelas}) · Wakil (${cand.wakil_kelas})`;

    const cleanHtmlSpaces = (str) => (str || '').replace(/&nbsp;|\u00A0/g, ' ');
    const visiEl = document.getElementById('vm-visi');
    const rawVisi = cleanHtmlSpaces(cand.visi);
    if (rawVisi && /<[a-z][\s\S]*>/i.test(rawVisi)) {
      visiEl.innerHTML = rawVisi;
    } else {
      visiEl.textContent = rawVisi;
    }

    const misiBox = document.getElementById('vm-misi');
    const rawMisi = cleanHtmlSpaces(cand.misi);
    if (/<[a-z][\s\S]*>/i.test(rawMisi)) {
      misiBox.innerHTML = `<div class="space-y-2 break-words [overflow-wrap:anywhere] [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:space-y-1.5 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1.5">${rawMisi}</div>`;
    } else {
      const lines = rawMisi.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
      misiBox.innerHTML = lines.map((line, idx) => {
        const match = line.match(/^(\d+[\.\)]?)\s*(.+)$/);
        const num = match ? (match[1].endsWith('.') ? match[1] : `${match[1]}.`) : `${idx + 1}.`;
        const text = (match ? match[2] : line).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return `
          <div class="p-3.5 rounded-lg bg-notion-sidebar/60 border border-notion-border text-sm text-notion-text flex items-start gap-2.5">
            <span class="font-mono font-bold text-notion-blue shrink-0">${num}</span>
            <span class="leading-relaxed">${text}</span>
          </div>
        `;
      }).join('');
    }

    openAppModal('visi-misi-modal');
  }
</script>
@endpush
