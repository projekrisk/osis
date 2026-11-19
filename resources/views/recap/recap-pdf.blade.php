<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekapitulasi Lengkap Pemilihan OSIS</title>
  <style>
    @font-face {
      font-family: 'Bricolage Grotesque';
      font-style: normal;
      font-weight: 400;
      src: url('{{ storage_path('fonts/BricolageGrotesque-Regular.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'Bricolage Grotesque';
      font-style: normal;
      font-weight: 700;
      src: url('{{ storage_path('fonts/BricolageGrotesque-Bold.ttf') }}') format('truetype');
    }
    @page { margin: 15mm 14mm; }
    body {
      font-family: 'Bricolage Grotesque', DejaVu Sans, Arial, sans-serif;
      font-size: 10px;
      color: #222222;
      line-height: 1.4;
      margin: 0;
    }
    .sheet {
      page-break-after: always;
    }
    .sheet:last-child {
      page-break-after: auto;
    }
    .header-wrap {
      width: 100%;
      border-collapse: collapse;
      border-bottom: 2px solid #222222;
      margin-bottom: 12px;
    }
    .header-wrap td {
      padding-bottom: 8px;
      vertical-align: middle;
    }
    .header-title {
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
      margin: 0 0 2px 0;
    }
    .header-sub {
      font-size: 11.5px;
      font-weight: bold;
      color: #444444;
      margin: 0 0 3px 0;
    }
    .header-meta {
      font-size: 9.5px;
      color: #555555;
      margin: 0;
    }
    .header-logo {
      width: 52px;
      height: 52px;
      object-fit: contain;
    }
    .summary-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
    }
    .summary-table td {
      border: 1px solid #e5e5e5;
      background-color: #f7f7f5;
      padding: 6px 10px;
      width: 25%;
    }
    .summary-label {
      font-size: 8.5px;
      color: #666666;
      text-transform: uppercase;
      display: block;
      margin-bottom: 2px;
    }
    .summary-value {
      font-size: 12px;
      font-weight: bold;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }
    .data-table th {
      background-color: #f7f7f5;
      border: 1px solid #d4d4d4;
      padding: 6px 8px;
      text-align: left;
      font-size: 9.5px;
      font-weight: bold;
    }
    .data-table td {
      border: 1px solid #e5e5e5;
      padding: 5.5px 8px;
      font-size: 10px;
      vertical-align: middle;
    }
    .data-table tr:nth-child(even) td {
      background-color: #fcfcfc;
    }
    .mono { font-family: DejaVu Sans Mono, monospace; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .signatures {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    .signatures td {
      width: 50%;
      text-align: center;
      vertical-align: top;
      font-size: 10px;
    }
    .sign-space { height: 48px; }
  </style>
</head>
<body>
  @php
    $logoFile = $setting->logo_path ? base_path('uploads/' . $setting->logo_path) : null;
    $hasLogo = $logoFile && file_exists($logoFile);
  @endphp

  <div class="sheet">
    <table class="header-wrap">
      <tr>
        <td>
          <p class="header-title">Hasil Perolehan Suara Pasangan Calon</p>
          <p class="header-sub">{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
          <p class="header-meta">
            <strong>Jadwal:</strong> {{ $setting->jadwal_mulai }} s/d {{ $setting->jadwal_selesai }}
            &nbsp;|&nbsp; <strong>Status:</strong> {{ ucfirst($setting->votingState()) }}
          </p>
        </td>
        @if($hasLogo)
          <td style="width: 68px; text-align: right;">
            <img src="{{ $logoFile }}" alt="Logo" class="header-logo">
          </td>
        @endif
      </tr>
    </table>

    <table class="summary-table">
      <tr>
        <td>
          <span class="summary-label">Total DPT</span>
          <span class="summary-value">{{ $totalVoters }}</span>
        </td>
        <td>
          <span class="summary-label">Suara Masuk</span>
          <span class="summary-value" style="color: #2563eb;">{{ $votedCount }}</span>
        </td>
        <td>
          <span class="summary-label">Partisipasi</span>
          <span class="summary-value" style="color: #0f7b6c;">{{ $participationRate }}%</span>
        </td>
        <td>
          <span class="summary-label">Belum Memilih</span>
          <span class="summary-value" style="color: #b45309;">{{ $notVotedCount }}</span>
        </td>
      </tr>
    </table>

    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 52px;">Peringkat</th>
          <th class="text-center" style="width: 52px;">No.</th>
          <th>Pasangan Calon (Ketua &amp; Wakil)</th>
          <th class="text-right" style="width: 78px;">Suara</th>
          <th class="text-right" style="width: 72px;">Persen</th>
        </tr>
      </thead>
      <tbody>
        @foreach($candidates as $rank => $c)
          @php $pct = $totalVotes > 0 ? round(($c->votes_count / $totalVotes) * 100, 1) : 0; @endphp
          <tr>
            <td class="text-center mono"><strong>#{{ $rank + 1 }}</strong></td>
            <td class="text-center mono">0{{ $c->nomor_urut }}</td>
            <td>
              <div><strong>Ketua: {{ $c->ketua_nama }}</strong> <span style="color:#666666;">({{ $c->ketua_kelas }})</span></div>
              <div style="color:#444444; margin-top:2px;">Wakil: {{ $c->wakil_nama }} <span style="color:#666666;">({{ $c->wakil_kelas }})</span></div>
            </td>
            <td class="text-right mono"><strong>{{ $c->votes_count }}</strong></td>
            <td class="text-right mono" style="color: #2563eb;"><strong>{{ $pct }}%</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <table class="signatures">
      <tr>
        <td>
          Mengetahui,<br>
          <strong>Pembina OSIS</strong>
          <div class="sign-space"></div>
          ........................................
        </td>
        <td>
          {{ $setting->nama_sekolah }}, {{ $tanggalIndo }}<br>
          <strong>Ketua Panitia Pemilihan OSIS</strong>
          <div class="sign-space"></div>
          ........................................
        </td>
      </tr>
    </table>
  </div>

  <div class="sheet">
    <table class="header-wrap">
      <tr>
        <td>
          <p class="header-title">Rekapitulasi Partisipasi per Kelas</p>
          <p class="header-sub">{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
          <p class="header-meta"><strong>Dicetak:</strong> {{ $tanggalIndo }}</p>
        </td>
        @if($hasLogo)
          <td style="width: 68px; text-align: right;">
            <img src="{{ $logoFile }}" alt="Logo" class="header-logo">
          </td>
        @endif
      </tr>
    </table>

    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 36px;">No</th>
          <th>Kelas / Unit</th>
          <th class="text-right" style="width: 75px;">Total DPT</th>
          <th class="text-right" style="width: 80px;">Memilih</th>
          <th class="text-right" style="width: 80px;">Belum</th>
          <th class="text-right" style="width: 75px;">Persen</th>
        </tr>
      </thead>
      <tbody>
        @foreach($kelasStats as $idx => $ks)
          @php $kpct = $ks->total_dpt > 0 ? round(($ks->sudah_memilih / $ks->total_dpt) * 100, 1) : 0; @endphp
          <tr>
            <td class="text-center mono">{{ $idx + 1 }}</td>
            <td><strong>{{ $ks->kelas }}</strong></td>
            <td class="text-right mono">{{ $ks->total_dpt }}</td>
            <td class="text-right mono" style="color: #0f7b6c;">{{ $ks->sudah_memilih }}</td>
            <td class="text-right mono" style="color: #b45309;">{{ $ks->total_dpt - $ks->sudah_memilih }}</td>
            <td class="text-right mono" style="color: #2563eb;"><strong>{{ $kpct }}%</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <table class="signatures">
      <tr>
        <td>
          Mengetahui,<br>
          <strong>Pembina OSIS</strong>
          <div class="sign-space"></div>
          ........................................
        </td>
        <td>
          {{ $setting->nama_sekolah }}, {{ $tanggalIndo }}<br>
          <strong>Ketua Panitia Pemilihan OSIS</strong>
          <div class="sign-space"></div>
          ........................................
        </td>
      </tr>
    </table>
  </div>

  <div class="sheet">
    <table class="header-wrap">
      <tr>
        <td>
          <p class="header-title">Rekapitulasi Partisipasi per TPS</p>
          <p class="header-sub">{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
          <p class="header-meta"><strong>Dicetak:</strong> {{ $tanggalIndo }}</p>
        </td>
        @if($hasLogo)
          <td style="width: 68px; text-align: right;">
            <img src="{{ $logoFile }}" alt="Logo" class="header-logo">
          </td>
        @endif
      </tr>
    </table>

    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 75px;">Kode</th>
          <th>Nama TPS</th>
          <th>Lokasi</th>
          <th class="text-right" style="width: 68px;">DPT</th>
          <th class="text-right" style="width: 68px;">Hadir</th>
          <th class="text-right" style="width: 70px;">Persen</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tpsStats as $tps)
          @php $tpct = $tps->total_dpt > 0 ? round(($tps->sudah_memilih / $tps->total_dpt) * 100, 1) : 0; @endphp
          <tr>
            <td class="mono">{{ $tps->kode_tps }}</td>
            <td><strong>{{ $tps->nama_tps }}</strong></td>
            <td>{{ $tps->lokasi ?? '-' }}</td>
            <td class="text-right mono">{{ $tps->total_dpt }}</td>
            <td class="text-right mono" style="color: #0f7b6c;">{{ $tps->sudah_memilih }}</td>
            <td class="text-right mono" style="color: #2563eb;"><strong>{{ $tpct }}%</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <table class="signatures">
      <tr>
        <td>
          Mengetahui,<br>
          <strong>Pembina OSIS</strong>
          <div class="sign-space"></div>
          ........................................
        </td>
        <td>
          {{ $setting->nama_sekolah }}, {{ $tanggalIndo }}<br>
          <strong>Ketua Panitia Pemilihan OSIS</strong>
          <div class="sign-space"></div>
          ........................................
        </td>
      </tr>
    </table>
  </div>

  @foreach($tpsWithVoters as $tps)
    <div class="sheet">
      <table class="header-wrap">
        <tr>
          <td>
            <p class="header-title">Daftar Hadir Pemilih Tetap — {{ $tps->nama_tps }}</p>
            <p class="header-sub">{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
            <p class="header-meta">
              <strong>TPS:</strong> {{ $tps->kode_tps }}
              @if($tps->lokasi) &nbsp;|&nbsp; <strong>Lokasi:</strong> {{ $tps->lokasi }} @endif
              &nbsp;|&nbsp; <strong>Total DPT:</strong> {{ $tps->voters->count() }} pemilih
            </p>
          </td>
          @if($hasLogo)
            <td style="width: 68px; text-align: right;">
              <img src="{{ $logoFile }}" alt="Logo" class="header-logo">
            </td>
          @endif
        </tr>
      </table>

      <table class="data-table">
        <thead>
          <tr>
            <th class="text-center" style="width: 34px;">No</th>
            <th style="width: 82px;">NIS</th>
            <th>Nama Pemilih</th>
            <th style="width: 64px;">Kelas</th>
            <th style="width: 78px;">Bukti</th>
            <th style="width: 82px;">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tps->voters as $idx => $av)
            <tr>
              <td class="text-center mono">{{ $idx + 1 }}</td>
              <td class="mono">{{ $av->nis }}</td>
              <td><strong>{{ $av->nama }}</strong></td>
              <td>{{ $av->kelas }}</td>
              <td class="mono">{{ $av->receipt_code ?? '-' }}</td>
              <td>
                @if($av->has_voted)
                  <span style="color: #0f7b6c; font-weight: bold;">Hadir</span>
                @else
                  <span style="color: #888888;">Belum</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center" style="padding: 14px; color: #666666;">Belum ada pemilih terdaftar pada TPS ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <table class="signatures">
        <tr>
          <td>
            Mengetahui,<br>
            <strong>Pembina OSIS</strong>
            <div class="sign-space"></div>
            ........................................
          </td>
          <td>
            {{ $setting->nama_sekolah }}, {{ $tanggalIndo }}<br>
            <strong>Koordinator {{ $tps->kode_tps }}</strong>
            <div class="sign-space"></div>
            ........................................
          </td>
        </tr>
      </table>
    </div>
  @endforeach

</body>
</html>
