<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Name: CharactersRule.php
 * User: JerryTian<tzfforyou@163.com>
 * Date: 2021/9/13
 * Time: 下午6:27
 */

namespace App\Lib\_Validator\Rules;


use Hyperf\Validation\Validator;

class CharactersRule implements RuleInterface
{
    const NAME = 'characters';

    public function passes($attribute, $value, $parameters, Validator $validator): bool
    {
        return (bool)preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", (string)$value);
    }

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string
    {
        return '必须为汉字';
    }
}