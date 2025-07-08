@forelse($transaksi as $t)
<tr class="bg-white">
    <td class="px-5 py-4 border-b border-gray-200 text-sm">{{ $t->updated_at->format('Y-m-d H:i') }}</td>
    <td class="px-5 py-4 border-b border-gray-200 text-sm">
        @if($t->type == 'payment')
            {{ $t->external_id ?? '-' }}
        @else
            {{ $t->xendit_reference_id ?? $t->id }}
        @endif
    </td>
    <td class="px-5 py-4 border-b border-gray-200 text-sm">
        @if($t->type == 'payment')
            {{ $t->description ?? 'Top Up Saldo' }}
        @else
            Penarikan Dana - {{ $t->account_name ?? '-' }}
        @endif
    </td>
    <td class="px-5 py-4 border-b border-gray-200 text-sm">
        @if($t->type == 'payment')
            <span class="text-green-600">+Rp {{ number_format($t->amount, 0, ',', '.') }}</span>
        @else
            <span class="text-red-600">-Rp {{ number_format($t->amount, 0, ',', '.') }}</span>
        @endif
    </td>
    <td class="px-5 py-4 border-b border-gray-200 text-sm">
        @if($t->type == 'payment')
            {{ strtoupper($t->method ?? '-') }}
        @else
            {{ strtoupper($t->payment_type ?? 'BANK') }}
        @endif
    </td>
    <td class="px-5 py-4 border-b border-gray-200 text-sm">
        <span class="px-2 py-1 text-xs rounded-full 
            @if($t->status == 'completed') bg-green-100 text-green-800
            @elseif($t->status == 'processing') bg-blue-100 text-blue-800
            @elseif($t->status == 'failed') bg-red-100 text-red-800
            @elseif($t->status == 'pending') bg-yellow-100 text-yellow-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ $t->status_label }}
        </span>
    </td>
    <td class="px-5 py-4 border-b border-gray-200 text-sm">
        @if($t->type == 'payment' && $t->external_id)
            <a href="{{ route('manajemen.topup.payment', ['external_id' => $t->external_id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">Cek Status</a>
        @else
            <span class="text-gray-400 text-xs">{{ $t->type_label }}</span>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="px-5 py-10 border-b border-gray-200 text-sm text-center text-gray-500">
        Tidak ada transaksi lainnya.
    </td>
</tr>
@endforelse
