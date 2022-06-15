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
namespace App\Lib\_Validator;

use Hyperf\Validation\Rule;

class DemoValidator extends AbstractValidator
{
    public static function ossValidator(array $data, $message = []): bool
    {
        $rules = ['action' => ['required', Rule::in(['get', 'upload'])]];
        $message = empty($message) ? [
            'action.required' => '行为必填',
            'action.in' => '行为只能是 get 或者 upload',
        ] : [];
        return self::make($data, $rules, $message);
    }
}
