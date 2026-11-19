@extends('layouts.frontend')

@section('title', 'Bilik Suara Digital')
@section('meta_robots', 'noindex, nofollow')

@section('content')
  @php
    $currentStep = $receipt ? 3 : ($activeVoter ? 2 : 1);
  @endphp

  <div class="w-full max-w-[1000px] mx-auto space-y-8">
  <section class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-notion-border pb-6">
    <div class="space-y-2">
      <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-notion-sidebar border border-notion-border text-sm font-medium text-notion-gray">
        <i data-lucide="vote" class="w-4 h-4 text-notion-blue"></i>
        <span>{{ $activeVoter?->tps?->nama_tps ?? 'Lokasi TPS Anda' }}</span>
      </div>
      <h1 class="text-3xl font-bold tracking-tight text-notion-text">Bilik Suara Digital</h1>
      <p class="text-sm text-notion-gray">
        TPS pemilih otomatis terkunci sesuai data Daftar Pemilih Tetap (DPT) Anda.
      </p>
    </div>

    <div class="flex items-center gap-2 sm:gap-3 text-xs font-medium bg-notion-sidebar/70 border border-notion-border rounded-xl px-3.5 py-2.5 shrink-0">
      <div class="flex items-center gap-1.5 {{ $currentStep === 1 ? 'text-notion-blue font-bold' : ($currentStep > 1 ? 'text-emerald-700' : 'text-notion-gray') }}">
        <span class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-mono {{ $currentStep === 1 ? 'bg-notion-blue text-white' : ($currentStep > 1 ? 'bg-emerald-600 text-white' : 'bg-notion-border text-notion-gray') }}">1</span>
        <span>Verifikasi</span>
      </div>
      <span class="text-notion-border">/</span>
      <div class="flex items-center gap-1.5 {{ $currentStep === 2 ? 'text-notion-blue font-bold' : ($currentStep > 2 ? 'text-emerald-700' : 'text-notion-gray') }}">
        <span class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-mono {{ $currentStep === 2 ? 'bg-notion-blue text-white' : ($currentStep > 2 ? 'bg-emerald-600 text-white' : 'bg-notion-border text-notion-gray') }}">2</span>
        <span>Surat Suara</span>
      </div>
      <span class="text-notion-border">/</span>
      <div class="flex items-center gap-1.5 {{ $currentStep === 3 ? 'text-emerald-700 font-bold' : 'text-notion-gray' }}">
        <span class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-mono {{ $currentStep === 3 ? 'bg-emerald-600 text-white' : 'bg-notion-border text-notion-gray' }}">3</span>
        <span>Bukti Sah</span>
      </div>
    </div>
  </section>

  @if($receipt)

    <div class="w-full bg-white border border-notion-border rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 text-center space-y-2.5 bg-emerald-50/40 border-b border-dashed border-notion-border">
        <div class="w-14 h-14 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 flex items-center justify-center mx-auto">
          <i data-lucide="check-circle-2" class="w-8 h-8"></i>
        </div>
        <span class="inline-block px-3 py-0.5 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800 tracking-wide">
          SUARA TERCATAT SAH
        </span>
        <h2 class="text-2xl font-bold tracking-tight text-notion-text">Terima Kasih, {{ $receipt['nama'] }}!</h2>
        <p class="text-sm text-notion-gray">Hak pilih Anda telah berhasil direkam secara rahasia.</p>
      </div>

      <div class="p-6 space-y-4">
        <div class="p-4 rounded-xl bg-notion-sidebar border border-notion-border text-center space-y-1">
          <p class="text-sm uppercase tracking-wider text-notion-gray font-semibold">Kode Bukti Pemilih</p>
          <p class="text-2xl font-mono font-bold text-notion-blue tnum tracking-widest">{{ $receipt['receipt_code'] }}</p>
          <p class="text-sm font-mono text-notion-gray tnum pt-0.5">{{ $receipt['nis'] }} · {{ $receipt['kelas'] }} · {{ $receipt['kode_tps'] }}</p>
          <p class="text-sm font-mono text-notion-gray tnum">{{ $receipt['voted_at'] }}</p>
        </div>

        <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 flex items-start gap-3">
          <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-700 shrink-0 mt-0.5"></i>
          <div class="space-y-1">
            <p class="text-sm font-bold text-amber-900">Tunjukkan ke Panitia TPS!</p>
            <p class="text-sm text-amber-800 leading-relaxed">
              Harap perlihatkan layar bukti ini (<span class="font-mono font-semibold">{{ $receipt['receipt_code'] }}</span>) kepada Panitia TPS sebelum meninggalkan area pemilihan.
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          <button type="button" onclick="downloadReceiptJpg()" class="w-full py-3 px-4 bg-notion-blue text-white text-sm font-semibold rounded-xl hover:bg-notion-blueHover transition-colors flex items-center justify-center gap-2">
            <i data-lucide="image-down" class="w-4 h-4"></i> Simpan Bukti (JPG)
          </button>
          <a href="{{ route('voting.index') }}" class="w-full py-3 px-4 bg-notion-text text-white text-sm font-semibold rounded-xl hover:bg-black transition-colors flex items-center justify-center gap-2">
            <i data-lucide="check" class="w-4 h-4"></i> Selesai &amp; Tutup
          </a>
        </div>
      </div>
    </div>
  @elseif(!$isElectionOpen)

    <div class="w-full border border-notion-border rounded-2xl bg-white p-6 sm:p-8 shadow-sm text-center space-y-5">
      <div class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center {{ $electionState === 'selesai' ? 'bg-notion-hover text-notion-text' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
        <i data-lucide="{{ $electionState === 'selesai' ? 'lock' : 'clock' }}" class="w-7 h-7"></i>
      </div>
      <div class="space-y-1.5">
        <h2 class="text-xl font-bold text-notion-text">
          {{ $electionState === 'selesai' ? 'Pemilihan OSIS Telah Selesai' : 'Pemilihan OSIS Belum Dimulai' }}
        </h2>
        <p class="text-sm text-notion-gray leading-relaxed">
          @if($electionState === 'selesai')
            Bilik suara telah resmi ditutup. Terima kasih atas partisipasi seluruh warga {{ $setting->nama_sekolah }}.
          @else
            Bilik suara belum dibuka oleh panitia. Pemungutan suara dijadwalkan pada <span class="font-mono text-notion-text">{{ $setting->jadwal_mulai }}</span> s/d <span class="font-mono text-notion-text">{{ $setting->jadwal_selesai }}</span>.
          @endif
        </p>
      </div>
      <div class="flex flex-col sm:flex-row gap-2.5 pt-2">
        <a href="{{ route('home') }}" class="flex-1 py-2.5 px-4 bg-white border border-notion-border text-notion-text text-sm font-medium rounded-lg hover:bg-notion-hover transition-colors">
          Lihat Kandidat
        </a>
        <a href="{{ route('quick-count') }}" class="flex-1 py-2.5 px-4 bg-notion-blue text-white text-sm font-medium rounded-lg hover:bg-notion-blueHover transition-colors">
          Quick Count
        </a>
      </div>
    </div>
  @elseif(!$activeVoter)

    <div class="w-full space-y-4">
      <div class="bg-white border border-notion-border rounded-2xl p-6 shadow-sm space-y-5">
        <div class="space-y-1">
          <h2 class="text-xl font-bold tracking-tight">Verifikasi Pemilih</h2>
          <p class="text-sm text-notion-gray leading-relaxed">
            Masukkan NIS dan Token dari kartu pemilih Anda.
          </p>
        </div>

        <form action="{{ route('voting.verify') }}" method="POST" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium mb-1.5">Nomor Induk Siswa (NIS)</label>
            <div class="relative">
              <i data-lucide="user" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
              <input type="text" inputmode="numeric" name="nis" id="booth-nis" value="{{ old('nis') }}" required placeholder="Contoh: 202601004"
                     class="w-full border border-notion-border rounded-xl pl-10 pr-4 py-3 text-base font-mono tnum bg-white focus:outline-none focus:border-notion-blue">
            </div>
          </div>

          @if($setting->require_token)
            <div>
              <label class="block text-sm font-medium mb-1.5">Token Kartu Pemilih</label>
              <div class="relative">
                <i data-lucide="key-round" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-notion-gray pointer-events-none"></i>
                <input type="password" name="token" id="booth-token" required placeholder="6 digit token..."
                       class="w-full border border-notion-border rounded-xl pl-10 pr-12 py-3 text-base font-mono uppercase bg-white focus:outline-none focus:border-notion-blue">
                <button type="button" onclick="togglePassword('booth-token', this)" class="absolute inset-y-0 right-0 px-3.5 flex items-center text-notion-gray hover:text-notion-text">
                  <i data-lucide="eye" class="w-5 h-5"></i>
                </button>
              </div>
            </div>
          @endif

          <button type="submit" class="w-full py-3.5 px-4 bg-notion-blue text-white text-base font-semibold rounded-xl hover:bg-notion-blueHover active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-sm">
            <i data-lucide="shield-check" class="w-5 h-5"></i> Buka Surat Suara
          </button>
        </form>
      </div>

      @if(auth()->user()?->isAdmin() && $demoVoters->isNotEmpty())
        <div class="bg-white border border-notion-border rounded-2xl p-4 space-y-2.5">
          <p class="text-sm font-semibold text-notion-gray flex items-center gap-1.5">
            <i data-lucide="sparkles" class="w-4 h-4 text-notion-blue"></i> Mode Admin — Uji Cepat Pemilih {{ $activeTps?->kode_tps }}
          </p>
          <div class="space-y-1.5">
            @foreach($demoVoters as $dv)
              <button type="button" onclick="fillDemoVoter('{{ $dv->nis }}', '{{ $dv->token }}')"
                      class="w-full text-left px-3 py-2 rounded-xl bg-notion-sidebar/60 border border-notion-border hover:border-notion-blue flex items-center justify-between text-sm">
                <span class="font-medium truncate">{{ $dv->nama }} ({{ $dv->kelas }})</span>
                <span class="font-mono text-notion-blue font-semibold shrink-0 ml-2">{{ $dv->nis }} / {{ $dv->token }}</span>
              </button>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  @else

    <div class="space-y-6 pb-20">
      <div class="bg-blue-50/60 border border-blue-200 rounded-2xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-full bg-notion-blue text-white flex items-center justify-center font-bold text-sm shrink-0">
            {{ strtoupper(substr($activeVoter->nama, 0, 2)) }}
          </div>
          <div class="min-w-0">
            <p class="text-sm font-bold text-notion-text truncate">{{ $activeVoter->nama }}</p>
            <p class="text-sm font-mono text-notion-gray tnum">{{ $activeVoter->nis }} · {{ $activeVoter->kelas }} · {{ $activeVoter->tps?->kode_tps }}</p>
          </div>
        </div>
        <form action="{{ route('voting.reset') }}" method="POST" class="shrink-0">
          @csrf
          <button type="submit" class="px-3 py-1.5 rounded-lg bg-white border border-blue-200 text-sm font-medium text-notion-text hover:bg-notion-hover">
            Batal
          </button>
        </form>
      </div>

      @php
        $voteCandCount = $candidates->count();
        $voteCandGrid = $voteCandCount === 1 ? 'md:grid-cols-1' : ($voteCandCount === 2 ? 'md:grid-cols-2' : 'md:grid-cols-3');
      @endphp
      <div class="grid grid-cols-1 {{ $voteCandGrid }} gap-6">
        @foreach($candidates as $cand)
          <article onclick="selectBallotCard({{ $cand->id }}, 'Paslon 0{{ $cand->nomor_urut }} — {{ addslashes($cand->ketua_nama) }} & {{ addslashes($cand->wakil_nama) }}')"
                   id="ballot-card-{{ $cand->id }}"
                   class="ballot-card group cursor-pointer bg-white border-2 border-notion-border rounded-2xl overflow-hidden shadow-xs hover:shadow-md hover:border-notion-text/30 transition-all flex flex-col">
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
              <span id="ballot-check-{{ $cand->id }}" class="ballot-check hidden absolute top-3 right-3 w-8 h-8 rounded-full bg-notion-blue text-white flex items-center justify-center shadow">
                <i data-lucide="check" class="w-5 h-5"></i>
              </span>
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

              <button type="button" onclick='event.stopPropagation(); openVisiMisiModal(@json($cand))'
                      class="w-full py-2.5 px-4 bg-notion-text text-white text-sm font-medium rounded-lg hover:bg-notion-blue transition-colors flex items-center justify-center gap-2">
                <i data-lucide="book-open" class="w-4 h-4"></i> Lihat Visi &amp; Misi
              </button>
            </div>
          </article>
        @endforeach
      </div>
    </div>

    <div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-notion-border p-3.5">
      <div class="w-full max-w-[1000px] mx-auto flex items-center justify-between gap-3">
        <div class="min-w-0">
          <p class="text-sm text-notion-gray">Pilihan Anda:</p>
          <p id="selected-ballot-label" class="text-sm font-bold text-notion-text truncate">Belum ada paslon dipilih</p>
        </div>
        <button type="button" id="submit-ballot-btn" disabled onclick="openConfirmVoteModal()"
                class="px-5 py-3 rounded-xl bg-notion-blue text-white text-sm font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-notion-blueHover transition-all shrink-0 inline-flex items-center gap-2">
          <i data-lucide="send" class="w-4 h-4"></i> Kirim Pilihan
        </button>
      </div>
    </div>
  @endif
@endsection

@push('modals')

  @if(!$isElectionOpen && !$receipt)
    <div id="election-closed-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('election-closed-modal')">
      <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-2xl max-w-md w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
        <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
        <div class="p-6 text-center space-y-4">
          <div class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center {{ $electionState === 'selesai' ? 'bg-notion-hover text-notion-text' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
            <i data-lucide="{{ $electionState === 'selesai' ? 'lock' : 'clock' }}" class="w-7 h-7"></i>
          </div>
          <div class="space-y-1.5">
            <h3 class="font-bold text-xl text-notion-text">
              {{ $electionState === 'selesai' ? 'Pemilihan Telah Selesai' : 'Pemilihan Belum Dimulai' }}
            </h3>
            <p class="text-sm text-notion-gray leading-relaxed">
              @if($electionState === 'selesai')
                Pemungutan suara OSIS telah ditutup. Silakan pantau hasil perolehan suara pada halaman Quick Count.
              @else
                Bilik suara belum dibuka. Jadwal pemungutan suara: <span class="font-mono text-notion-text">{{ $setting->jadwal_mulai }}</span> s/d <span class="font-mono text-notion-text">{{ $setting->jadwal_selesai }}</span>.
              @endif
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-2 pt-2">
            <a href="{{ route('home') }}" class="flex-1 py-2.5 px-4 bg-white border border-notion-border text-notion-text text-sm font-medium rounded-lg hover:bg-notion-hover">Beranda</a>
            <a href="{{ route('quick-count') }}" class="flex-1 py-2.5 px-4 bg-notion-blue text-white text-sm font-medium rounded-lg hover:bg-notion-blueHover">Quick Count</a>
          </div>
        </div>
      </div>
    </div>
  @endif
  </div>

  <div id="confirm-vote-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('confirm-vote-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-2xl max-w-md w-full flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
      <div class="sm:hidden pt-2.5 pb-1 flex justify-center shrink-0 bg-white"><div class="w-10 h-1.5 bg-notion-border rounded-full"></div></div>
      <div class="px-6 py-4 border-b border-notion-border flex items-center justify-between">
        <h3 class="font-bold text-lg">Konfirmasi Pilihan</h3>
        <button type="button" onclick="closeAppModal('confirm-vote-modal')" class="p-1 text-notion-gray hover:text-notion-text rounded-md"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <form action="{{ route('voting.cast') }}" method="POST" class="p-6 space-y-4">
        @csrf
        <input type="hidden" name="candidate_id" id="vote-candidate-id">
        <p class="text-sm text-notion-gray">Anda yakin ingin mengirim suara untuk pasangan calon berikut? Pilihan tidak dapat diubah setelah dikirim.</p>
        <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200">
          <p id="vote-candidate-label" class="font-bold text-sm text-notion-text"></p>
        </div>
        <div class="grid grid-cols-2 gap-2.5 pt-2">
          <button type="button" onclick="closeAppModal('confirm-vote-modal')" class="py-3 text-sm font-medium text-notion-gray bg-notion-sidebar hover:bg-notion-hover rounded-xl">Batal</button>
          <button type="submit" class="py-3 bg-notion-blue text-white text-sm font-semibold rounded-xl hover:bg-notion-blueHover">Ya, Kirim Suara</button>
        </div>
      </form>
    </div>
  </div>

  <div id="visi-misi-modal" class="hidden fixed inset-0 w-screen h-screen z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-200" onclick="closeAppModal('visi-misi-modal')">
    <div onclick="event.stopPropagation()" class="bg-white border border-notion-border rounded-t-2xl sm:rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-xl transform translate-y-6 sm:translate-y-0 sm:scale-95 transition-all duration-200 overflow-hidden">
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
      <div class="sticky bottom-0 z-10 bg-white px-6 py-4 border-t border-notion-border flex items-center justify-end gap-2 shrink-0">
        <button type="button" onclick="closeAppModal('visi-misi-modal')" class="px-4 py-2 bg-notion-text text-white text-sm font-medium rounded-lg hover:bg-black">Tutup</button>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
  let selectedCandidateId = null;
  let selectedCandidateLabel = '';

  function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.innerHTML = `<i data-lucide="${isHidden ? 'eye-off' : 'eye'}" class="w-5 h-5"></i>`;
    lucide.createIcons();
  }

  function fillDemoVoter(nis, token) {
    document.getElementById('booth-nis').value = nis;
    const tok = document.getElementById('booth-token');
    if (tok) tok.value = token;
    showToast(`Data uji ${nis} diisikan.`, 'info');
  }

  function selectBallotCard(id, label) {
    selectedCandidateId = id;
    selectedCandidateLabel = label;

    document.querySelectorAll('.ballot-card').forEach((card) => {
      card.classList.remove('border-notion-blue', 'bg-blue-50/20', 'ring-2', 'ring-notion-blue/20');
      card.classList.add('border-notion-border');
    });
    document.querySelectorAll('.ballot-check').forEach((chk) => chk.classList.add('hidden'));

    const activeCard = document.getElementById(`ballot-card-${id}`);
    const activeCheck = document.getElementById(`ballot-check-${id}`);
    activeCard?.classList.remove('border-notion-border');
    activeCard?.classList.add('border-notion-blue', 'bg-blue-50/20', 'ring-2', 'ring-notion-blue/20');
    activeCheck?.classList.remove('hidden');

    document.getElementById('selected-ballot-label').textContent = label;
    const btn = document.getElementById('submit-ballot-btn');
    if (btn) btn.disabled = false;
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

  function openConfirmVoteModal() {
    if (!selectedCandidateId) return;
    document.getElementById('vote-candidate-id').value = selectedCandidateId;
    document.getElementById('vote-candidate-label').textContent = selectedCandidateLabel;
    openAppModal('confirm-vote-modal');
  }

  @if($receipt)
    function downloadReceiptJpg() {
      const receipt = @json($receipt);
      const schoolName = @json($setting->nama_sekolah);
      const activityName = @json($setting->nama_kegiatan);

      const scale = 2;
      const w = 640;
      const h = 470;
      const canvas = document.createElement('canvas');
      canvas.width = w * scale;
      canvas.height = h * scale;
      const ctx = canvas.getContext('2d');
      ctx.scale(scale, scale);

      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, w, h);

      ctx.strokeStyle = '#e5e5e5';
      ctx.lineWidth = 2;
      ctx.strokeRect(16, 16, w - 32, h - 32);

      ctx.fillStyle = '#ecfdf5';
      ctx.fillRect(17, 17, w - 34, 110);

      ctx.fillStyle = '#d1fae5';
      ctx.fillRect(w / 2 - 90, 32, 180, 26);
      ctx.fillStyle = '#065f46';
      ctx.font = 'bold 12px sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText('SUARA TERCATAT SAH', w / 2, 49);

      ctx.fillStyle = '#222222';
      ctx.font = 'bold 20px sans-serif';
      ctx.fillText('BUKTI TELAH MEMILIH', w / 2, 84);
      ctx.fillStyle = '#666666';
      ctx.font = '13px sans-serif';
      ctx.fillText(`${activityName} — ${schoolName}`, w / 2, 107);

      ctx.setLineDash([6, 4]);
      ctx.strokeStyle = '#d4d4d4';
      ctx.beginPath();
      ctx.moveTo(32, 140);
      ctx.lineTo(w - 32, 140);
      ctx.stroke();
      ctx.setLineDash([]);

      ctx.fillStyle = '#f7f7f5';
      ctx.fillRect(48, 160, w - 96, 84);
      ctx.strokeStyle = '#e5e5e5';
      ctx.lineWidth = 1.5;
      ctx.strokeRect(48, 160, w - 96, 84);

      ctx.fillStyle = '#666666';
      ctx.font = 'bold 11px sans-serif';
      ctx.fillText('KODE BUKTI PEMILIH', w / 2, 184);

      ctx.fillStyle = '#2563eb';
      ctx.font = 'bold 30px monospace';
      ctx.fillText(receipt.receipt_code || '-', w / 2, 224);

      ctx.textAlign = 'left';
      const rows = [
        ['Nama Pemilih', receipt.nama || '-'],
        ['NIS / Kelas', `${receipt.nis || '-'}  ·  ${receipt.kelas || '-'}`],
        ['TPS Terdaftar', receipt.tps || receipt.kode_tps || '-'],
        ['Waktu Selesai', receipt.voted_at || '-'],
      ];
      let y = 278;
      rows.forEach(([label, val]) => {
        ctx.fillStyle = '#666666';
        ctx.font = '13px sans-serif';
        ctx.fillText(label, 52, y);
        ctx.fillStyle = '#222222';
        ctx.font = 'bold 13px sans-serif';
        ctx.textAlign = 'right';
        ctx.fillText(String(val), w - 52, y);
        ctx.textAlign = 'left';
        y += 28;
      });

      ctx.fillStyle = '#fffbeb';
      ctx.fillRect(48, 392, w - 96, 42);
      ctx.strokeStyle = '#fcd34d';
      ctx.strokeRect(48, 392, w - 96, 42);
      ctx.fillStyle = '#92400e';
      ctx.font = 'bold 12px sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText('Tunjukkan bukti ini kepada Panitia TPS sebelum keluar area.', w / 2, 417);

      const link = document.createElement('a');
      link.download = `bukti-memilih-${receipt.nis || 'osis'}.jpg`;
      link.href = canvas.toDataURL('image/jpeg', 0.95);
      document.body.appendChild(link);
      link.click();
      link.remove();
      showToast('Bukti berhasil disimpan sebagai gambar JPG.', 'success');
    }
  @endif

  @if(!$isElectionOpen && !$receipt)
    document.addEventListener('DOMContentLoaded', () => {
      openAppModal('election-closed-modal');
    });
  @endif
</script>
@endpush
