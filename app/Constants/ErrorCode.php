<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/24 21:58
 * Author: JerryTian<tzfforyou@163.com>
 * File: ErrorCode.php
 * Desc:
 */


namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class ErrorCode extends AbstractConstants
{
    /**
     * @Message(您的账号在其他设备登录，请检查)
     */
    public const LONG_DISTANCE_LOGIN = 2980;
}