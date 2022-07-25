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

namespace App\Controller;

use App\Lib\_Redis\Redis;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'poker')]
class PokerController extends AbstractController
{
    /**
     * 游戏名称
     * @var string
     */
    protected string $gameName = 'golden_flowers';

    /**
     * 牌的数值
     * @var array|string[]
     */
    protected array $v = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

    /**
     * 牌的花色
     * @var array|string[]
     */
    protected array $t = ['♠', '♣', '♦', '♥'];

    /**
     * 初始化52张扑克牌
     * @return void
     */
    #[GetMapping(path: 'init')]
    public function initPokers(): void
    {
        $pokers = [];
        $redis = Redis::getRedisInstance();
        foreach ($this->t as $type) {
            foreach ($this->v as $value) {
                $pokers[] = $type . $value;
            }
        }

        $redis->del($this->gameName);
        $redis->sAddArray($this->gameName, $pokers);
    }

    /**
     * 随机获取指定张数的扑克牌
     * @param int $sum 扑克张数
     * @return string
     */
    #[GetMapping(path: 'draw')]
    public function drawPoker(int $sum = 3): string
    {
        [$value, $type, $poker] = [[], [], ''];
        for ($i = 0; $i < $sum; $i++) {
            $type[] = $this->t[array_rand($this->t)];
            $value[] = $this->v[array_rand($this->v)];
        }

        // 一行一行画牌
        for ($h = 0; $h < 7; $h++) {
            switch ($h) {
                case 0:
                    $poker .= str_repeat('.-------. ', $sum);
                    break;
                case 1:
                    for ($i = 0; $i < $sum; $i++) {
                        $poker .= match (true) {
                            in_array($value[$i], ['J', 'Q', 'K']) => "|$value[$i]$type[$i]     | ",
                            $value[$i] === '10' => "|10     | ",
                            default => "|$value[$i]      | ",
                        };
                    }
                    break;
                case 2:
                    for ($i = 0; $i < $sum; $i++) {
                        $poker .= match (true) {
                            in_array($value[$i], ['A', 'J', 'Q', 'K']) => "|       | ",
                            in_array($value[$i], ['2', '3']) => "|   $type[$i]   | ",
                            in_array($value[$i], ['4', '5', '6', '7', '8']) => "|  $type[$i] $type[$i]  | ",
                            default => "| $type[$i] $type[$i] $type[$i] | ",
                        };
                    }
                    break;
                case 3:
                    for ($i = 0; $i < $sum; $i++) {
                        $poker .= match (true) {
                            in_array($value[$i], ['A', '3', '5']) => "|   $type[$i]   | ",
                            in_array($value[$i], ['2', '4']) => "|       | ",
                            $value[$i] == '6' => "|  $type[$i] $type[$i]  | ",
                            $value[$i] == 'J' => "|GENERAL| ",
                            $value[$i] == 'Q' => "| QUEEN | ",
                            $value[$i] == 'K' => "| KINGS | ",
                            default => "| $type[$i] $type[$i] $type[$i] | ",
                        };
                    }
                    break;
                case 4:
                    for ($i = 0; $i < $sum; $i++) {
                        $poker .= match (true) {
                            in_array($value[$i], ['A', 'J', 'Q', 'K']) => "|       | ",
                            in_array($value[$i], ['2', '3']) => "|   $type[$i]   | ",
                            in_array($value[$i], ['4', '5', '6', '7']) => "|  $type[$i] $type[$i]  | ",
                            default => "| $type[$i] $type[$i] $type[$i] | ",
                        };
                    }
                    break;
                case 5:
                    for ($i = 0; $i < $sum; $i++) {
                        $poker .= match (true) {
                            $value[$i] === '10' => "|   $type[$i] 10| ",
                            in_array($value[$i], ['J', 'Q', 'K']) => "|     $type[$i]$value[$i]| ",
                            default => "|      $value[$i]| ",
                        };
                    }
                    break;
                case 6:
                    $poker .= str_repeat("'-------' ", $sum);
                    break;
                default:
            }
            $poker .= "\n";
        }
        echo $poker;
        return $poker;
    }
}
