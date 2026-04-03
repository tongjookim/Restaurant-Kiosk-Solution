<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>관리자 대시보드</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden text-gray-800">
    <aside class="w-64 bg-indigo-900 text-white flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-indigo-800 font-bold text-xl">
            🍔 스마트 키오스크 관리자
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/admin" class="block px-4 py-3 rounded hover:bg-indigo-800 transition {{ request()->is('admin') ? 'bg-indigo-800' : '' }}">📊 실시간 주문 현황</a>
            <a href="/admin/menus" class="block px-4 py-3 rounded hover:bg-indigo-800 transition {{ request()->is('admin/menus') ? 'bg-indigo-800' : '' }}">🍔 카테고리/메뉴 관리</a>
            <a href="/admin/history" class="block px-4 py-3 rounded hover:bg-indigo-800 transition {{ request()->is('admin/history') ? 'bg-indigo-800' : '' }}">💰 결제 내역 조회</a>
        </nav>
        <div class="p-4 border-t border-indigo-800 text-sm text-indigo-300">
            접속자: 사장님
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <h2 class="text-xl font-bold text-gray-700">@yield('title', '대시보드')</h2>
            <button class="text-sm bg-gray-200 px-4 py-2 rounded font-bold hover:bg-gray-300">로그아웃</button>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>
</body>
</html>
