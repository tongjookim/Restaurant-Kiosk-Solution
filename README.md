# 🍔 Restaurant Kiosk & Admin Solution

라라벨(Laravel)로 구축한 스마트 매장 관리 및 키오스크 통합 솔루션입니다. 고객용 키오스크 화면과 사장님용 실시간 관리자 대시보드를 제공합니다.

---

## 🚀 주요 기능

### 📱 고객용 키오스크 (Client Kiosk)
* **실시간 메뉴 판:** 카테고리별 메뉴 확인 및 장바구니 담기
* **스마트 장바구니:** 수량 조절(+/-) 및 개별 삭제 기능
* **결제 프로세스:** 카드, 현금, QR 등 다양한 결제 수단 선택 및 주문 완료 처리
* **LocalStorage 연동:** 페이지 이동 시에도 장바구니 데이터 유지

### 👨‍💼 사장님용 관리자 (Admin Dashboard)
* **실시간 주문 현황:** 테이블별 주문 내역 확인 및 원클릭 주문 승인
* **카테고리/메뉴 CRUD:** 카테고리 추가/수정/삭제 및 메뉴 상세 설정 (가격, 상태 등)
* **품절 관리:** 메뉴별 실시간 판매 상태(판매중/품절) 토글 기능
* **결제 내역 조회:** 과거 주문 기록 및 매출 상세 내역 확인 (페이징 지원)

---

## 🛠 기술 스택 (Tech Stack)

| 구분 | 기술 스택 |
| :--- | :--- |
| **Backend** | Laravel 11 (PHP 8.x) |
| **Frontend** | Tailwind CSS, Blade Engine, Axios |
| **Database** | SQLite (Default) |
| **Real-time** | Laravel Broadcasting (Pusher 연동 가능) |

---

## ⚙️ 시작하기 (Installation)

1. **저장소 복제**
   ```bash
   git clone https://github.com/tongjookim/Restaurant-Kiosk-Solution.git
   cd Restaurant-Kiosk-Solution

2. **패키지 설치**
    ```bash
   composer install
   npm install && npm run dev

3. **환경설정**
   ```bash
   cp .env.example .env
   php artisan key:generate

4.  **데이터베이스 설정**
    ```bash
    php artisan migrate --seed

5. **서버 실행**
   ```bash
   php artisan serve --port=2323

---

## 📂 핵심 프로젝트 구조
* **`app/Http/Controllers:`** 키오스크 및 관리자 핵심 로직
* **`resources/views/kiosk:`** 고객용 화면 UI (Blade)
* **`resources/views/admin:`** 관리자용 화면 UI (Blade)
* **`resources/views/layouts:`** 공통 레이아웃 (Admin/App)
* **`routes/api.php:`** 비동기 통신을 위한 API 주소 정의
