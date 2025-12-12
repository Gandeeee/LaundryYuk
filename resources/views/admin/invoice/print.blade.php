<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota #LD-{{ $order->id }}</title>
    <style>
        /* RESET CSS UNTUK PRINT */
        body {
            font-family: 'Courier New', Courier, monospace; /* Font khas struk */
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4; /* Background abu di layar */
        }
        
        .invoice-box {
            max-width: 380px; /* Lebar standar kertas thermal 80mm */
            margin: 0 auto;
            padding: 20px;
            background-color: #fff; /* Putih di layar */
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        /* HEADER */
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 15px; margin-bottom: 15px; }
        .logo { font-size: 22px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px; }
        .address { font-size: 11px; line-height: 1.4; color: #555; }

        /* INFO DATA */
        .info-group { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
        .label { font-weight: bold; }

        /* TABEL ITEM */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; }
        th { text-align: left; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 8px 0; font-size: 12px; text-transform: uppercase; }
        td { padding: 8px 0; vertical-align: top; font-size: 13px; }
        .text-right { text-align: right; }
        
        /* TOTAL */
        .total-section { border-top: 2px dashed #000; padding-top: 10px; margin-top: 10px; }
        .grand-total { font-size: 18px; font-weight: 900; padding: 10px 0; border-bottom: 2px dashed #000; margin-bottom: 15px; }

        /* FOOTER */
        .footer { text-align: center; font-size: 10px; margin-top: 20px; color: #555; }
        .qr-code { margin: 15px auto; width: 80px; display: block; }

        /* TOMBOL PRINT (Hanya di layar) */
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; font-family: sans-serif; font-size: 14px; cursor: pointer; }
        .btn-print { background: #333; color: #fff; border: none; }
        .btn-close { background: #ddd; color: #333; margin-left: 10px; border: none; }

        /* SETTING KHUSUS PRINTER */
        @media print {
            body { background-color: #fff; padding: 0; }
            .invoice-box { border: none; box-shadow: none; width: 100%; max-width: 100%; padding: 0; margin: 0; }
            .no-print { display: none !important; }
            @page { margin: 0; } /* Hilangkan margin kertas default */
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Nota</button>
        <button onclick="window.close()" class="btn btn-close">Tutup</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">LAUNDRY YUK!</div>
            <div class="address">
                Jl. Raya Jetis Kulon I No.66B, Wonokromo, Kec. Wonokromo, Surabaya, Jawa Timur 60243<br>
                WA: 0899-7990-809
            </div>
        </div>

        <div class="info-group">
            <span class="label">Order ID:</span>
            <span>#LD-{{ $order->id }}</span>
        </div>
        <div class="info-group">
            <span class="label">Tanggal:</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-group">
            <span class="label">Pelanggan:</span>
            <span style="text-transform: uppercase;">{{ substr($order->customer_name, 0, 18) }}</span>
        </div>
        <div class="info-group">
            <span class="label">Status:</span>
            <span style="font-weight:bold;">{{ str_replace('_', ' ', $order->status) }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="55%">Layanan</th>
                    <th width="20%" class="text-right">Jml/Brt</th>
                    <th width="25%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $order->service_type }}
                        @if($order->notes)
                            <br><i style="font-size: 10px; color: #666;">({{ $order->notes }})</i>
                        @endif
                    </td>
                    <td class="text-right">{{ $order->total_weight }}</td>
                    <td class="text-right">{{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="info-group grand-total">
                <span>TOTAL BAYAR</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="info-group">
                <span class="label">Metode Bayar:</span>
                <span>Transfer / Tunai</span>
            </div>
            <div class="info-group">
                <span class="label">Status Bayar:</span>
                <span style="border: 1px solid #000; padding: 2px 5px;">{{ $order->is_paid ? 'LUNAS' : 'BELUM LUNAS' }}</span>
            </div>
        </div>

        <div class="footer">
            <p>*** TERIMA KASIH ***</p>
            <p>Harap simpan struk ini sebagai bukti pengambilan.</p>
        </div>
    </div>

</body>
</html>