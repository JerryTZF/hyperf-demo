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
class SystemCode extends AbstractConstants
{
    /**
     * @Message("API地址错误或HTTP方法错误，请检查 :(")
     */
    public const ROUTE_NOT_FOUND = 9902;

    /**
     * @Message("HTTP方法错误，请检查 :(")
     */
    public const HTTP_METHOD_ERR = 9903;

    /**
     * @Message("系统繁忙，请稍后尝试")
     */
    public const SYSTEM_ERROR = 9999;

    /**
     * @Message("数据库数据未找到")
     */
    public const DATA_NOT_FOUND = 9904;

    /**
     * @Message("当前用户较多，请稍后再试")
     */
    public const RATE_LIMIT_ERR = 9905;

    /**
     * @Message("数据验证失败，原因如下：%s")
     */
    public const VALIDATOR_ERR = 9906;
}
