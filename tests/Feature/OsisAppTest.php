<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Tps;
use App\Models\User;
use App\Models\Voter;
use App\Services\XlsxService;
use Database\Seeders\OsisSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OsisAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_frontend_and_guest_redirect_to_login(): void
    {
        $this->seed(OsisSeeder::class);

        $this->get('/')->assertStatus(200)->assertSee('Pasangan Calon Resmi')->assertSee('Lokasi TPS Resmi')->assertSee('Pertanyaan Umum (FAQ)');
        $this->get('/quick-count')->assertStatus(200)->assertSee('Quick Count');
        $this->get('/quick-count/feed')->assertStatus(200)->assertJsonStructure(['total_voters', 'total_votes', 'candidates', 'tps_stats']);
        $this->get('/kebijakan-privasi')->assertStatus(200)->assertSee('Kebijakan Privasi');
        $this->get('/tata-tertib')->assertStatus(200)->assertSee('Tata Tertib Pemilihan');
        $this->get('/bantuan')->assertStatus(200)->assertSee('Bantuan');

        $this->get('/dasbor')->assertRedirect('/login');
        $this->get('/login')->assertStatus(200)->assertSee('Panel Pemilihan OSIS');
        $this->get('/bilik')->assertStatus(200)->assertSee('Bilik Suara');
    }

    public function test_admin_and_panitia_roles_and_token_visibility(): void
    {
        $this->seed(OsisSeeder::class);

        $admin = User::where('email', 'admin@osis.sch.id')->firstOrFail();
        $panitia = User::where('email', 'panitia@osis.sch.id')->firstOrFail();

        $unusedVoter = Voter::where('has_voted', false)->firstOrFail();
        $usedVoter = Voter::where('has_voted', true)->firstOrFail();

        // Dashboard without recent activity + PDF recap preview
        $this->actingAs($admin)->get('/dasbor')
            ->assertStatus(200)
            ->assertDontSee('Aktivitas Pemungutan Suara Terbaru')
            ->assertSee('Cetak / Preview Rekap (PDF)');
        $this->actingAs($admin)->get('/rekapitulasi/export-pdf')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
        foreach (['perolehan', 'kelas', 'tps', 'daftar-hadir'] as $tab) {
            $this->actingAs($admin)->get("/rekapitulasi/export-tab-pdf?tab={$tab}")
                ->assertStatus(200)
                ->assertHeader('content-type', 'application/pdf');
        }

        // Admin can access Pengguna and Candidate create/edit pages
        $this->actingAs($admin)->get('/pengguna')->assertStatus(200)->assertSee('Manajemen Pengguna');
        $this->actingAs($admin)->get('/kandidat/tambah')->assertStatus(200)->assertSee('Tambah Pasangan Calon')->assertSee('Visi &amp; Misi (Editor)', false);
        $this->actingAs($admin)->get('/kandidat/1/edit')->assertStatus(200)->assertSee('Edit Pasangan Calon');
        $this->actingAs($admin)->get('/kandidat/cari-dpt?q=Ahmad')->assertStatus(200)->assertJsonFragment(['nama' => 'Ahmad Fauzan']);
        $this->actingAs($admin)->get('/pantau-tps/unduh-pdf?tps_id=1')
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
        $this->actingAs($admin)->get('/pemilih')
            ->assertStatus(200)
            ->assertSee('1–10')
            ->assertSee('audit-voter-modal')
            ->assertSee($unusedVoter->token)
            ->assertDontSee('HANGUS')
            ->assertSee('line-through');
        $this->actingAs($admin)->get('/pemilih?page=2')
            ->assertStatus(200)
            ->assertSee('11–12');
        $this->actingAs($admin)->get('/pemilih/cetak-kartu')
            ->assertStatus(200)
            ->assertSee('Kartu Pemilih OSIS')
            ->assertSee('qr-box')
            ->assertSee('/bilik')
            ->assertDontSee('/bilik?tps_id=');

        // Natural class sort check on /pemilih/cetak-dpt
        $tps1 = Tps::where('kode_tps', 'TPS-01')->firstOrFail();
        Voter::create(['nis' => '202609910', 'nama' => 'Zaki Sepuluh', 'kelas' => 'X-E10', 'tps_id' => $tps1->id, 'token' => 'OSTST1', 'status' => 'terverifikasi']);
        $this->actingAs($admin)->get("/pemilih/cetak-dpt?tps_id={$tps1->id}")
            ->assertStatus(200)
            ->assertSeeInOrder(['X-E1', 'X-E2', 'X-E10']);

        // Panitia cannot access Pengguna, Cetak Kartu, Add/Edit/Delete/Import DPT, and unused token is hidden
        $this->actingAs($panitia)->get('/pengguna')->assertRedirect('/dasbor');
        $this->actingAs($panitia)->get('/pemilih/cetak-kartu')->assertRedirect('/dasbor');
        $this->actingAs($panitia)->post('/pemilih', [])->assertRedirect('/dasbor');
        $this->actingAs($panitia)->delete("/pemilih/{$unusedVoter->id}")->assertRedirect('/dasbor');
        $this->actingAs($panitia)->get('/pemilih')
            ->assertStatus(200)
            ->assertDontSee($unusedVoter->token)
            ->assertDontSee('audit-voter-modal')
            ->assertDontSee('Tambah Pemilih')
            ->assertDontSee('Cetak Kartu')
            ->assertDontSee('Impor XLSX')
            ->assertSee('Cetak DPT')
            ->assertSee($usedVoter->token)
            ->assertDontSee('HANGUS')
            ->assertSee('line-through');
    }

    public function test_xlsx_import_and_mobile_voting_flow(): void
    {
        $this->seed(OsisSeeder::class);

        $admin = User::where('email', 'admin@osis.sch.id')->firstOrFail();
        $tps1 = Tps::where('kode_tps', 'TPS-01')->firstOrFail();
        $candidate = Candidate::firstOrFail();

        // Create a real XLSX file and import it
        $xlsxPath = XlsxService::createWorkbook([
            ['nis', 'nama', 'kelas', 'kode_tps'],
            ['202688881', 'Siswa Excel Satu', 'X-E1', 'TPS-01'],
        ]);
        $uploaded = new UploadedFile($xlsxPath, 'dpt.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $this->actingAs($admin)->post('/pemilih/import', [
            'file_dpt' => $uploaded,
            'default_tps_id' => $tps1->id,
        ])->assertRedirect('/pemilih');

        $voter = Voter::where('nis', '202688881')->firstOrFail();
        $this->assertEquals($tps1->id, $voter->tps_id);

        // Vote via mobile booth (/bilik)
        $this->post('/bilik/set-tps', ['tps_id' => $tps1->id])->assertRedirect('/bilik');
        $this->post('/bilik/verifikasi', [
            'nis' => $voter->nis,
            'token' => $voter->token,
        ])->assertRedirect('/bilik')->assertSessionHas('verified_voter_id', $voter->id);

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 Chrome/131.0.0.0 Mobile Safari/537.36',
        ])->post('/bilik/pilih', [
            'candidate_id' => $candidate->id,
        ])->assertRedirect('/bilik')->assertSessionHas('last_vote_receipt');

        $voter->refresh();
        $this->assertTrue($voter->has_voted);
        $this->assertNotNull($voter->receipt_code);
        $this->assertNotNull($voter->ip_address);
        $this->assertEquals('Mobile (Pixel 8)', $voter->device_type);
        $this->assertStringContainsString('Android', $voter->os);
        $this->assertStringContainsString('Chrome', $voter->browser);

        // Closed / not started election blocks voting booth
        $this->flushSession();
        $setting = \App\Models\ElectionSetting::current();
        $setting->update(['status_pemilihan' => 'persiapan']);
        $this->get('/bilik')->assertStatus(200)->assertSee('Pemilihan OSIS Belum Dimulai')->assertSee('election-closed-modal');
        $this->post('/bilik/verifikasi', ['nis' => '202601004', 'token' => 'OS1004'])->assertSessionHas('error');

        $setting->update(['status_pemilihan' => 'selesai']);
        $this->get('/bilik')->assertStatus(200)->assertSee('Pemilihan OSIS Telah Selesai')->assertSee('election-closed-modal');

        // Schedule-based auto open/close
        $setting->update([
            'status_pemilihan' => 'berlangsung',
            'jadwal_mulai' => now()->addHour()->format('Y-m-d H:i'),
            'jadwal_selesai' => now()->addHours(5)->format('Y-m-d H:i'),
        ]);
        $this->get('/bilik')->assertStatus(200)->assertSee('Pemilihan OSIS Belum Dimulai');

        $setting->update([
            'jadwal_mulai' => now()->subHours(5)->format('Y-m-d H:i'),
            'jadwal_selesai' => now()->subHour()->format('Y-m-d H:i'),
        ]);
        $this->get('/bilik')->assertStatus(200)->assertSee('Pemilihan OSIS Telah Selesai');
        $this->get('/quick-count')->assertStatus(200)->assertSee('Selesai · Hasil Akhir');
        $this->get('/quick-count/feed')->assertStatus(200)->assertJsonFragment(['is_finished' => true]);

        // Admin settings: reset voter status & clear all voters
        $admin = User::where('email', 'admin@osis.sch.id')->firstOrFail();
        $this->actingAs($admin)->get('/pengaturan')->assertStatus(200)->assertSee('Hapus &amp; Reset Data', false);

        $this->actingAs($admin)->post('/pengaturan/reset-suara')->assertRedirect('/pengaturan');
        $this->assertEquals(0, \App\Models\Vote::count());
        $this->assertEquals(0, Voter::where('has_voted', true)->count());
        $this->assertGreaterThan(0, Voter::count());

        $this->actingAs($admin)->delete('/pengaturan/hapus-pemilih')->assertRedirect('/pengaturan');
        $this->assertEquals(0, Voter::count());
    }
}
