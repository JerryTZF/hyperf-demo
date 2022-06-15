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
namespace App\Lib\_Alipay;

use Alipay\EasySDK\Base\Image\Client as imageClient;
use Alipay\EasySDK\Base\OAuth\Client as oauthClient;
use Alipay\EasySDK\Base\Qrcode\Client as qrcodeClient;
use Alipay\EasySDK\Base\Video\Client as videoClient;
use Alipay\EasySDK\Kernel\CertEnvironment;
use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\EasySDKKernel;
use Alipay\EasySDK\Marketing\OpenLife\Client as openLifeClient;
use Alipay\EasySDK\Marketing\Pass\Client as passClient;
use Alipay\EasySDK\Marketing\TemplateMessage\Client as templateMessageClient;
use Alipay\EasySDK\Member\Identification\Client as identificationClient;
use Alipay\EasySDK\Payment\App\Client as appClient;
use Alipay\EasySDK\Payment\Common\Client as commonClient;
use Alipay\EasySDK\Payment\FaceToFace\Client as faceToFaceClient;
use Alipay\EasySDK\Payment\Huabei\Client as huabeiClient;
use Alipay\EasySDK\Payment\Page\Client as pageClient;
use Alipay\EasySDK\Payment\Wap\Client as wapClient;
use Alipay\EasySDK\Security\TextRisk\Client as textRiskClient;
use Alipay\EasySDK\Util\AES\Client as aesClient;
use Alipay\EasySDK\Util\Generic\Client as genericClient;

/**
 * AlipaySDK 封装.
 */
final class AlipaySDK
{
    public array $config = [];

    public EasySDKKernel $kernel;

    private Base $base;

    private Marketing $marketing;

    private Member $member;

    private Payment $payment;

    private Security $security;

    private Util $util;

    private function __construct(Config $config)
    {
        if (! empty($config->alipayCertPath)) {
            $certEnvironment = new CertEnvironment();
            $certEnvironment->certEnvironment(
                $config->merchantCertPath,
                $config->alipayCertPath,
                $config->alipayRootCertPath
            );
            $config->merchantCertSN = $certEnvironment->getMerchantCertSN();
            $config->alipayRootCertSN = $certEnvironment->getRootCertSN();
            $config->alipayPublicKey = $certEnvironment->getCachedAlipayPublicKey();
        }

        $kernel = new EasySDKKernel($config);
        $this->base = new Base($kernel);
        $this->marketing = new Marketing($kernel);
        $this->member = new Member($kernel);
        $this->payment = new Payment($kernel);
        $this->security = new Security($kernel);
        $this->util = new Util($kernel);
    }

    /**
     * 使用原始配置初始化 AlipaySDK.
     */
    public static function setOptions(Config $config): AlipaySDK
    {
        return new self($config);
    }

    public function base(): Base
    {
        return $this->base;
    }

    public function marketing(): Marketing
    {
        return $this->marketing;
    }

    public function member(): Member
    {
        return $this->member;
    }

    public function payment(): Payment
    {
        return $this->payment;
    }

    public function security(): Security
    {
        return $this->security;
    }

    public function util(): Util
    {
        return $this->util;
    }
}

class Base
{
    private $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    public function image(): imageClient
    {
        return new imageClient($this->kernel);
    }

    public function oauth(): oauthClient
    {
        return new oauthClient($this->kernel);
    }

    public function qrcode(): qrcodeClient
    {
        return new qrcodeClient($this->kernel);
    }

    public function video(): videoClient
    {
        return new videoClient($this->kernel);
    }
}

class Marketing
{
    private $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    public function openLife(): openLifeClient
    {
        return new openLifeClient($this->kernel);
    }

    public function pass(): passClient
    {
        return new passClient($this->kernel);
    }

    public function templateMessage(): templateMessageClient
    {
        return new templateMessageClient($this->kernel);
    }
}

class Member
{
    private $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    public function identification(): identificationClient
    {
        return new identificationClient($this->kernel);
    }
}

class Payment
{
    private $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    public function app(): appClient
    {
        return new appClient($this->kernel);
    }

    public function common(): commonClient
    {
        return new commonClient($this->kernel);
    }

    public function faceToFace(): faceToFaceClient
    {
        return new faceToFaceClient($this->kernel);
    }

    public function huabei(): huabeiClient
    {
        return new huabeiClient($this->kernel);
    }

    public function page(): pageClient
    {
        return new pageClient($this->kernel);
    }

    public function wap(): wapClient
    {
        return new wapClient($this->kernel);
    }
}

class Security
{
    private $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    public function textRisk(): textRiskClient
    {
        return new textRiskClient($this->kernel);
    }
}

class Util
{
    private $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    public function generic(): genericClient
    {
        return new genericClient($this->kernel);
    }

    public function aes(): aesClient
    {
        return new aesClient($this->kernel);
    }
}
