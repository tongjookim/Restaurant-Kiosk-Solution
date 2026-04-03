@extends('layouts.admin')
@section('title', '카테고리 및 메뉴 관리')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-bold mb-4 border-b pb-2">📂 카테고리 관리</h3>
        <div class="flex gap-2 mb-4">
            <input type="text" id="new_category_name" placeholder="새 카테고리명" class="border p-2 rounded flex-1 text-sm">
            <button onclick="addCategory()" class="bg-indigo-600 text-white px-4 rounded text-sm font-bold">추가</button>
        </div>
        <ul class="space-y-2 text-sm">
            @foreach($categories as $category)
            <li class="flex justify-between items-center bg-gray-50 p-2 rounded border">
                <span class="font-bold">{{ $category->name }}</span>
                <div class="space-x-2">
                    <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}')" class="text-blue-500 hover:underline">수정</button>
                    <button onclick="deleteCategory({{ $category->id }})" class="text-red-500 hover:underline">삭제</button>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-bold mb-4 border-b pb-2">🍔 메뉴 목록 및 추가</h3>
        
        <form class="flex gap-2 mb-6 bg-gray-50 p-3 rounded border" id="menu-form">
            <input type="hidden" id="menu_id">
            <select id="menu_category_id" class="border p-2 rounded text-sm">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="text" id="menu_name" placeholder="메뉴 이름" class="border p-2 rounded flex-1 text-sm">
            <input type="number" id="menu_price" placeholder="가격(원)" class="border p-2 rounded w-24 text-sm">
            <button type="button" id="btn-save-menu" onclick="saveMenu()" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-bold">등록</button>
            <button type="button" id="btn-cancel-edit" onclick="resetMenuForm()" class="hidden bg-gray-400 text-white px-4 py-2 rounded text-sm font-bold">취소</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 text-gray-600">
                    <tr><th class="p-3">카테고리</th><th class="p-3">메뉴명</th><th class="p-3">가격</th><th class="p-3">상태</th><th class="p-3">관리</th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($menus as $menu)
                    <tr>
                        <td class="p-3">{{ $menu->category->name }}</td>
                        <td class="p-3 font-bold">{{ $menu->name }}</td>
                        <td class="p-3">{{ number_format($menu->price) }}원</td>
                        <td class="p-3">
                            <button onclick="toggleMenuStatus({{ $menu->id }}, {{ $menu->is_available ? 'false' : 'true' }})" 
                                    class="{{ $menu->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-2 py-1 rounded text-xs font-bold">
                                {{ $menu->is_available ? '판매중' : '품절' }}
                            </button>
                        </td>
                        <td class="p-3 space-x-2">
                            <button onclick="editMenu({{ $menu->id }}, {{ $menu->category_id }}, '{{ $menu->name }}', {{ $menu->price }})" class="text-blue-500 hover:underline">수정</button>
                            <button onclick="deleteMenu({{ $menu->id }})" class="text-red-500 hover:underline">삭제</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // 카테고리 관리 로직 (기존과 동일)
    // ==========================================
    function addCategory() {
        const name = document.getElementById('new_category_name').value;
        if(!name) return alert('카테고리명을 입력하세요.');
        axios.post('/api/admin/categories/store', { name })
            .then(() => location.reload())
            .catch(error => {
                console.error(error);
                alert('카테고리 추가 중 오류가 발생했습니다.');
            });
    }

    function editCategory(id, oldName) {
        const newName = prompt('수정할 카테고리 이름을 입력하세요:', oldName);
        if(newName && newName !== oldName) {
            axios.put(`/api/admin/categories/${id}/update`, { name: newName })
                .then(() => location.reload())
                .catch(error => {
                    console.error(error);
                    alert('카테고리 수정 중 오류가 발생했습니다.');
                });
        }
    }

    function deleteCategory(id) {
        if(confirm('이 카테고리를 삭제하시겠습니까?')) {
            axios.delete(`/api/admin/categories/${id}/delete`).then(res => {
                if(!res.data.success) {
                    // 컨트롤러에서 보낸 에러 메시지(메뉴가 남아있음) 출력
                    alert(res.data.message); 
                } else {
                    location.reload();
                }
            }).catch(error => {
                console.error(error);
                alert('카테고리 삭제 중 오류가 발생했습니다.');
            });
        }
    }

    // ==========================================
    // 메뉴 관리 로직 (추가/수정/삭제)
    // ==========================================
    function saveMenu() {
        const id = document.getElementById('menu_id').value;
        const data = {
            category_id: document.getElementById('menu_category_id').value,
            name: document.getElementById('menu_name').value,
            price: document.getElementById('menu_price').value
        };

        if(!data.name || !data.price) return alert('메뉴명과 가격을 입력하세요.');

        const request = id ? axios.put(`/api/admin/menus/${id}/update`, data) : axios.post('/api/admin/menus/store', data);
        
        request.then(res => {
             // 서버 응답이 성공적인지 확인 (선택 사항이지만 안전함)
             if(res.data && res.data.success === false) {
                 alert(res.data.message || '저장 중 오류가 발생했습니다.');
             } else {
                 alert(id ? '수정되었습니다.' : '등록되었습니다.');
                 location.reload();
             }
        }).catch(error => {
             console.error(error);
             alert('오류가 발생했습니다. 입력값을 확인해주세요.');
        });
    }

    function editMenu(id, categoryId, name, price) {
        document.getElementById('menu_id').value = id;
        document.getElementById('menu_category_id').value = categoryId;
        document.getElementById('menu_name').value = name;
        document.getElementById('menu_price').value = price;
        
        document.getElementById('btn-save-menu').innerText = '수정 완료';
        document.getElementById('btn-save-menu').classList.replace('bg-indigo-600', 'bg-green-600');
        document.getElementById('btn-cancel-edit').classList.remove('hidden');
    }

    function resetMenuForm() {
        document.getElementById('menu_id').value = '';
        document.getElementById('menu_name').value = '';
        document.getElementById('menu_price').value = '';
        
        document.getElementById('btn-save-menu').innerText = '등록';
        document.getElementById('btn-save-menu').classList.replace('bg-green-600', 'bg-indigo-600');
        document.getElementById('btn-cancel-edit').classList.add('hidden');
    }

    // ✅ 에러 처리 로직이 추가된 메뉴 삭제 함수
    function deleteMenu(id) {
        if(confirm('이 메뉴를 삭제하시겠습니까?')) {
            axios.delete(`/api/admin/menus/${id}/delete`)
                .then(res => {
                    // 1. 서버에서 "삭제 불가" 메시지를 보냈을 경우
                    if (!res.data.success) {
                        alert(res.data.message); 
                    } 
                    // 2. 정상적으로 삭제된 경우
                    else {
                        alert('메뉴가 삭제되었습니다.');
                        location.reload();
                    }
                })
                .catch(error => {
                    // 네트워크 오류 등 500 에러 처리
                    console.error('메뉴 삭제 실패:', error);
                    alert('삭제 처리 중 시스템 오류가 발생했습니다.');
                });
        }
    }

    function toggleMenuStatus(id, isAvailable) {
        axios.put(`/api/admin/menus/${id}/update`, { is_available: isAvailable })
            .then(() => location.reload())
            .catch(error => {
                console.error(error);
                alert('상태 변경 중 오류가 발생했습니다.');
            });
    }
</script>
@endsection