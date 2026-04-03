// CSRF 토큰 전역 설정
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ==========================================
// ✅ 1. Laravel Echo 설정 (Pusher와 연결)
// ==========================================
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: 'your_app_key', // .env에 적은 값과 동일하게 입력
    cluster: 'your_cluster', // .env에 적은 값과 동일하게 입력
    forceTLS: true
});

// ==========================================
// ✅ 2. 실시간 메뉴 업데이트 리스너 추가
// ==========================================
// 'menu-updates' 공개 채널을 듣습니다.
echo.channel('menu-updates')
    .listen('MenuUpdated', (e) => {
        console.log('메뉴 변경 감지:', e);
        
        // 서버로부터 "메뉴가 바뀌었어!"라는 신호(update_needed)를 받으면
        if(e.message === 'update_needed') {
            // 사용자 화면의 메뉴 데이터를 다시 서버에서 가져와서 화면을 갱신합니다.
            console.log('메뉴 화면을 다시 로드합니다.');
            loadMenus(); // 기존에 우리가 만들어둔 메뉴 로드 함수를 재호출!
        }
    });


let cart = JSON.parse(localStorage.getItem('kiosk_cart') || '[]');
let totalAmount = 0;

// 페이지 로드 시 메뉴 가져오기
document.addEventListener('DOMContentLoaded', () => {
    loadMenus();
});

// 메뉴 데이터를 서버(API)에서 가져와서 화면에 출력
function loadMenus() {
    axios.get('/api/menus')
        .then(response => {
            const categories = response.data;
            const menuGrid = document.getElementById('menu-grid');
            menuGrid.innerHTML = ''; // 초기화

            categories.forEach(category => {
                category.menus.forEach(menu => {
                    // 메뉴 카드 HTML 생성
                    const menuHtml = `
                        <div class="border p-1 rounded flex flex-col items-center">
                            <img src="${menu.image_url}" alt="${menu.name}" class="w-8 h-8 rounded mb-1">
                            <span>${menu.name}</span>
                            <span class="font-bold">${menu.price}원</span>
                            <button onclick="addToCart(${menu.id}, '${menu.name}', ${menu.price})" class="bg-gray-200 text-gray-700 px-1 rounded text-[6px]">Add</button>
                        </div>
                    `;
                    menuGrid.innerHTML += menuHtml;
                });
            });
        })
        .catch(error => console.error("메뉴 로드 실패:", error));
}

// 장바구니 담기
function addToCart(id, name, price) {
    cart.push({ id, name, price, quantity: 1 });
    totalAmount += price;
    updateCartSummary();
}

// 장바구니 요약 업데이트
function updateCartSummary() {
    document.getElementById('cart-count').innerText = cart.length;
    document.getElementById('cart-total').innerText = totalAmount;
}

// 키오스크 단계 전환 (1단계 -> 2단계 -> ...)
function changeStep(step) {
    // 1단계 카드는 유지, 2~5단계 카드만 숨기고 보여주기
    document.getElementById('step-2-card').classList.add('hidden');
    document.getElementById('step-3-card').classList.add('hidden');
    document.getElementById('step-4-card').classList.add('hidden');
    document.getElementById('step-5-card').classList.add('hidden');

    if (step === 2) {
        document.getElementById('step-2-card').classList.remove('hidden');
        renderOrderManagementScreen(); // 2단계 화면 렌더링
    } else if (step === 3) {
        document.getElementById('step-3-card').classList.remove('hidden');
        renderPaymentOptionsScreen(); // 3단계 화면 렌더링
    }
    // ... 나머지 단계도 구현
}

// 2단계: 주문 관리 화면 렌더링 (장바구니 목록)
function renderOrderManagementScreen() {
    const card = document.getElementById('step-2-card');
    let orderItemsHtml = '';
    cart.forEach(item => {
        orderItemsHtml += `<div class="text-[10px] flex justify-between border-b py-1"><span>${item.name}</span><span>${item.quantity}</span><span>${item.price}원</span></div>`;
    });

    card.innerHTML = `
        <span class="kiosk-step text-xs font-bold text-gray-400 mb-2">2 / 5</span>
        <div class="hardware border-4 border-gray-300 rounded-3xl w-full h-[400px] overflow-hidden relative">
            <div class="w-2 h-2 bg-gray-300 rounded-full absolute top-1 left-1/2 -translate-x-1/2"></div>
            <div class="screen flex-1 p-2 flex flex-col">
                <header class="text-xs font-bold mb-2">My Orders</header>
                <main class="flex-1 overflow-y-auto space-y-1">
                    ${orderItemsHtml}
                </main>
                <footer class="mt-2 border-t pt-1 flex justify-between items-center text-[10px]">
                    <span class="font-bold">Total: ${totalAmount}원</span>
                    <button onclick="changeStep(3)" class="bg-orange-500 text-white p-1 rounded text-[8px]">Checkout</button>
                </footer>
            </div>
        </div>
        <div class="w-12 h-1 bg-gray-300 rounded-full mt-2"></div>
        <p class="text-xs font-bold text-white text-center mt-3">SIMPLE ORDER MANAGEMENT</p>
    `;
}

// 3단계: 결제 수단 화면 렌더링 및 최종 주문
function renderPaymentOptionsScreen() {
    const card = document.getElementById('step-3-card');
    card.innerHTML = `
        <span class="kiosk-step text-xs font-bold text-gray-400 mb-2">3 / 5</span>
        <div class="hardware border-4 border-gray-300 rounded-3xl w-full h-[400px] overflow-hidden relative">
            <div class="w-2 h-2 bg-gray-300 rounded-full absolute top-1 left-1/2 -translate-x-1/2"></div>
            <div class="screen flex-1 p-2 flex flex-col">
                <header class="text-xs font-bold mb-4">Select Payment Method</header>
                <main class="grid grid-cols-2 gap-4 text-center">
                    <button onclick="placeOrder('card')" class="border p-4 rounded-xl flex flex-col items-center text-[10px]"><span class="text-xl">💳</span>Card</button>
                    <button onclick="placeOrder('qr')" class="border p-4 rounded-xl flex flex-col items-center text-[10px]"><span class="text-xl">📲</span>QR</button>
                    <button onclick="placeOrder('cash')" class="border p-4 rounded-xl flex flex-col items-center text-[10px]"><span class="text-xl">💵</span>Cash</button>
                </main>
            </div>
        </div>
        <div class="w-12 h-1 bg-gray-300 rounded-full mt-2"></div>
        <p class="text-xs font-bold text-white text-center mt-3">WIDE RANGE OF INTEGRATED PAYMENT OPTIONS</p>
    `;
}

// 최종 주문 전송 및 완료 단계로 이동
function placeOrder(paymentMethod) {
    if (cart.length === 0) return;

    const orderData = {
        table_number: 5, // 실제론 로그인된 태블릿 번호 사용
        total_amount: totalAmount,
        payment_method: paymentMethod,
        items: cart
    };

    // 라라벨 API로 최종 주문 데이터 전송
    axios.post('/api/orders', orderData)
        .then(response => {
            const res = response.data;
            alert(res.message); // 선불/후불 모드에 맞는 알림창 노출
            
            if(res.success) {
                // 주문 성공 시 5단계(완료) 카드로 이동
                cart = []; totalAmount = 0; updateCartSummary(); // 초기화
                renderOrderSuccessScreen(res.order_id);
                changeStep(5); // TODO: changeStep 함수 수정 필요 (지금은 4단계 건너뛰고 5단계로감)
            }
        })
        .catch(error => { console.error("주문 실패:", error); alert("주문 중 오류 발생."); });
}