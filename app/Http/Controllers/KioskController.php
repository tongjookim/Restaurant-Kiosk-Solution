<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class KioskController extends Controller
{
    // 메인 키오스크 화면 렌더링
    public function index()
    {
        return view('kiosk.index');
    }

    // 메뉴 데이터를 JSON으로 반환하는 API (자바스크립트가 호출)
    public function getMenus()
    {
        // 카테고리와 해당 카테고리의 품절되지 않은 메뉴만 가져오기
        $categories = Category::with(['menus' => function ($query) {
            $query->where('is_available', true);
        }])->get();

        return response()->json($categories);
    }

    // 최종 주문 완료 처리 (자바스크립트가 호출)
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|integer',
            'total_amount' => 'required|numeric',
            'payment_method' => 'required|string', // cash, card, qr
            'items' => 'required|array',
        ]);

        // 트랜잭션 처리 (안전한 저장)
        DB::beginTransaction();
        try {
            // 주문 생성
            $order = Order::create([
                'table_number' => $validated['table_number'],
                'total_amount' => $validated['total_amount'],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending', // 기본 상태: 대기
            ]);

            // 주문 상세(메뉴들) 저장
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price_at_order' => $item['price'], // 주문 당시 가격
                ]);
            }

            DB::commit();

            // TODO: 사장님 대시보드 실시간 알림 전송 (나중에 구현)

            $message = '주문이 성공적으로 접수되었습니다.';
            // 현금 결제 선불 모드일 때 메시지 변경 (config 연동 필요)
            if ($validated['payment_method'] === 'cash') {
                $message = '직원을 호출 중입니다. 잠시만 기다려주세요.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '주문 처리 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    // 결제 페이지
    public function payment() { return view('kiosk.payment'); }
    
    // 완료 페이지
    public function complete(Request $request) { 
        $orderId = $request->query('order_id');
        return view('kiosk.complete', compact('orderId')); 
    }

    // 관리자 주문 상태 변경 API (API 라우터용)
    public function updateStatus(Request $request, $id) {
        $order = \App\Models\Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }
}
