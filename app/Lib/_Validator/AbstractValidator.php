<?php

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
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param bool $firstError
     * @return bool
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
     * 获取验证器
     * @return ValidatorFactoryInterface
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

    /**
     * 初始化扩展
     */
    protected static function initExtends(): void
    {
        self::$extends = [
            PhoneRule::NAME      => new PhoneRule(),
            CharactersRule::NAME => new CharactersRule(),
            IdCardRule::NAME     => new IdCardRule()
        ];
    }

    /**
     * 注册验证器扩展
     * @param ValidatorFactoryInterface $validator
     * @param array $extends
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

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [];
    }
}