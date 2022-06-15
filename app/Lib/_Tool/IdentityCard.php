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
namespace App\Lib\_Tool;

/**
 * 身份证是否合法
 * Class IdentityCard.
 */
class IdentityCard
{
    public static function isValid(string $num): bool
    {
        // 老身份证长度15位，新身份证长度18位
        $length = strlen($num);
        if ($length == 15) { // 如果是15位身份证
            // 15位身份证没有字母
            if (! is_numeric($num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月（6位）
            $dateNum = substr($num, 6, 6);
        } elseif ($length == 18) { // 如果是18位身份证
            // 基本格式校验
            if (! preg_match('/^\d{17}[0-9xX]$/', $num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月日（8位）
            $dateNum = substr($num, 6, 8);
        } else { // 假身份证
            return false;
        }

        // 验证地区
        if (! self::isAreaCodeValid($areaNum)) {
            return false;
        }

        // 验证日期
        if (! self::isDateValid($dateNum)) {
            return false;
        }

        // 验证最后一位
        if (! self::isVerifyCodeValid($num)) {
            return false;
        }

        return true;
    }

    private static function isAreaCodeValid(string $area): bool
    {
        $provinceCode = substr($area, 0, 2);

        // 根据GB/T2260—999，省市代码11到65
        if (11 <= $provinceCode && $provinceCode <= 65) {
            return true;
        }
        return false;
    }

    private static function isDateValid(string $date): bool
    {
        if (strlen($date) == 6) { // 15位身份证号没有年份，这里拼上年份
            $date = '19' . $date;
        }
        $year = intval(substr($date, 0, 4));
        $month = intval(substr($date, 4, 2));
        $day = intval(substr($date, 6, 2));

        // 日期基本格式校验
        if (! checkdate($month, $day, $year)) {
            return false;
        }

        // 日期格式正确，但是逻辑存在问题(如:年份大于当前年)
        $currYear = date('Y');
        if ($year > $currYear) {
            return false;
        }
        return true;
    }

    private static function isVerifyCodeValid(string $num): bool
    {
        if (strlen($num) == 18) {
            $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            $tokens = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

            $checkSum = 0;
            for ($i = 0; $i < 17; ++$i) {
                $checkSum += intval($num[$i]) * $factor[$i];
            }

            $mod = $checkSum % 11;
            $token = $tokens[$mod];

            $lastChar = strtoupper($num[17]);

            if ($lastChar != $token) {
                return false;
            }
        }
        return true;
    }
}
