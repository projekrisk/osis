<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $title }}</title>
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
    @page { margin: 16mm 15mm; }
    body {
      font-family: 'Bricolage Grotesque', DejaVu Sans, Arial, sans-serif;
      font-size: 10.5px;
      color: #222222;
      line-height: 1.45;
      margin: 0;
    }
    .header {
      border-bottom: 2px solid #222222;
      padding-bottom: 10px;
      margin-bottom: 14px;
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
      font-size: 9.5px;
      color: #666666;
      margin: 0;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 18px;
    }
    .data-table th {
      background-color: #f7f7f5;
      border: 1px solid #d4d4d4;
      padding: 6.5px 8px;
      text-align: left;
      font-size: 9.5px;
      font-weight: bold;
    }
    .data-table td {
      border: 1px solid #e5e5e5;
      padding: 6px 8px;
      font-size: 9.5px;
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
      margin-top: 22px;
      border-collapse: collapse;
    }
    .signatures td {
      width: 50%;
      text-align: center;
      vertical-align: top;
      font-size: 9.5px;
    }
    .sign-space { height: 50px; }
  </style>
</head>
<body>

  <div class="header">
    <p class="header-title">{{ $title }}</p>
    <p class="header-sub">{{ $setting->nama_kegiatan }} — {{ $setting->nama_sekolah }} ({{ $setting->tahun_ajaran }})</p>
    <p class="header-meta">
      <strong>Total DPT:</strong> {{ $totalVoters }} pemilih
      &nbsp;|&nbsp; <strong>Total Suara Masuk:</strong> {{ $totalVotes }} suara
      &nbsp;|&nbsp; <strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i') }}
    </p>
  </div>

  @if($tab === 'perolehan')
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 50px;">Peringkat</th>
          <th class="text-center" style="width: 50px;">No. Urut</th>
          <th style="width: 80px;">Kode</th>
          <th>Pasangan Calon (Ketua &amp; Wakil)</th>
          <th class="text-right" style="width: 75px;">Jumlah Suara</th>
          <th class="text-right" style="width: 65px;">Persentase</th>
        </tr>
      </thead>
      <tbody>
        @foreach($candidates as $rank => $c)
          @php $pct = $totalVotes > 0 ? round(($c->votes_count / $totalVotes) * 100, 1) : 0; @endphp
          <tr>
            <td class="text-center mono"><strong>#{{ $rank + 1 }}</strong></td>
            <td class="text-center mono">0{{ $c->nomor_urut }}</td>
            <td class="mono">{{ $c->kode_paslon }}</td>
            <td><strong>{{ $c->ketua_nama }}</strong> ({{ $c->ketua_kelas }}) &amp; {{ $c->wakil_nama }} ({{ $c->wakil_kelas }})</td>
            <td class="text-right mono"><strong>{{ $c->votes_count }}</strong></td>
            <td class="text-right mono" style="color: #2563eb;"><strong>{{ $pct }}%</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @elseif($tab === 'kelas')
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 32px;">No</th>
          <th>Kelas / Unit</th>
          <th class="text-right" style="width: 75px;">Total DPT</th>
          <th class="text-right" style="width: 85px;">Sudah Memilih</th>
          <th class="text-right" style="width: 85px;">Belum Memilih</th>
          <th class="text-right" style="width: 70px;">Partisipasi</th>
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
  @elseif($tab === 'tps')
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 72px;">Kode TPS</th>
          <th>Nama TPS</th>
          <th>Lokasi</th>
          <th class="text-right" style="width: 65px;">Total DPT</th>
          <th class="text-right" style="width: 65px;">Hadir</th>
          <th class="text-right" style="width: 65px;">Partisipasi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tpsList as $tps)
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
  @elseif($tab === 'daftar-hadir')
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 28px;">No</th>
          <th style="width: 74px;">NIS</th>
          <th>Nama Pemilih</th>
          <th style="width: 58px;">Kelas</th>
          <th style="width: 58px;">TPS</th>
          <th style="width: 75px;">Kode Bukti</th>
          <th style="width: 82px;">Kehadiran</th>
          <th class="text-right" style="width: 82px;">Waktu Hadir</th>
        </tr>
      </thead>
      <tbody>
        @forelse($attendanceVoters as $idx => $av)
          <tr>
            <td class="text-center mono">{{ $idx + 1 }}</td>
            <td class="mono">{{ $av->nis }}</td>
            <td><strong>{{ $av->nama }}</strong></td>
            <td>{{ $av->kelas }}</td>
            <td class="mono">{{ $av->tps?->kode_tps ?? '-' }}</td>
            <td class="mono">{{ $av->receipt_code ?? '-' }}</td>
            <td>
              @if($av->has_voted)
                <span style="color: #0f7b6c; font-weight: bold;">Hadir / Memilih</span>
              @else
                <span style="color: #888888;">Belum Hadir</span>
              @endif
            </td>
            <td class="text-right mono">{{ $av->voted_at?->format('d/m/Y H:i') ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center" style="padding: 14px; color: #666666;">Data daftar hadir tidak ditemukan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  @endif

  <table class="signatures">
    <tr>
      <td>
        Mengetahui,<br>
        <strong>Pembina OSIS</strong>
        <div class="sign-space"></div>
        ........................................
      </td>
      <td>
        {{ $setting->nama_sekolah }}, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
        <strong>Ketua Panitia Pemilihan OSIS</strong>
        <div class="sign-space"></div>
        ........................................
      </td>
    </tr>
  </table>

</body>
</html>
