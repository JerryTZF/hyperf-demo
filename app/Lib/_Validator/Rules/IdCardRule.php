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

use App\Lib\_Tool\IdentityCard;
use Hyperf\Validation\Validator;

class IdCardRule implements RuleInterface
{
    public const NAME = 'id_card';

    public function passes($attribute, $value, $parameters, Validator $validator): bool
    {
        return IdentityCard::isValid($value);
    }

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string
    {
        return '身份证号码格式错误';
    }
}
