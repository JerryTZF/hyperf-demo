<?php

declare(strict_types=1);

/**
CREATE TABLE `sale_records` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`gid` int unsigned NOT NULL DEFAULT '0',
`order_no` varchar(128) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`buyer` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`amount` decimal(5,2) NOT NULL,
`create_time` datetime DEFAULT NULL,
`modify_time` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `order_no` (`order_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 */


/**
 * Created by PhpStorm
 * Time: 2022/4/7 10:16
 * Author: JerryTian<tzfforyou@163.com>
 * File: SaleRecords.php
 * Desc:
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
 * @package App\Model
 */
class SaleRecords extends Model
{
    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'modify_time';

    /**
     * 表名称
     * @var string
     */
    protected $table = 'sale_records';

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
        'gid'         => 'integer',
        'create_time' => 'Y-m-d H:i:s',
        'modify_time' => 'Y-m-d H:i:s'
    ];

    /**
     * 时间格式
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';
}