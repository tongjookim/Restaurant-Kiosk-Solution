<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\AdminController;

// ==========================================
// 1. 고객용 키오스크 라우트
// ==========================================
Route::get('/', [KioskController::class, 'index']);           // 메뉴 주문 페이지
Route::get('/payment', [KioskController::class, 'payment']); // 결제 수단 선택 페이지
Route::get('/complete', [KioskController::class, 'complete']);// 결제 완료/영수증 페이지

// ==========================================
// 2. 사장님용 관리자 라우트 
// (실제 서비스 시에는 로그인 미들웨어(auth)를 추가해야 합니다)
// ==========================================
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);      // 실시간 주문 확인
    Route::get('/menus', [AdminController::class, 'menus']);     // 메뉴 추가 및 관리
    Route::get('/history', [AdminController::class, 'history']); // 결제 내역 조회
});