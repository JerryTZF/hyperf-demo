<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/4/14 14:17
 * Author: JerryTian<tzfforyou@163.com>
 * File: WebSocketController.php
 * Desc:
 */


namespace App\Controller;

use App\Lib\_Log\Log;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Utils\Arr;
use Swoole\Http\Request;
use Swoole\Websocket\Frame;

class WebSocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{

    public function onClose($server, int $fd, int $reactorId): void
    {
        Log::stdout()->warning("fd:{$fd};reactorId:{$reactorId} 已经关闭");
    }

    public function onMessage($server, Frame $frame): void
    {
        Log::stdout()->info("已收到{$frame->fd}号请求,发送数据为: {$frame->data}");
        $server->push($frame->fd, "已收到{$frame->fd}号请求,发送数据为: {$frame->data}");
    }

    public function onOpen($server, Request $request): void
    {
        $token = Arr::get($request->get, 'token', '');
        // 添加你自己的校验 【注意】本应该在handshake里面实现,这里偷一下懒 :(
        if ($token === 'joker') {
            $server->push($request->fd, "{$request->fd}号已经连接成功");
        } else {
            $server->close($request->fd);
        }
    }
}