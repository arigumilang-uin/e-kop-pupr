<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Services\PengaturanService;
use App\Services\PotonganService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PotonganBulananController extends Controller
{
    public function __construct(
        private PengaturanService $pengaturan,
        private PotonganService $potonganService,
    ) {}

    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $jenisPokok = JenisSimpanan::pokok();
        $jenisWajib = JenisSimpanan::wajib();

        $nominalPokok = $this->pengaturan->simpananPokok();
        $nominalWajib = $this->pengaturan->simpananWajib();

        $anggotas = Anggota::aktif()
            ->with(['bidang'])
            ->orderBy('nama')
            ->get();

        $dataPotongan = collect();
        $totalKeseluruhan = 0;

        foreach ($anggotas as $anggota) {
            $tanggalMasuk = $anggota->tanggal_masuk;
            $bulanMulaiPotongan = $tanggalMasuk ? $tanggalMasuk->copy()->addMonth()->startOfMonth() : null;
            $periodeFilter = Carbon::createFromDate($year, $month, 1);
            $belumWaktunyaDipotong = $bulanMulaiPotongan && $periodeFilter->lt($bulanMulaiPotongan);

            $sudahBayarPokok = DB::table('simpanan')
                ->where('anggota_id', $anggota->id)
                ->where('jenis_simpanan_id', $jenisPokok->id ?? 0)
                ->exists();
            $potonganPokok = ($sudahBayarPokok || $belumWaktunyaDipotong) ? 0 : $nominalPokok;

            $sudahBayarWajib = DB::table('simpanan')
                ->where('anggota_id', $anggota->id)
                ->where('jenis_simpanan_id', $jenisWajib->id ?? 0)
                ->where('bulan_untuk', $month)
                ->where('tahun_untuk', $year)
                ->exists();
            $potonganWajib = ($sudahBayarWajib || $belumWaktunyaDipotong) ? 0 : $nominalWajib;

            $potonganPinjaman = DB::table('angsuran')
                ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
                ->where('pinjaman.anggota_id', $anggota->id)
                ->where('angsuran.status', 'belum')
                ->whereMonth('angsuran.tanggal_jatuh_tempo', $month)
                ->whereYear('angsuran.tanggal_jatuh_tempo', $year)
                ->sum('angsuran.nominal_total');

            $totalPotongan = $potonganPokok + $potonganWajib + $potonganPinjaman;
            $totalKeseluruhan += $totalPotongan;

            $dataPotongan->push((object)[
                'anggota' => $anggota,
                'potongan_pokok' => $potonganPokok,
                'potongan_wajib' => $potonganWajib,
                'potongan_pinjaman' => $potonganPinjaman,
                'total_potongan' => $totalPotongan,
            ]);
        }

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
        }
        $years = range(now()->year - 2, now()->year + 2);

        return view('potongan.index', compact('dataPotongan', 'month', 'year', 'months', 'years', 'totalKeseluruhan'));
    }

    public function proses(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return back()->with('error', 'Bulan dan tahun tidak valid.');
        }

        try {
            $countProcessed = $this->potonganService->proses((int) $month, (int) $year, auth()->id());

            return back()->with('success', "Pemrosesan TPP periode $month/$year berhasil dibukukan. ($countProcessed anggota)");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses TPP: ' . $e->getMessage());
        }
    }
}
