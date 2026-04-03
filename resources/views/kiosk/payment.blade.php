@extends('layouts.app')
@section('content')
<div class="h-screen bg-gray-100 flex flex-col items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-2xl text-center">
        <h1 class="text-3xl font-bold mb-2">결제 수단 선택</h1>
        <p class="text-gray-500 mb-8">총 결제 금액: <span id="pay-total" class="text-orange-500 font-bold text-2xl"></span></p>
        
        <div class="grid grid-cols-3 gap-6 mb-8">
            <button onclick="processPayment('card')" class="p-8 border-2 rounded-xl hover:border-orange-500 hover:bg-orange-50 transition flex flex-col items-center">
                <span class="text-5xl mb-4">💳</span><span class="text-xl font-bold">카드 결제</span>
            </button>
            <button onclick="processPayment('qr')" class="p-8 border-2 rounded-xl hover:border-orange-500 hover:bg-orange-50 transition flex flex-col items-center">
                <span class="text-5xl mb-4">📱</span><span class="text-xl font-bold">QR / 간편결제</span>
            </button>
            <button onclick="processPayment('cash')" class="p-8 border-2 rounded-xl hover:border-orange-500 hover:bg-orange-50 transition flex flex-col items-center">
                <span class="text-5xl mb-4">💵</span><span class="text-xl font-bold">현금 결제</span>
            </button>
        </div>
        <button onclick="window.history.back()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold">이전으로</button>
    </div>
</div>

<script>
    let cart = JSON.parse(localStorage.getItem('kiosk_cart') || '[]');
    let total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    if(cart.length === 0) window.location.href = '/';
    document.getElementById('pay-total').innerText = total.toLocaleString() + '원';

    function processPayment(method) {
        axios.post('/api/orders', { items: cart, payment_method: method, table_number: 1, total_amount: total })
            .then(res => {
                localStorage.removeItem('kiosk_cart'); // 장바구니 비우기
                window.location.href = '/complete?order_id=' + res.data.order_id;
            }).catch(() => alert('결제 오류가 발생했습니다.'));
    }
</script>
@endsection
