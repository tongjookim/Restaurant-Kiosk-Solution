@extends('layouts.admin')

{{-- 레이아웃 상단 헤더 영역에 들어갈 제목 지정 --}}
@section('title', '결제 및 주문 내역')

@section('content')
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 text-gray-700 font-semibold border-b">
                <tr>
                    <th class="p-4">주문번호</th>
                    <th class="p-4">주문 일시</th>
                    <th class="p-4">테이블</th>
                    <th class="p-4">결제수단</th>
                    <th class="p-4">총 결제액</th>
                    <th class="p-4">상태</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-400">#{{ $order->id }}</td>
                    <td class="p-4 text-gray-600">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td class="p-4">
                        <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded font-bold text-xs">
                            T-{{ $order->table_number }}
                        </span>
                    </td>
                    <td class="p-4">
                        {{-- 결제 수단별 아이콘 및 색상 적용 --}}
                        @if($order->payment_method == 'card')
                            <span class="text-blue-600 font-bold"><span class="mr-1">💳</span>카드</span>
                        @elseif($order->payment_method == 'cash')
                            <span class="text-green-600 font-bold"><span class="mr-1">💵</span>현금</span>
                        @elseif($order->payment_method == 'qr')
                            <span class="text-purple-600 font-bold"><span class="mr-1">📱</span>QR</span>
                        @else
                            <span class="text-gray-600 font-bold uppercase">{{ $order->payment_method }}</span>
                        @endif
                    </td>
                    <td class="p-4 font-bold text-gray-800">{{ number_format($order->total_amount) }}원</td>
                    <td class="p-4">
                        {{-- 상태별 색상 뱃지 적용 --}}
                        @if($order->status == 'pending')
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold border border-red-200">대기중</span>
                        @elseif($order->status == 'approved')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold border border-green-200">완료(승인)</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $order->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500 font-medium">
                        <div class="text-4xl mb-2">📭</div>
                        아직 결제 및 주문 내역이 없습니다.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex justify-center">
    {{ $orders->links() }}
</div>
@endsection