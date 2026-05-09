<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\PengaturanService::class);
echo "Simpanan Wajib (Global): " . $service->simpananWajib() . "\n";
echo "Simpanan Wajib (11, 2026) int: " . $service->simpananWajib(11, 2026) . "\n";
echo "Simpanan Wajib ('11', '2026') string: " . $service->simpananWajib('11', '2026') . "\n";

$khusus = \App\Models\PengaturanKhusus::all();
echo "\nIsi Table PengaturanKhusus:\n";
foreach($khusus as $k) {
    echo "Key: {$k->key}, Bulan: {$k->bulan}, Tahun: {$k->tahun}, Value: {$k->value}\n";
}
