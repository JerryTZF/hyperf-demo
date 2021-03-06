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

#[Constants]
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("您的账号在其他设备登录，请检查")
     */
    public const LONG_DISTANCE_LOGIN = 2980;

    /**
     * @Message("账户或密码错误")
     */
    public const ADMIN_404 = 2981;

    /**
     * @Message("cookie不存在")
     */
    public const COOKIE_NOT_FOUND = 2982;

    /**
     * @Message("文件的Mime或类型不符")
     */
    public const FILE_MIME_ERR = 2983;

    /**
     * @Message("文件名不能为空")
     */
    public const FILE_NAME_ERR = 2984;

    /**
     * @Message("库存不足")
     */
    public const INVENTORY_ERR = 2985;

    /**
     * @Message("获取锁超时")
     */
    public const GET_LOCK_ERR = 2986;

    /**
     * @Message("日志类型错误")
     */
    public const LOG_TYPE_ERR = 2987;
}
