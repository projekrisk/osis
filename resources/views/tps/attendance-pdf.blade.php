<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Hadir Pemilih — {{ $tps?->kode_tps ?? 'TPS' }}</title>
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
    @page { margin: 18mm 15mm; }
    body {
      font-family: 'Bricolage Grotesque', DejaVu Sans, Arial, sans-serif;
      font-size: 11px;
      color: #222222;
      line-height: 1.45;
      margin: 0;
    }
    .header-wrap {
      width: 100%;
      border-collapse: collapse;
      border-bottom: 2px solid #222222;
      margin-bottom: 14px;
    }
    .header-wrap td {
      padding-bottom: 10px;
      vertical-align: middle;
    }
    .header-logo {
      width: 52px;
      height: 52px;
      object-fit: contain;
    }
    .header-title {
      font-size: 15px;
      font-weight: bold;
      text-transform: uppercase;
      margin: 0 0 2px 0;
    }
    .header-sub {
      font-size: 12px;
      font-weight: bold;
      color: #444444;
      margin: 0 0 4px 0;
    }
    .header-meta {
      font-size: 10px;
      color: #666666;
      margin: 0;
    }
    .summary-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }
    .summary-table td {
      border: 1px solid #e5e5e5;
      background-color: #f7f7f5;
      padding: 7px 10px;
      width: 25%;
    }
    .summary-label {
      font-size: 9px;
      color: #666666;
      text-transform: uppercase;
      display: block;
      margin-bottom: 2px;
    }
    .summary-value {
      font-size: 13px;
      font-weight: bold;
      color: #222222;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .data-table th {
      background-color: #f7f7f5;
      border: 1px solid #d4d4d4;
      padding: 7px 8px;
      text-align: left;
      font-size: 10px;
      font-weight: bold;
      color: #333333;
    }
    .data-table td {
      border: 1px solid #e5e5e5;
      padding: 6px 8px;
      font-size: 10px;
      vertical-align: middle;
    }
    .data-table tr:nth-child(even) td {
      background-color: #fcfcfc;
    }
    .mono {
      font-family: DejaVu Sans Mono, monospace;
    }
    .badge-hadir {
      color: #0f7b6c;
      font-weight: bold;
    }
    .badge-belum {
      color: #888888;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .signatures {
      width: 100%;
      margin-top: 24px;
      border-collapse: collapse;
    }
    .signatures td {
      width: 50%;
      text-align: center;
      vertical-align: top;
      font-size: 10px;
    }
    .sign-space {
      height: 55px;
    }
  </style>
</head>
<body>
  @php
    $logoFile = $setting->logo_path ? base_path('uploads/' . $setting->logo_path) : null;
    $hasLogo = $logoFile && file_exists($logoFile);
    $tanggalIndo = now()->locale('id')->translatedFormat('d F Y');
  @endphp

  <table class="header-wrap">
    <tr>
      <td>
        <p class="header-title">Daftar Hadir Pemilih Tetap (DPT)</p>
        <p class="header-sub">{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
        <p class="header-meta">
          <strong>TPS:</strong> {{ $tps?->nama_tps ?? 'Semua TPS' }}
          @if($tps?->lokasi) &nbsp;|&nbsp; <strong>Lokasi:</strong> {{ $tps->lokasi }} @endif
          &nbsp;|&nbsp; <strong>Dicetak:</strong> {{ $tanggalIndo }}
        </p>
      </td>
      @if($hasLogo)
        <td style="width: 64px; text-align: right;">
          <img src="{{ $logoFile }}" alt="Logo" class="header-logo">
        </td>
      @endif
    </tr>
  </table>

  <table class="summary-table">
    <tr>
      <td>
        <span class="summary-label">Total DPT TPS</span>
        <span class="summary-value">{{ $totalDpt }} Pemilih</span>
      </td>
      <td>
        <span class="summary-label">Sudah Hadir / Memilih</span>
        <span class="summary-value" style="color: #0f7b6c;">{{ $hadirCount }} Pemilih</span>
      </td>
      <td>
        <span class="summary-label">Belum Hadir</span>
        <span class="summary-value" style="color: #b45309;">{{ $belumCount }} Pemilih</span>
      </td>
      <td>
        <span class="summary-label">Partisipasi</span>
        <span class="summary-value" style="color: #2563eb;">{{ $partisipasi }}%</span>
      </td>
    </tr>
  </table>

  <table class="data-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 28px;">No</th>
        <th style="width: 78px;">NIS</th>
        <th>Nama Lengkap Pemilih</th>
        <th style="width: 60px;">Kelas</th>
        <th style="width: 85px;">Status</th>
        <th style="width: 78px;">Kode Bukti</th>
        <th class="text-right" style="width: 88px;">Waktu Hadir</th>
      </tr>
    </thead>
    <tbody>
      @forelse($voters as $idx => $v)
        <tr>
          <td class="text-center mono">{{ $idx + 1 }}</td>
          <td class="mono">{{ $v->nis }}</td>
          <td><strong>{{ $v->nama }}</strong></td>
          <td>{{ $v->kelas }}</td>
          <td>
            @if($v->has_voted)
              <span class="badge-hadir">Hadir / Memilih</span>
            @else
              <span class="badge-belum">Belum Hadir</span>
            @endif
          </td>
          <td class="mono">{{ $v->receipt_code ?? '-' }}</td>
          <td class="text-right mono">{{ $v->voted_at?->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center" style="padding: 16px; color: #666666;">Belum ada data pemilih terdaftar pada TPS ini.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <table class="signatures">
    <tr>
      <td>
        Mengetahui,<br>
        <strong>Ketua Panitia Pemilihan OSIS</strong>
        <div class="sign-space"></div>
        ........................................
      </td>
      <td>
        {{ $setting->nama_sekolah }}, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
        <strong>Koordinator {{ $tps?->kode_tps ?? 'TPS' }}</strong>
        <div class="sign-space"></div>
        ........................................
      </td>
    </tr>
  </table>

</body>
</html>
