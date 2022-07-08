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
namespace App\Tenant;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Traits\StaticInstance;
use Psr\Container\ContainerInterface;

class Tenant
{
    use StaticInstance;

    protected ContainerInterface $container;

    protected int $id = 1;

    public function __construct()
    {
        $this->container = ApplicationContext::getContainer();
    }

    public function init($id = null): void
    {
        if (empty($id)) {
            $request = $this->container->get(RequestInterface::class);
            $id = $request->header('X-TENANT-ID');
        }

        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
