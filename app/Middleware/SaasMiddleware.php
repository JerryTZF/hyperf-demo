<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/7/8 16:39
 * Author: JerryTian<tzfforyou@163.com>
 * File: SaasMiddleware.php
 * Desc:
 */


namespace App\Middleware;

use App\Tenant\Tenant;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class SaasMiddleware implements MiddlewareInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Tenant::instance()->init();

        return $handler->handle($request);
    }
}