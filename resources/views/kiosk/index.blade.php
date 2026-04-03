@extends('layouts.app')
@section('content')
<div class="h-screen flex flex-col bg-gray-100">
    <header class="bg-orange-500 text-white text-center py-4 text-2xl font-bold">스마트 주문</header>
    <div class="flex-1 flex overflow-hidden">
        <main class="w-2/3 p-4 overflow-y-auto grid grid-cols-3 gap-4" id="menu-grid"></main>
        
        <aside class="w-1/3 bg-white border-l p-4 flex flex-col">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">장바구니</h2>
            <div id="cart-items" class="flex-1 overflow-y-auto space-y-2"></div>
            <div class="mt-4 border-t pt-4">
                <div class="flex justify-between text-xl font-bold mb-4">
                    <span>총 결제금액:</span>
                    <span id="cart-total" class="text-orange-500">0원</span>
                </div>
                <button onclick="goToPayment()" class="w-full bg-orange-500 text-white py-4 rounded-xl text-xl font-bold hover:bg-orange-600">결제하기</button>
            </div>
        </aside>
    </div>
</div>

<script>
    let cart = JSON.parse(localStorage.getItem('kiosk_cart') || '[]');
    
    document.addEventListener('DOMContentLoaded', () => {
        axios.get('/api/menus').then(res => {
            const grid = document.getElementById('menu-grid');
            grid.innerHTML = ''; // 초기화
            
            res.data.forEach(category => {
                category.menus.forEach(menu => {
                    grid.innerHTML += `
                        <div class="bg-white p-4 rounded-xl shadow cursor-pointer hover:shadow-md transition flex flex-col items-center text-center" 
                             onclick="addToCart(${menu.id}, '${menu.name}', ${menu.price})">
                            <div class="w-full h-24 bg-gray-200 rounded-lg mb-2 bg-cover bg-center" style="background-image: url('${menu.image_url || 'https://via.placeholder.com/150'}')"></div>
                            <h3 class="font-bold text-md text-gray-800">${menu.name}</h3>
                            <p class="text-orange-500 font-bold mt-1">${menu.price.toLocaleString()}원</p>
                        </div>
                    `;
                });
            });
        });
        renderCart();
    });

    // 1. 장바구니에 추가 (기존)
    function addToCart(id, name, price) {
        let existing = cart.find(item => item.id === id);
        if(existing) {
            existing.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1 });
        }
        renderCart();
    }

    // 2. 장바구니 화면 렌더링 (수정됨: UI 개선 및 삭제 버튼 추가)
    function renderCart() {
        let total = 0;
        document.getElementById('cart-items').innerHTML = cart.map(item => {
            total += item.price * item.quantity;
            return `
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                    <div class="flex flex-col flex-1">
                        <span class="font-bold text-sm text-gray-800">${item.name}</span>
                        <span class="text-orange-500 text-xs font-bold">${item.price.toLocaleString()}원</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center border border-gray-300 rounded bg-white">
                            <button onclick="decreaseQuantity(${item.id})" class="px-2 text-gray-600 hover:bg-gray-100">-</button>
                            <span class="px-2 text-sm font-bold border-x border-gray-300 min-w-[2rem] text-center">${item.quantity}</span>
                            <button onclick="increaseQuantity(${item.id})" class="px-2 text-gray-600 hover:bg-gray-100">+</button>
                        </div>
                        <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:bg-red-50 p-1 rounded text-lg" title="삭제">
                            🗑️
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        
        document.getElementById('cart-total').innerText = total.toLocaleString() + '원';
        localStorage.setItem('kiosk_cart', JSON.stringify(cart)); // 로컬스토리지 저장
    }

    // ✅ 3. 수량 감소 함수
    function decreaseQuantity(id) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.quantity--;
            if (item.quantity <= 0) {
                removeFromCart(id); // 수량이 0이 되면 장바구니에서 완전히 삭제
            } else {
                renderCart();
            }
        }
    }

    // ✅ 4. 수량 증가 함수
    function increaseQuantity(id) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.quantity++;
            renderCart();
        }
    }

    // ✅ 5. 장바구니에서 완전히 삭제하는 함수
    function removeFromCart(id) {
        // 선택한 id와 같지 않은 항목들만 남겨서 배열을 덮어씌움
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    // 6. 결제 페이지로 이동 (기존)
    function goToPayment() {
        if(cart.length === 0) return alert('메뉴를 선택해주세요.');
        window.location.href = '/payment';
    }
</script>
@endsection