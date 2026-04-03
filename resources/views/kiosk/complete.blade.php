@extends('layouts.app')
@section('content')
<div class="h-screen bg-orange-500 flex flex-col items-center justify-center text-white p-4">
    <div class="text-8xl mb-6">✅</div>
    <h1 class="text-5xl font-bold mb-4">주문이 완료되었습니다!</h1>
    <p class="text-2xl mb-8">주문번호: <span class="font-bold text-4xl bg-white text-orange-500 px-4 py-1 rounded-lg">#{{ $orderId }}</span></p>
    <p class="text-xl">영수증을 챙겨주시고 자리에서 잠시만 기다려주세요.</p>
    <p class="mt-12 text-sm opacity-70">5초 후 메인 화면으로 돌아갑니다...</p>
</div>

<script>
    setTimeout(() => { window.location.href = '/'; }, 5000);
</script>
@endsection
