<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TpsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VotingBoothController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/quick-count', [HomeController::class, 'quickCount'])->name('quick-count');
Route::get('/quick-count/feed', [HomeController::class, 'quickCountFeed'])->name('quick-count.feed');
Route::get('/kebijakan-privasi', [HomeController::class, 'privacy'])->name('legal.privacy');
Route::get('/tata-tertib', [HomeController::class, 'terms'])->name('legal.terms');
Route::get('/bantuan', [HomeController::class, 'help'])->name('legal.help');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => route('quick-count'), 'priority' => '0.9', 'changefreq' => 'always'],
        ['loc' => route('legal.privacy'), 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['loc' => route('legal.terms'), 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['loc' => route('legal.help'), 'priority' => '0.5', 'changefreq' => 'monthly'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $u) {
        $xml .= "  <url>\n";
        $xml .= '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
        $xml .= '    <changefreq>' . $u['changefreq'] . "</changefreq>\n";
        $xml .= '    <priority>' . $u['priority'] . "</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, [
        'Content-Type' => 'application/xml; charset=UTF-8',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('sitemap');

Route::get('/uploads/{path}', function (string $path) {
    $base = realpath(base_path('uploads'));
    $full = $base ? realpath($base . DIRECTORY_SEPARATOR . $path) : false;
    if (!$base || !$full || !str_starts_with($full, $base) || !is_file($full)) {
        abort(404);
    }
    return response()->file($full, [
        'Cache-Control' => 'public, max-age=604800, stale-while-revalidate=86400',
    ]);
})->where('path', '.*');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/bilik', [VotingBoothController::class, 'index'])->name('voting.index');
Route::post('/bilik/set-tps', [VotingBoothController::class, 'setTps'])->name('voting.set-tps');
Route::post('/bilik/verifikasi', [VotingBoothController::class, 'verify'])->name('voting.verify');
Route::post('/bilik/pilih', [VotingBoothController::class, 'cast'])->name('voting.cast');
Route::post('/bilik/reset', [VotingBoothController::class, 'resetSession'])->name('voting.reset');

Route::middleware('auth')->group(function () {
    Route::get('/dasbor', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pantau-tps', [TpsController::class, 'monitor'])->name('tps.monitor');
    Route::get('/pantau-tps/feed', [TpsController::class, 'monitorFeed'])->name('tps.monitor.feed');
    Route::get('/pantau-tps/unduh-pdf', [TpsController::class, 'exportAttendancePdf'])->name('tps.monitor.pdf');

    Route::get('/pemilih', [VoterController::class, 'index'])->name('voters.index');
    Route::get('/pemilih/cetak-dpt', [VoterController::class, 'printDpt'])->name('voters.print-dpt');

    Route::get('/rekapitulasi', [RecapController::class, 'index'])->name('recap.index');
    Route::get('/rekapitulasi/export-csv', [RecapController::class, 'exportCsv'])->name('recap.export');
    Route::get('/rekapitulasi/export-pdf', [RecapController::class, 'exportPdf'])->name('recap.pdf');
    Route::get('/rekapitulasi/export-tab-pdf', [RecapController::class, 'exportTabPdf'])->name('recap.tab-pdf');
    Route::get('/rekapitulasi/export-daftar-hadir', [RecapController::class, 'exportAttendanceCsv'])->name('recap.export-attendance');

    Route::post('/pengaturan/password', [SettingController::class, 'updatePassword'])->name('settings.password');

    Route::middleware('admin')->group(function () {

        Route::post('/pemilih', [VoterController::class, 'store'])->name('voters.store');
        Route::post('/pemilih/import', [VoterController::class, 'import'])->name('voters.import');
        Route::get('/pemilih/template-xlsx', [VoterController::class, 'downloadTemplate'])->name('voters.template-xlsx');
        Route::get('/pemilih/cetak-kartu', [VoterController::class, 'printCards'])->name('voters.print-cards');
        Route::put('/pemilih/{voter}', [VoterController::class, 'update'])->name('voters.update');
        Route::delete('/pemilih/{voter}', [VoterController::class, 'destroy'])->name('voters.destroy');
        Route::post('/pemilih/{voter}/reset-token', [VoterController::class, 'resetToken'])->name('voters.reset-token');

        Route::get('/kandidat', [CandidateController::class, 'index'])->name('candidates.index');
        Route::get('/kandidat/cari-dpt', [CandidateController::class, 'searchDpt'])->name('candidates.search-dpt');
        Route::get('/kandidat/tambah', [CandidateController::class, 'create'])->name('candidates.create');
        Route::post('/kandidat', [CandidateController::class, 'store'])->name('candidates.store');
        Route::get('/kandidat/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
        Route::put('/kandidat/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
        Route::delete('/kandidat/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');

        Route::get('/tps', [TpsController::class, 'index'])->name('tps.index');
        Route::post('/tps', [TpsController::class, 'store'])->name('tps.store');
        Route::put('/tps/{tps}', [TpsController::class, 'update'])->name('tps.update');
        Route::delete('/tps/{tps}', [TpsController::class, 'destroy'])->name('tps.destroy');

        Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
        Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
        Route::delete('/pengaturan/logo', [SettingController::class, 'removeLogo'])->name('settings.remove-logo');
        Route::post('/pengaturan/reset-suara', [SettingController::class, 'resetVoterStatus'])->name('settings.reset-votes');
        Route::delete('/pengaturan/hapus-pemilih', [SettingController::class, 'clearVoters'])->name('settings.clear-voters');
    });
});
