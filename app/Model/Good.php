<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/4/7 10:14
 * Author: JerryTian<tzfforyou@163.com>
 * File: Good.php
 * Desc:
 */


namespace App\Model;

/**
 * @property int $id
 * @property string $g_name
 * @property string $g_flag
 * @property int $g_inventory
 * @property float $g_price
 * @property string $create_time
 * @property string $modify_time
 * @package App\Model
 */
class Good extends Model
{
    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'modify_time';

    /**
     * 表名称
     * @var string
     */
    protected $table = 'good';

    /**
     * 允许被批量赋值的字段集合
     * @var array
     */
    protected $guarded = [];

    /**
     * 数据格式化配置
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'g_inventory' => 'integer',
        'create_time' => 'Y-m-d H:i:s',
        'modify_time' => 'Y-m-d H:i:s'
    ];

    /**
     * 时间格式
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';
}