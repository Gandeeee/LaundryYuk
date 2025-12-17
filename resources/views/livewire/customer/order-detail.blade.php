<div wire:poll.5s class="container mt-5 pt-4">

    {{-- JUDUL --}}
    <h4 class="fw-bold mb-4">
        Detail Pesanan
        <span class="text-muted">#LD-{{ $order->id }}</span>
    </h4>

    {{-- CARD DETAIL --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            {{-- STATUS --}}
            <div class="row mb-2">
                <div class="col-5 text-muted">Status</div>
                <div class="col-7 fw-bold
                    @if($order->status === 'MENUNGGU_PEMBAYARAN') text-warning
                    @elseif($order->status === 'PROSES_PENCUCIAN') text-primary
                    @elseif(in_array($order->status, ['SELESAI_DICUCI','TIBA'])) text-success
                    @else text-secondary
                    @endif
                ">
                    {{ str_replace('_', ' ', $order->status) }}
                </div>
            </div>

            {{-- LAYANAN --}}
            <div class="row mb-2">
                <div class="col-5 text-muted">Layanan</div>
                <div class="col-7">
                    {{ $order->service_type ?? '-' }}
                </div>
            </div>

            {{-- ALAMAT --}}
            <div class="row mb-2">
                <div class="col-5 text-muted">Alamat Jemput</div>
                <div class="col-7">
                    {{ $order->pickup_address ?? '-' }}
                </div>
            </div>

            {{-- JADWAL --}}
            <div class="row mb-2">
                <div class="col-5 text-muted">Jadwal Jemput</div>
                <div class="col-7">
                    @if($order->pickup_schedule)
                        {{ \Carbon\Carbon::parse($order->pickup_schedule)->format('d M Y H:i') }}
                    @else
                        <span class="text-muted">Belum dijadwalkan</span>
                    @endif
                </div>
            </div>

            {{-- BERAT --}}
            <div class="row mb-2">
                <div class="col-5 text-muted">Total Berat</div>
                <div class="col-7">
                    {{ $order->total_weight }} kg
                </div>
            </div>

            {{-- HARGA --}}
            <div class="row mb-2">
                <div class="col-5 text-muted">Total Harga</div>
                <div class="col-7 fw-bold text-success">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </div>
            </div>

        </div>
    </div>

    {{-- TOMBOL KEMBALI --}}
    <a href="{{ route('customer.history') }}"
       class="btn btn-outline-secondary">
        ← Kembali ke Riwayat
    </a>

</div>
