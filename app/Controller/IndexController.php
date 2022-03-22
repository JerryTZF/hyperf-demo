<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Job\ConsumerJob;
use App\Job\ErrorDemoJob;
use App\Lib\_RedisQueue\DriverFactory;

class IndexController extends AbstractController
{
    public function index()
    {
        $driver = DriverFactory::getDriverInstance('redis-queue');
        for ($i = 200; $i--;) {
            $driver->push(new ErrorDemoJob((string)$i, [$i]));
        }
        return [];
    }
}
