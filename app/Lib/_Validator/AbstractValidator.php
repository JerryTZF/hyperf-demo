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

use App\Lib\_Validator\Rules\CharactersRule;
use App\Lib\_Validator\Rules\IdCardRule;
use App\Lib\_Validator\Rules\PhoneRule;
use App\Lib\_Validator\Rules\RuleInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractValidator
{
    protected static array $extends = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function make(array $data, array $rules, array $messages = [], bool $firstError = true): bool
    {
        $validator = self::getValidator();
        if (empty($messages)) {
            $messages = self::messages();
        }

        $valid = $validator->make($data, $rules, $messages);
        if ($valid->fails()) {
            $errors = $valid->errors();
            $error = $firstError ? $errors->first() : $errors;
            throw new ValidationException($valid);
        }

        return true;
    }

    /**
     * 获取验证器.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getValidator(): ValidatorFactoryInterface
    {
        static $validator = null;
        if (is_null($validator)) {
            $container = ApplicationContext::getContainer();
            $validator = $container->get(ValidatorFactoryInterface::class);
            // 初始化扩展
            self::initExtends();
            // 注册扩展
            self::registerExtends($validator, self::$extends);
        }
        return $validator;
    }

    public static function messages(): array
    {
        return [];
    }

    /**
     * 初始化扩展.
     */
    protected static function initExtends(): void
    {
        self::$extends = [
            PhoneRule::NAME => new PhoneRule(),
            CharactersRule::NAME => new CharactersRule(),
            IdCardRule::NAME => new IdCardRule(),
        ];
    }

    /**
     * 注册验证器扩展.
     */
    protected static function registerExtends(ValidatorFactoryInterface $validator, array $extends)
    {
        foreach ($extends as $key => $extend) {
            if ($extend instanceof RuleInterface) {
                $validator->extend($key, function (...$args) use ($extend) {
                    return call_user_func_array([$extend, RuleInterface::PASSES_NAME], $args);
                });

                $validator->replacer($key, function (...$args) use ($extend) {
                    return call_user_func_array([$extend, RuleInterface::MESSAGE_NAME], $args);
                });
            }
        }
    }
}
