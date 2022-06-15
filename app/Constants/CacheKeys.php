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
namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

// 集中定义业务中一些常用的缓存Key
#[Constants]
class CacheKeys extends AbstractConstants
{
    public const IS_USER_LOGON = 'USER_LOGON_%s_%s';
}
