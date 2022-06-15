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

interface RuleInterface
{
    public const PASSES_NAME = 'passes';

    public const MESSAGE_NAME = 'message';

    public function passes($attribute, $value, $parameters, Validator $validator): bool;

    public function message($message, $attribute, $rule, $parameters, Validator $validator): string;
}
