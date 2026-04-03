// public/js/admin.js
window.Echo.channel('orders')
    .listen('NewOrderCreated', (e) => {
        // 1. 주문 목록 상단에 새로운 카드 추가
        const orderHtml = createOrderCard(e.order); 
        $('#order-list').prepend(orderHtml);

        // 2. 알림음 재생
        const audio = new Audio('/sounds/notification.mp3');
        audio.play();

        // 3. 브라우저 푸시 알림 (필요 시)
        alert('새로운 주문이 들어왔습니다: ' + e.order.table_number + '번 테이블');
    });
