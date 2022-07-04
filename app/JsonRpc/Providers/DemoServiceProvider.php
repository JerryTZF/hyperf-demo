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
namespace App\JsonRpc\Providers;

use App\JsonRpc\src\DemoServiceProviderInterface;
use Hyperf\RpcServer\Annotation\RpcService;

#[RpcService(name: 'DemoService', protocol: 'jsonrpc', server: 'jsonrpc', publishTo: 'nacos')]
class DemoServiceProvider implements DemoServiceProviderInterface
{
    public function add(int $a, int $b): int
    {
        return $a + $b;
    }
}
