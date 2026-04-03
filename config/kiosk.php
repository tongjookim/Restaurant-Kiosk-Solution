<?php

return [
    // 현금 결제 모드: 'pre' (선불-직원호출) 또는 'post' (후불-카운터결제)
    'cash_payment_mode' => env('CASH_PAYMENT_MODE', 'pre'),
    
    // 자동 승인 여부
    'auto_approve' => env('AUTO_APPROVE_ORDERS', false),
];
