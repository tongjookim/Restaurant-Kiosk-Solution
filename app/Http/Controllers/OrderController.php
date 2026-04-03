<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // 관리자 대시보드 화면 렌더링
    public function adminDashboard()
    {
        // 오늘 들어온 모든 주문 가져오기 (실제론 상태별 필터링 필요)
        $orders = Order::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('orders'));
    }

    // 새로운 주문 접수 처리 (고객 태블릿 -> 서버)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|integer',
            'total_amount' => 'required|numeric',
            'payment_method' => 'required|string', // cash, card, qr
            'items' => 'required|array',
        ]);

        $cashMode = config('kiosk.cash_payment_mode');
        $status = 'pending'; // 기본 상태: 대기

        // 후불 현금 결제이거나 카드결제가 완료된 경우, 자동 승인 옵션 체크
        if (($validated['payment_method'] === 'cash' && $cashMode === 'post') || 
             $validated['payment_method'] !== 'cash') {
            if (config('kiosk.auto_approve')) {
                $status = 'approved';
            }
        }

        // DB에 주문 생성
        $order = Order::create([
            'table_number' => $validated['table_number'],
            'total_amount' => $validated['total_amount'],
            'payment_method' => $validated['payment_method'],
            'status' => $status,
        ]);

        // TODO: 여기서 Reverb나 Pusher를 통해 사장님 관리자 화면으로 실시간 알림(이벤트) 전송
        // event(new OrderCreated($order));

        $message = '주문이 접수되었습니다.';
        if ($validated['payment_method'] === 'cash' && $cashMode === 'pre') {
            $message = '직원을 호출 중입니다. 잠시만 기다려주세요.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'order_id' => $order->id,
            'status' => $status
        ]);
    }
}
