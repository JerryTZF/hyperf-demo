<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Name: PhoneRule.php
 * User: JerryTian<tzfforyou@163.com>
 * Date: 2021/6/30
 * Time: 下午3:34
 */

namespace App\Lib\_Validator\Rules;


use Hyperf\Validation\Validator;

/**
 * 手机号号码校验规则
 * Class PhoneRule
 * @package App\Lib\_Validator\Rules
 */
class PhoneRule implements RuleInterface
{
    const NAME = 'mobile';

    public function passes($attribute, $value, $parameters, Validator $validator): bool
    {
        return (bool)preg_match("/^1[234578]\d{9}$/", (string)$value);
    }

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string
    {
        return '手机号错误,请检查 :(';
    }
}