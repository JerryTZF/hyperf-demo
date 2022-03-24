<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/24 17:20
 * Author: JerryTian<tzfforyou@163.com>
 * File: Captcha.php
 * Desc:
 */


namespace App\Lib\_Image;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;

class Captcha
{
    private Redis $redis;

    private string $prefix = 'captcha_';

    public function __construct()
    {
        $this->redis = ApplicationContext::getContainer()->get(Redis::class);
    }

    // 客户端的唯一码(保证多个验证码的唯一性)
    public function getStream(string $unique): string
    {
        // 验证码长度和范围
        $phrase = new PhraseBuilder(4, 'abcdefghijklmnpqrstuvwxyz123456789');
        // 初始化验证码
        $builder = new CaptchaBuilder(null, $phrase);
        // 创建验证码
        $builder->build();
        // 获取验证码内容
        $phrase = $builder->getPhrase();
        // 验证码写入redis
        $this->redis->del($this->prefix . $unique);
        $this->redis->setex($this->prefix . $unique, 300, $phrase);

        return $builder->get();
    }

    // 校验验证码
    public function verify(string $captcha, string $unique): bool
    {
        if ($this->getPhrase($unique) == $captcha) {
            $this->redis->del($this->prefix . $unique);
            return true;
        }
        return false;
    }

    private function getPhrase(string $unique): string
    {
        return $this->redis->get($this->prefix . $unique) ?: '';
    }
}