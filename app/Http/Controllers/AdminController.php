<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Category;
use App\Models\OrderItem; // ✅ 메뉴 삭제 안전장치를 위해 꼭 추가해야 합니다!
use App\Events\MenuUpdated;

class AdminController extends Controller
{
    // 1. 실시간 주문 관리 대시보드
    public function dashboard()
    {
        $orders = Order::with('items.menu')->whereDate('created_at', today())->orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('orders'));
    }

    // 2. 상품(메뉴) 관리 페이지
    public function menus()
    {
        $menus = Menu::with('category')->get();
        $categories = Category::all();
        return view('admin.menus', compact('menus', 'categories'));
    }

    // ✅ 메뉴 등록 (POST /api/admin/menus/store)
    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
        ]);

        $menu = Menu::create($validated);

        // ✅ 웹소켓 설정이 아직 안 끝났다면 에러를 막기 위해 임시로 주석 처리해 두는 것이 좋습니다.
        // broadcast(new MenuUpdated())->toOthers(); 

        return response()->json([
            'success' => true,
            'message' => '새 메뉴가 등록되었습니다.',
            'menu' => $menu
        ]);
    }

    // --- 카테고리 관리 로직 ---
    public function storeCategory(Request $request) {
        $request->validate(['name' => 'required|string|max:50']);
        Category::create(['name' => $request->name]);
        return response()->json(['success' => true]);
    }

    public function updateCategory(Request $request, $id) {
        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);
        return response()->json(['success' => true]);
    }

    public function destroyCategory($id) {
        $category = Category::findOrFail($id);
        if($category->menus()->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => '해당 카테고리에 등록된 메뉴가 있어 삭제할 수 없습니다. 메뉴를 먼저 삭제하거나 이동해주세요.'
            ]);
        }
        $category->delete();
        return response()->json(['success' => true]);
    }

    // --- 메뉴 삭제 로직 (✅ 에러 방지 안전장치 적용) ---
    public function destroyMenu($id) {
        $menu = Menu::findOrFail($id);
        
        // 이 메뉴가 이미 주문된 내역(order_items)에 존재하는지 확인
        $hasOrders = OrderItem::where('menu_id', $id)->exists();
        
        if ($hasOrders) {
            return response()->json([
                'success' => false,
                'message' => '이미 판매(주문) 내역이 존재하는 메뉴는 삭제할 수 없습니다. 대신 [품절] 처리를 이용해주세요.'
            ]);
        }

        $menu->delete();
        
        // broadcast(new MenuUpdated())->toOthers(); // 필요시 주석 해제
        
        return response()->json(['success' => true]);
    }

    // ✅ 메뉴 수정 (PUT /api/admin/menus/{id}/update)
    public function updateMenu(Request $request, $id)
    {
        // 1. 유효성 검사 (is_available 추가)
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|integer|min:0',
            'image_url' => 'nullable|url',
            'is_available' => 'nullable|boolean', // ✅ 프론트에서 넘어오는 품절/판매중 상태
        ]);

        $menu = Menu::findOrFail($id);
        
        // 2. null 값만 제거하고 false(품절) 값은 살려두는 커스텀 필터
        $updateData = array_filter($validated, function($value) {
            return $value !== null;
        });

        $menu->update($updateData);

        // broadcast(new MenuUpdated())->toOthers(); // 필요시 주석 해제

        return response()->json([
            'success' => true,
            'message' => '메뉴 정보가 수정되었습니다.',
            'menu' => $menu
        ]);
    }

    // 3. 결제 내역 조회 페이지
    public function history()
    {
        $orders = Order::with('items')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.history', compact('orders'));
    }
}