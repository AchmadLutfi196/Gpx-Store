<div class="container mx-auto p-4">
    <h2 class="text-xl font-semibold mb-4">Daftar Transaksi</h2>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Order ID</th>
                <th class="border p-2">Total</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Tanggal</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td class="border p-2">{{ $transaction->order_id }}</td>
                    <td class="border p-2">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                    <td class="border p-2">
                        @if ($transaction->status == 'settlement')
                            <span class="text-green-500">Berhasil</span>
                        @elseif ($transaction->status == 'pending')
                            <span class="text-yellow-500">Pending</span>
                        @elseif ($transaction->status == 'expire' || $transaction->status == 'cancel')
                            <span class="text-red-500">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="border p-2">{{ $transaction->created_at->format('d M Y') }}</td>
                    <td class="border p-2">
                        <button wire:click="viewDetail({{ $transaction->id }})" class="bg-blue-500 text-white px-3 py-1 rounded">
                            Detail
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center border p-2">Belum ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($selectedTransaction)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
                <h2 class="text-xl font-semibold mb-4">Detail Transaksi</h2>
                <p><strong>Order ID:</strong> {{ $selectedTransaction->order_id }}</p>
                <p><strong>Total:</strong> Rp {{ number_format($selectedTransaction->total_amount, 0, ',', '.') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($selectedTransaction->status) }}</p>
                <p><strong>Tanggal:</strong> {{ $selectedTransaction->created_at->format('d M Y') }}</p>

                <h3 class="mt-4 text-lg font-semibold">Produk</h3>
                <ul>
                    @foreach ($selectedTransaction->items as $item)
                        <li>{{ $item->product->name }} (x{{ $item->quantity }}) - Rp {{ number_format($item->price, 0, ',', '.') }}</li>
                    @endforeach
                </ul>

                <button wire:click="closeDetail" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Tutup</button>
            </div>
        </div>
    @endif
</div>
