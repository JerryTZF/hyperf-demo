<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/30 14:41
 * Author: JerryTian<tzfforyou@163.com>
 * File: DemoValidator.php
 * Desc:
 */


namespace App\Lib\_Validator;

use Hyperf\Validation\Rule;

class DemoValidator extends AbstractValidator
{
    public static function ossValidator(array $data, $message = []): bool
    {
        $rules = ['action' => ['required', Rule::in(['get', 'upload'])]];
        $message = empty($message) ? [
            'action.required' => '行为必填',
            'action.in'       => '行为只能是 get 或者 upload'
        ] : [];
        return self::make($data, $rules, $message);
    }
}