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
namespace App\Lib\_Encrypt;

class AES
{
    private static string $key = 'your secret key';

    private static string $iv = '';

    private static string $method = 'AES-128-ECB';

    /**
     * 加密(编码为Hex).
     * @param $data
     */
    public static function aesEn($data): string
    {
        return bin2hex(openssl_encrypt($data, self::$method, self::$key, OPENSSL_RAW_DATA, self::$iv));
    }

    /**
     * 解密(编码为Hex).
     * @param $data
     */
    public static function aesDe($data): string
    {
        return openssl_decrypt(hex2bin($data), self::$method, self::$key, OPENSSL_RAW_DATA, self::$iv);
    }

    /**
     * 加密(编码为Base64).
     * @param $data
     */
    public static function aesEnBase64($data): string
    {
        return base64_encode(openssl_encrypt($data, self::$method, self::$key, OPENSSL_RAW_DATA, self::$iv));
    }

    /**
     * 解密(编码为Base64).
     * @param $data
     */
    public static function aesDeBase64($data): string
    {
        return openssl_decrypt(base64_decode($data), self::$method, self::$key, OPENSSL_RAW_DATA, self::$iv);
    }
}
