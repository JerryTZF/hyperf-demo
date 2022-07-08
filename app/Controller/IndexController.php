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

use App\Lib\_Log\Log;

class IndexController extends AbstractController
{
    public function index(): array
    {
        $a = 1/0;
        return $this->result->getResult();
    }
}
