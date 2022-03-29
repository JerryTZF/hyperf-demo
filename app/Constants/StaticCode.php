<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/29 14:18
 * Author: JerryTian<tzfforyou@163.com>
 * File: StaticCode.php
 * Desc:
 */


namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

// 自己业务中一些常量
#[Constants]
class StaticCode extends AbstractConstants
{
    public const LOGIN_COOKIE_NAME = 'ADMIN-LOGIN-COOKIE';
}