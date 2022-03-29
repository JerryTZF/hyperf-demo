<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/27 13:08
 * Author: JerryTian<tzfforyou@163.com>
 * File: CoroutineDemoProcess.php
 * Desc:
 */


namespace App\Process;

use App\Lib\_Log\Log;
use Exception;
use Hyperf\Engine\Channel;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Exception\ParallelExecutionException;
use Hyperf\Utils\Parallel;

#[Process(
    nums: 1,
    name: 'CoroutineDemoProcess',
    enableCoroutine: true,
    redirectStdinStdout: false
)]
class CoroutineDemoProcess extends AbstractProcess
{
    // 演示协程基本使用
    public function handle(): void
    {
        $flag = 0;
        while (true) {
            Coroutine::sleep(1);
            if ($flag === 0) {
//                $this->_channel();
            }
            if ($flag > 1000000) {
                break; // 进程会退出(EXIT=0),但是会重新拉起再次执行
            }
            $flag++;
        }
    }

    // 异步创建多个协程
    private function _multipleCoroutines()
    {
        $cids = [];
        for ($i = 100; $i--;) {
            $cid = Coroutine::create(function () use ($i) {
                // 注意:虽然打印了日志,但是这里是不会执行的,
                // 因为是异步,主协程已经执行完了(没有协程切换)
                Log::stdout()->info("第{$i}个任务正在执行");
                Coroutine::sleep(mt_rand(0, 5));
            });
            array_push($cids, $cid);
        }

        // 除非这里阻塞,否则上面创建的协程不会执行
        Coroutine::sleep(5.0);

        $cids = json_encode($cids);
        Log::stdout()->info("子协程ID集合为: $cids");
    }

    // 协程间通信
    private function _channel()
    {
        $ch = new Channel(10); // 缓冲管道(类似golang)
        for ($i = 10; $i--;) {
            Coroutine::create(function () use ($ch, $i) {
                $random = mt_rand(1, 100);
                Log::stdout()->info('任务: ' . $i . ', 随机数为: ' . $random);
                Coroutine::sleep($i);
                $ch->push($random);
            });
        }

        $sum = 0;
        while (true) {
            Coroutine::sleep(1.0);
            if ($ch->isEmpty()) {
                Log::stdout()->warning('管道已经为空');
                break;
            }
            $data = $ch->pop(2.0);
            Log::stdout()->info("管道获取的数据为: $data");
            if ($data) {
                $sum += intval($data);
            } else {
                assert($ch->errCode === SWOOLE_CHANNEL_TIMEOUT);
                break;
            }
        }

        Log::stdout()->warning("管道pop数据总和为:   $sum");
    }

    // 协程间通信错误示例
    private function _channelErrorDemo()
    {
        $sum = 0;
        for ($i = 10; $i--;) {
            Coroutine::create(function () use (&$sum) {
                $random = mt_rand(1, 100);
                Log::stdout()->info("随机数为: $random");
                $sum += $random;
            });
        }

        Coroutine::sleep(5);

        Log::stdout()->warning("多个协程修改一个变量结果为:   $sum");

    }

    // 主协程等待子协程全部结束,子协程并发执行
    private function _parallel()
    {
        $parallel = new Parallel(5);
        for ($i = 20; $i--;) {
            $parallel->add(function () {
                sleep(1);
                return Coroutine::id();
            });
        }
        try {
            $result = json_encode($parallel->wait(true), 256);
            Log::stdout()->info("协程返回的结果集为: $result");
        } catch (ParallelExecutionException | Exception $e) {
            Log::stdout()->error($e->getTraceAsString());
        }
    }
}