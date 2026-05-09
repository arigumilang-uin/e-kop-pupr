<?php
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Services\PengaturanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Request::create('/potongan', 'GET', ['month' => 5, 'year' => 2026, 'jenis' => 'pokok']);
$export = new \App\Exports\PotonganExport($request);
$data = $export->getData();
print_r($data);
