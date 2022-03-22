<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Name: RuleInterface.php
 * User: JerryTian<tzfforyou@163.com>
 * Date: 2021/6/30
 * Time: 下午3:33
 */

namespace App\Lib\_Validator\Rules;


use Hyperf\Validation\Validator;

interface RuleInterface
{
    const PASSES_NAME = 'passes';

    const MESSAGE_NAME = 'message';

    public function passes($attribute, $value, $parameters, Validator $validator): bool;

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string;
}