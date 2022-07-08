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
namespace App\Model;

use Hyperf\Database\Model\SoftDeletes;

class Test extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = 'modify_time';

    protected $table = 'test';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'create_time' => 'Y-m-d H:i:s',
        'modify_time' => 'Y-m-d H:i:s',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function __construct(int $id, array $attributes = [])
    {

        parent::__construct($attributes);
    }
}
