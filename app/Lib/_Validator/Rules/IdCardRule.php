<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Name: IdCardRule.php
 * User: JerryTian<tzfforyou@163.com>
 * Date: 2021/9/14
 * Time: 下午3:19
 */

namespace App\Lib\_Validator\Rules;


use Hyperf\Validation\Validator;
use App\Lib\_Tool\IdentityCard;

class IdCardRule implements RuleInterface
{
    const NAME = 'id_card';

    public function passes($attribute, $value, $parameters, Validator $validator): bool
    {
        return IdentityCard::isValid($value);
    }

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string
    {
        return '身份证号码格式错误';
    }
}