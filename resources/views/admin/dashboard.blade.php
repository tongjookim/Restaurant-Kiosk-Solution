@extends('layouts.admin')

@section('title', '실시간 주문 현황')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($orders as $order)
    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 transition-all flex flex-col {{ $order->status == 'pending' ? 'border-red-500' : 'border-green-500' }}">
        
        <div class="flex justify-between items-center mb-4">
            <span class="font-bold text-lg text-gray-800">
                🍽️ 테이블 {{ $order->table_number }}
            </span>
            <span class="text-sm font-semibold {{ $order->status == 'pending' ? 'text-red-500 animate-pulse' : 'text-gray-500' }}">
                {{ $order->created_at->format('H:i') }}
            </span>
        </div>

        <div class="flex-1 mb-6">
            <ul class="space-y-2 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
                @forelse($order->items as $item)
                <li class="flex justify-between items-center border-b border-dashed border-gray-200 pb-2 last:border-0 last:pb-0">
                    {{-- 메뉴가 삭제되었을 경우를 대비한 안전장치 (?? '삭제된 메뉴') --}}
                    <span class="font-medium">{{ $item->menu->name ?? '삭제된 메뉴' }}</span>
                    <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">x{{ $item->quantity }}</span>
                </li>
                @empty
                <li class="text-gray-400 text-center text-xs">메뉴 정보가 없습니다.</li>
                @endforelse
            </ul>
        </div>

        <div class="flex justify-between items-end border-t border-gray-100 pt-4 mt-auto">
            <div>
                <span class="block text-xs text-gray-400 font-bold mb-1">
                    결제: {{ strtoupper($order->payment_method) }}
                </span>
                <span class="font-bold text-lg text-indigo-600">
                    {{ number_format($order->total_amount) }}원
                </span>
            </div>
            
            @if($order->status == 'pending')
                <button onclick="updateStatus({{ $order->id }}, 'approved')" 
                        class="bg-red-500 text-white px-5 py-2 rounded-lg text-sm font-bold shadow hover:bg-red-600 transition">
                    주문 승인
                </button>
            @else
                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-bold border border-green-200">
                    ✅ 조리 중
                </span>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full flex flex-col items-center justify-center p-12 bg-white rounded-xl shadow-sm border border-gray-100">
        <span class="text-6xl mb-4 opacity-50">😴</span>
        <p class="text-xl font-bold text-gray-500">현재 대기 중인 주문이 없습니다.</p>
    </div>
    @endforelse
</div>

<script>
    function updateStatus(id, status) {
        if(confirm('이 주문을 승인하시겠습니까?')) {
            axios.patch(`/api/admin/orders/${id}/status`, { status: status })
                .then(() => location.reload())
                .catch(err => {
                    console.error(err);
                    alert('상태 변경 중 오류가 발생했습니다.');
                });
        }
    }
</script>
@endsection