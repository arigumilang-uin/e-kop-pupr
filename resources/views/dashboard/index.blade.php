@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Selamat datang, ' . auth()->user()->nama)

@section('content')

{{-- ZONA 1: Ringkasan Utama (6 Kartu) --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    {{-- Kas & Saldo Tersedia (Highlight) --}}
    <x-stat-card 
        class="col-span-2 md:col-span-1 lg:col-span-2"
        :highlight="true"
        title="Kas Saldo Tersedia"
        value="{{ format_rupiah($stats['saldo_koperasi']) }}"
        subtitle="Dana liquid bisa dicairkan"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    />

    {{-- Piutang Koperasi --}}
    <x-stat-card 
        class="col-span-1 lg:col-span-2"
        title="Piutang Koperasi"
        value="{{ format_rupiah($stats['piutang_berjalan']) }}"
        subtitle="Sisa pokok pinjaman berjalan"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'
    />

    {{-- Simpanan Anggota --}}
    <x-stat-card 
        class="col-span-1 lg:col-span-2"
        title="Simpanan Anggota"
        value="{{ format_rupiah($stats['total_simpanan']) }}"
        subtitle="Estimasi titipan dana anggota"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>'
    />

    {{-- Total Aset --}}
    <x-stat-card 
        class="col-span-2 md:col-span-1 lg:col-span-2"
        title="Total Aset Koperasi"
        value="{{ format_rupiah($stats['total_aset']) }}"
        subtitle="Akumulasi Kas + Total Piutang"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>'
    />

    {{-- Anggota Aktif --}}
    <x-stat-card 
        class="col-span-1 lg:col-span-1"
        title="Anggota Koperasi"
        value="{{ $stats['total_anggota'] }}"
        subtitle="Status bergabung aktif"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'
    />
    
    {{-- Pinjaman Aktif --}}
    <x-stat-card 
        class="col-span-1 lg:col-span-1"
        title="Draft Pinjaman"
        value="{{ $stats['total_pinjaman_aktif'] }}"
        subtitle="Riwayat draft berjalan"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'
    />
</div>

{{-- ZONA 2: Grafik Analitik Keuangan (2 Kolom) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    {{-- Chart A: Arus Kas Bulanan --}}
    <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm h-[320px] flex flex-col">
        <h3 class="text-stone-800 font-semibold mb-1">Pemasukan vs Pengeluaran ({{ date('Y') }})</h3>
        <p class="text-stone-400 text-xs mb-4">Pemantauan siklus uang Koperasi bulanan</p>
        <div class="flex-1 relative w-full h-full">
            <canvas id="cashflowChart"></canvas>
        </div>
    </div>

    {{-- Chart B: Komposisi Simpanan --}}
    <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm h-[320px] flex flex-col relative">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between mb-4 gap-3 z-10">
            <div>
                <h3 class="text-stone-800 font-semibold mb-1">Komposisi Dana Simpanan Anggota</h3>
                <p class="text-stone-400 text-xs">Distribusi bank dana berdasarkan tipeny</p>
            </div>
            <div class="flex flex-row gap-2 shrink-0">
                <select id="filterBidang" class="px-2 py-1.5 rounded-lg border border-stone-200 text-xs text-stone-600 focus:ring-2 focus:ring-[#043d2e]/20 outline-none max-w-[120px] truncate bg-stone-50">
                    <option value="">Semua Bidang</option>
                    @foreach($bidangs as $bidang)
                        <option value="{{ $bidang->id }}">{{ $bidang->nama_bidang }}</option>
                    @endforeach
                </select>
                <select id="filterGolongan" class="px-2 py-1.5 rounded-lg border border-stone-200 text-xs text-stone-600 focus:ring-2 focus:ring-[#043d2e]/20 outline-none max-w-[120px] truncate bg-stone-50">
                    <option value="">Semua Gol. ASN</option>
                    @foreach(\App\Enums\GolonganAsn::cases() as $gol)
                        <option value="{{ $gol->value }}">{{ $gol->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex-1 relative w-full h-[220px]">
            <canvas id="simpananChart"></canvas>
        </div>
    </div>
</div>

{{-- ZONA 3: Metrik Kesehatan (3 Kartu) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-stone-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-stone-500 text-xs font-medium uppercase tracking-wider mb-1">Rasio Likuiditas</p>
            <p class="text-xl font-bold text-stone-800">{{ number_format($stats['rasio_likuiditas'], 1, ',', '.') }}%</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-stone-50 flex items-center justify-center border border-stone-100">
            <span class="text-stone-400 font-bold">💧</span>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-stone-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-stone-500 text-xs font-medium uppercase tracking-wider mb-1">Total Pendapatan</p>
            <p class="text-xl font-bold text-stone-800">{{ format_rupiah($stats['total_pendapatan'] ?? 0) }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-stone-50 flex items-center justify-center border border-stone-100">
            <span class="text-stone-400 font-bold">📈</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-4 md:p-5 border border-stone-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-stone-500 text-xs font-medium uppercase tracking-wider mb-1">Rasio Piutang</p>
            <p class="text-xl font-bold text-stone-800">{{ number_format($stats['rasio_piutang'], 1, ',', '.') }}%</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-stone-50 flex items-center justify-center border border-stone-100">
            <span class="text-stone-400 font-bold">💼</span>
        </div>
    </div>
</div>

{{-- ZONA 4: Tabel Aktivitas (2 Kolom) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Tabel Pengajuan Terbaru --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-semibold text-stone-800">Menunggu Review</h3>
                <p class="text-xs text-stone-500 mt-0.5">Pengajuan pinjaman terbaru</p>
            </div>
            @if($pinjamanMenunggu->count() > 0)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                {{ $pinjamanMenunggu->count() }} Antrean
            </span>
            @endif
        </div>

        @if($pinjamanMenunggu->isEmpty())
        <div class="p-10 text-center flex-1 flex flex-col justify-center">
            <div class="w-12 h-12 rounded-2xl bg-stone-50 flex items-center justify-center mx-auto mb-3 border border-stone-100">
                <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-stone-500 text-sm">Tidak ada pengajuan pinjaman baru.</p>
        </div>
        @else
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left">
                <tbody class="divide-y divide-stone-100 bg-white">
                    @foreach($pinjamanMenunggu as $pinjaman)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-stone-800">{{ $pinjaman->anggota->nama }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[11px] font-medium text-[#043d2e] bg-[#043d2e]/10 px-1.5 py-0.5 rounded">{{ $pinjaman->no_referensi }}</span>
                                <span class="text-xs text-stone-400">{{ $pinjaman->tanggal_pengajuan->format('d M y') }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-bold text-stone-700 block">{{ format_rupiah($pinjaman->nominal_pinjaman) }}</span>
                            <span class="text-xs text-stone-500 mt-0.5 block">{{ $pinjaman->tenor_bulan }} Bulan</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Log Aktivitas Terakhir --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-semibold text-stone-800">Aktivitas Sistem</h3>
                <p class="text-xs text-stone-500 mt-0.5">Jejak audit terbaru admin</p>
            </div>
            <a href="#" class="text-xs font-semibold text-[#043d2e] hover:underline">Lihat Semua</a>
        </div>

        @if($recentActivities->isEmpty())
        <div class="p-10 text-center flex-1 flex flex-col justify-center">
            <div class="w-12 h-12 rounded-2xl bg-stone-50 flex items-center justify-center mx-auto mb-3 border border-stone-100">
                <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-stone-500 text-sm">Belum ada catatan aktivitas.</p>
        </div>
        @else
        <div class="overflow-y-auto flex-1 p-5">
            <div class="space-y-5 relative before:absolute before:inset-0 before:ml-2.5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-stone-200 before:to-transparent">
                @foreach($recentActivities as $aktivitas)
                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group select-none">
                    <div class="flex items-center justify-center w-5 h-5 rounded-full border-2 border-white bg-[#043d2e] shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                        <div class="w-1 h-1 bg-white rounded-full"></div>
                    </div>
                    
                    <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] bg-stone-50 p-3 rounded-xl border border-stone-100 group-hover:bg-[#043d2e]/5 group-hover:border-[#043d2e]/20 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-stone-800">{{ $aktivitas->user->nama ?? 'Sistem' }}</span>
                            <span class="text-[10px] font-medium text-stone-500">{{ $aktivitas->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-stone-600 line-clamp-2">{{ $aktivitas->deskripsi }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Styling Global Chart.js untuk menyesuaikan Tema Stone
    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.color = '#78716c'; // text-stone-500
    Chart.defaults.scale.grid.color = '#f5f5f4'; // border-stone-100
    
    // CHART A: Cashflow (Grouped Bar Chart)
    const ctxCashflow = document.getElementById('cashflowChart');
    if (ctxCashflow) {
        new Chart(ctxCashflow, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: @json($monthlyFlow['pemasukan']),
                        backgroundColor: '#10b981', // emerald-500
                        borderRadius: 4,
                        barPercentage: 0.8,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($monthlyFlow['pengeluaran']),
                        backgroundColor: '#f43f5e', // rose-500
                        borderRadius: 4,
                        barPercentage: 0.8,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { boxWidth: 10, usePointStyle: true, pointStyle: 'circle', padding: 15 }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(28, 25, 23, 0.9)', // text-stone-900
                        titleFont: { size: 13, family: "'Outfit', sans-serif" },
                        bodyFont: { size: 13, family: "'Outfit', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return '0';
                                return value >= 1000000 ? (value / 1000000).toFixed(0) + ' Jt' : value;
                            }
                        }
                    }
                }
            }
        });
    }

    // CHART B: Komposisi Simpanan (Horizontal Bar Chart)
    const ctxSimpanan = document.getElementById('simpananChart');
    if (ctxSimpanan) {
        const simpananDataRaw = @json($stats['breakdown_simpanan']);
        // Format array output from DB
        let simpananLabels = simpananDataRaw.map(item => item.nama);
        let simpananValues = simpananDataRaw.map(item => item.total);

        // Jika kosong, berikan placeholder
        if (simpananLabels.length === 0) {
            simpananLabels = ['Belum Ada Data'];
            simpananValues = [0];
        }

        const simpananChartObj = new Chart(ctxSimpanan, {
            type: 'bar',
            data: {
                labels: simpananLabels,
                datasets: [{
                    label: 'Total Dana',
                    data: simpananValues,
                    backgroundColor: '#043d2e', // Deep Hunter Green
                    borderRadius: 4,
                    barThickness: 'flex',
                    maxBarThickness: 30
                }]
            },
            options: {
                indexAxis: 'y', // Convert to horizontal bar
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(28, 25, 23, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f5f5f4' },
                        border: { display: false },
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return '0';
                                return value >= 1000000 ? (value / 1000000).toFixed(0) + ' Jt' : value;
                            }
                        }
                    },
                    y: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { weight: '600' },
                            color: '#44403c' // stone-700
                        }
                    }
                }
            }
        });

        // Filter Logic (AJAX / Fetch)
        const filterBidang = document.getElementById('filterBidang');
        const filterGolongan = document.getElementById('filterGolongan');

        const fetchSimpananData = async () => {
            const params = new URLSearchParams();
            if (filterBidang.value) params.append('bidang_id', filterBidang.value);
            if (filterGolongan.value) params.append('golongan_asn', filterGolongan.value);

            try {
                const res = await fetch(`/dashboard/simpanan-data?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                
                let newLabels = data.map(item => item.nama);
                let newValues = data.map(item => item.total);

                if (newLabels.length === 0) {
                    newLabels = ['Data Kosong'];
                    newValues = [0];
                }

                simpananChartObj.data.labels = newLabels;
                simpananChartObj.data.datasets[0].data = newValues;
                simpananChartObj.update();
            } catch (error) {
                console.error('Gagal mengambil data simpanan tersaring:', error);
            }
        };

        filterBidang.addEventListener('change', fetchSimpananData);
        filterGolongan.addEventListener('change', fetchSimpananData);
    }
});
</script>
@endpush
