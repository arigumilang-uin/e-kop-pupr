<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

Artisan::command('test:export {--bidang=} {--jenis=}', function () {
    $params = ['month' => 5, 'year' => 2026];
    if ($this->option('jenis')) $params['jenis'] = $this->option('jenis');
    if ($this->option('bidang')) $params['bidang'] = $this->option('bidang');
    
    $request = Request::create('/potongan/export/pdf', 'GET', $params);
    $export = new \App\Exports\PotonganExport($request);
    $data = $export->getData();
    
    $this->info("Params: " . json_encode($params));
    $this->info("Total rows: " . count($data['rows']));
    $this->info("Grand totals: " . json_encode($data['grandTotals']));
    
    if (count($data['rows']) > 0) {
        $this->info("First row sample: " . json_encode($data['rows'][0]));
    } else {
        $this->warn("No rows returned!");
    }
});
