<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/25 18:44
 * Author: JerryTian<tzfforyou@163.com>
 * File: CacheKeys.php
 * Desc:
 */


namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

// 集中定义业务中一些常用的缓存Key
#[Constants]
class CacheKeys extends AbstractConstants
{
    public const IS_USER_LOGON = 'USER_LOGON_%s_%s';
}