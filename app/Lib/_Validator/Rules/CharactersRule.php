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

class CharactersRule implements RuleInterface
{
    public const NAME = 'characters';

    public function passes($attribute, $value, $parameters, Validator $validator): bool
    {
        return (bool) preg_match('/^[\\x{4e00}-\\x{9fa5}]+$/u', (string) $value);
    }

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string
    {
        return '必须为汉字';
    }
}
