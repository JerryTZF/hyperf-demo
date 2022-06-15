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
namespace App\Lib\_Validator\Rules;

use Hyperf\Validation\Validator;

/**
 * 手机号号码校验规则
 * Class PhoneRule.
 */
class PhoneRule implements RuleInterface
{
    public const NAME = 'mobile';

    public function passes($attribute, $value, $parameters, Validator $validator): bool
    {
        return (bool) preg_match('/^1[234578]\\d{9}$/', (string) $value);
    }

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string
    {
        return '手机号错误,请检查 :(';
    }
}
