<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\AdminController;

// ==========================================
// 1. 키오스크(고객용) API
// ==========================================
// 메뉴 데이터를 가져오는 API
Route::get('/menus', [KioskController::class, 'getMenus']);
// 주문을 저장하는 API (주문 접수)
Route::post('/orders', [KioskController::class, 'placeOrder']);

// ==========================================
// 2. 사장님용 관리자 데이터 처리 API (/api/admin/...)
// ==========================================
Route::prefix('admin')->group(function () {
    // 실시간 주문 상태 변경 (승인/거절)
    Route::patch('/orders/{id}/status', [KioskController::class, 'updateStatus']);
    
    // ✅ 상품 메뉴 관리 (등록/수정/삭제)
    Route::post('/menus/store', [AdminController::class, 'storeMenu']);
    Route::put('/menus/{id}/update', [AdminController::class, 'updateMenu']);
    Route::delete('/menus/{id}/delete', [AdminController::class, 'destroyMenu']); // 안으로 들어왔습니다!
    
    // ✅ 카테고리 관리 (등록/수정/삭제)
    Route::post('/categories/store', [AdminController::class, 'storeCategory']);
    Route::put('/categories/{id}/update', [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{id}/delete', [AdminController::class, 'destroyCategory']); // 안으로 들어왔습니다!
});