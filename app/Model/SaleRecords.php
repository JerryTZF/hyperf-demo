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
/**
 * Created by PhpStorm
 * Time: 2022/4/7 10:16
 * Author: JerryTian<tzfforyou@163.com>
 * File: SaleRecords.php
 * Desc:.
 */

namespace App\Model;

/**
 * @property int $id
 * @property int $gid
 * @property string $order_no
 * @property string $buyer
 * @property float $amount
 * @property string $create_time
 * @property string $modify_time
 */
class SaleRecords extends Model
{
    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = 'modify_time';

    /**
     * 表名称.
     * @var string
     */
    protected $table = 'sale_records';

    /**
     * 允许被批量赋值的字段集合.
     * @var array
     */
    protected $guarded = [];

    /**
     * 数据格式化配置.
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'gid' => 'integer',
        'create_time' => 'Y-m-d H:i:s',
        'modify_time' => 'Y-m-d H:i:s',
    ];

    /**
     * 时间格式.
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';
}
