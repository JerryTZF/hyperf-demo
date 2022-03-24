<?php

declare (strict_types=1);

namespace App\Model;

/**
 * CREATE TABLE `admin` (
 * `id` int NOT NULL AUTO_INCREMENT,
 * `account` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户',
 * `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 * `pwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
 * `create_time` datetime DEFAULT NULL,
 * `modify_time` datetime DEFAULT NULL,
 * PRIMARY KEY (`id`) USING BTREE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;
 */

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $account
 * @property string $token
 * @property string $pwd
 * @property string $create_time
 * @property string $modify_time
 */
class Admin extends Model
{
    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'modify_time';

    /**
     * 表名称
     * @var string
     */
    protected $table = 'admin';

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
        'create_time' => 'Y-m-d H:i:s',
        'modify_time' => 'Y-m-d H:i:s'
    ];

    /**
     * 时间格式
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 密码修改器
     * @param $value
     */
    public function setPwdAttribute($value)
    {
        $this->attributes['pwd'] = md5($value);
    }
}