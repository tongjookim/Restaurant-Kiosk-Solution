<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <-- 이 인터페이스가 핵심입니다.
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // 브로드캐스트할 데이터 (가급적 최소한의 정보만 담습니다. "메뉴가 바뀌었어!"라는 신호 역할)
    public $message;

    public function __construct()
    {
        $this->message = 'update_needed'; // "업데이트가 필요해"라는 신호를 보냅니다.
    }

    // 어떤 채널로 메시지를 보낼지 결정합니다. (공개 채널)
    public function broadcastOn(): array
    {
        return [
            new Channel('menu-updates'), // 'menu-updates'라는 이름의 공개 채널을 사용합니다.
        ];
    }
}
