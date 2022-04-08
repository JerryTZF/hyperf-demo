<?php

declare(strict_types=1);

namespace App\Lib\_Alipay;

use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use App\Lib\_Log\Log;
use Exception;

/**
 * 支付宝 SDK 封装.
 */
class AlipayApi
{
    /**
     * APP ID
     * @var string
     */
    protected string $appId;

    /**
     * 配置项
     * @var array
     */
    protected array $options;

    /**
     * 是否是ISV代调用
     * @var string
     */
    protected string $ISV = '';

    /**
     * 证书目录
     * @var string
     */
    protected string $baseCertPath = BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Cert';

    /**
     * 私有构造函数 禁止自行初始化
     * @param string $appId
     * @param array $options
     */
    private function __construct(string $appId, array $options)
    {
        $this->appId = $appId;
        $this->options = $options;
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    }

    /**
     * 获取 AlipayApi 实例
     * @param string $appId
     * @param array $options
     * @return AlipayApi
     */
    public static function setOptions(string $appId, array $options = []): AlipayApi
    {
        return new static($appId, $options);
    }

    /**
     * alipay.open.auth.token.app 换取(刷新)应用授权令牌
     * https://opendocs.alipay.com/apis/api_9/alipay.open.auth.token.app
     * @param string $certificateCode
     * @param bool $isRefresh
     * @return array
     */
    public function oauthAppCode2Token(string $certificateCode, bool $isRefresh = false): array
    {
        try {
            $sdk = $this->getSDK();
            $method = 'alipay.open.auth.token.app';
            $bizParams = $isRefresh ? [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $certificateCode
            ] : [
                'grant_type' => 'authorization_code',
                'code'       => $certificateCode
            ];

            if ($this->ISV !== '') {
                $result = $sdk->util()->generic()->agent($this->ISV)->execute($method, [], $bizParams);
            } else {
                $result = $sdk->util()->generic()->execute($method, [], $bizParams);
            }

            $responseChecker = new ResponseChecker();
            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }
            $tokens = json_decode($result->httpBody)->alipay_open_auth_token_app_response->tokens;
            return [
                'status' => true,
                'msg'    => 'ok',
                'data'   => [
                    'app_auth_token'    => $tokens->app_auth_token, // 令牌
                    'app_refresh_token' => $tokens->app_refresh_token, // 刷新令牌
                    'auth_app_id'       => $tokens->auth_app_id, // 授权小程序ID(商家该小程序的ID)
                    're_expires_in'     => $tokens->re_expires_in, // 刷新令牌有效时间
                    'expires_in'        => 157680000, // 令牌有效时间(永久有效,除非解除授权)
                    'user_id'           => $tokens->user_id // 商家支付宝ID
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败, 原因: ' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.open.app.members.create(应用添加成员)
     * https://opendocs.alipay.com/apis/api_49/alipay.open.app.members.create/
     * @param string $account
     * @param string $role
     * @return array
     */
    public function createMembers(string $account, string $role = 'DEVELOPER'): array
    {
        try {
            $sdk = $this->getSDK();
            $method = 'alipay.open.app.members.create';
            $bizParams = [
                'logon_id' => $account,
                'role'     => $role
            ];
            if ($this->ISV !== '') {
                $result = $sdk->util()->generic()->agent($this->ISV)->execute($method, [], $bizParams);
            } else {
                $result = $sdk->util()->generic()->execute($method, [], $bizParams);
            }

            $responseChecker = new ResponseChecker();

            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }

            return [
                'status' => true,
                'msg'    => 'ok',
                'data'   => []
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败, 原因: ' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * 初始化 SDK
     * @return AlipaySDK
     * @throws Exception
     */
    private function getSDK(): AlipaySDK
    {
        $base = [
            'protocol'    => 'https',
            'gatewayHost' => 'openapi.alipay.com',
            'signType'    => 'RSA2',
            'notifyUrl'   => $this->options['callback'] ?? '',
            'appId'       => $this->appId,
        ];

        // 设置证书目录
        if (isset($this->options['cert_path'])) {
            $path = BASE_PATH . DIRECTORY_SEPARATOR . trim($this->options['cert'], DIRECTORY_SEPARATOR);
            if (!is_dir($path)) {
                throw new Exception("证书目录错误");
            }
            $this->baseCertPath = $path;
        }

        // 设置应用私钥
        if (isset($this->options['private_key'])) {
            $base['merchantPrivateKey'] = $this->options['private_key'];
        } else {
            $privateKey = file_get_contents($this->pathJoinWithCheck($this->appId, 'private.key'));
            if ($privateKey === false) {
                throw new Exception('私钥文件读取失败');
            }
            $base['merchantPrivateKey'] = trim($privateKey);
        }

        // 设置支付宝公钥或证书
        if (isset($this->options['public_key'])) {
            // 普通公钥模式
            $base['alipayPublicKey'] = $this->options['public_key'];
        } elseif (is_file($this->pathJoin($this->appId, 'public.key'))) {
            // 普通公钥模式
            $base['alipayPublicKey'] = file_get_contents($this->pathJoinWithCheck($this->appId, 'public.key'));
        } else {
            // 公钥证书模式
            $base['alipayCertPath'] = $this->pathJoinWithCheck($this->appId, 'alipayCertPublicKey_RSA2.crt');
            $base['alipayRootCertPath'] = $this->pathJoinWithCheck($this->appId, 'alipayRootCert.crt');
            $base['merchantCertPath'] = $this->pathJoinWithCheck($this->appId, "appCertPublicKey_$this->appId.crt");
        }

        // aes 密钥 可选
        if (isset($this->options['aes'])) {
            $base['encryptKey'] = $this->options['aes'];
        } else {
            $aesKeyPath = $this->pathJoin($this->appId, 'aes.key');
            if (is_file($aesKeyPath)) {
                $base['encryptKey'] = trim(file_get_contents($aesKeyPath));
            }
        }

        // 是否是ISV调用
        // 有些API(alipay.open.auth.token.app)不需要ISV参数，有一些又必须要(ISV角色调用支付宝API时)
        if (isset($this->options['isv_token'])) {
            $this->ISV = $this->options['isv_token'];
        } elseif (isset($this->options['isv_must']) && $this->options['isv_must'] === false) {
            $this->ISV = '';
        } else {
            $isvToken = $this->pathJoin($this->appId, 'isv.token');
            if (is_file($isvToken)) {
                $this->ISV = trim(file_get_contents($isvToken));
            }
        }

        $config = new Config($base);
        return AlipaySDK::setOptions($config);
    }

    /**
     * 路径拼接并检查文件是否存在
     * @param mixed ...$paths
     * @return string
     * @throws Exception
     */
    private function pathJoinWithCheck(...$paths): string
    {
        $path = $this->pathJoin(...$paths);
        if (!is_file($path)) {
            throw new Exception('证书未找到');
        }
        return $path;
    }

    /**
     * 路径拼接
     * @param string ...$paths
     * @return string
     */
    private function pathJoin(...$paths): string
    {
        return $this->baseCertPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths);
    }

    /**
     * alipay.system.oauth.token(换取授权访问令牌)
     * https://opendocs.alipay.com/apis/api_9/alipay.system.oauth.token
     * @param string $code
     * @return array
     */
    public function oauthCode2Token(string $code): array
    {
        try {
            $sdk = $this->getSDK();
            $result = $this->ISV !== '' ? $sdk->base()->oauth()->agent($this->ISV)->getToken($code) :
                $sdk->base()->oauth()->getToken($code);
            $responseChecker = new ResponseChecker();

            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => $result,
                ];
            }

            return [
                'status' => true,
                'msg'    => 'success',
                'data'   => [
                    'accessToken' => $result->accessToken,
                    'userId'      => $result->userId,
                    'expiresIn'   => time() + $result->expiresIn,
                ],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败, 原因: ' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * TOOL:支付宝 execute 调用封装
     *
     * @param string $method 调用 API
     * @param array $textParams 没有包装在biz_content下的请求参数集合，例如app_auth_token等参数
     * @param array $bizParams 被包装在biz_content下的请求参数集合
     * @param string $accessToken 用户授权调用，指定authToken
     * @return array
     */
    public function execute(string $method, array $textParams = [], array $bizParams = [], string $accessToken = ''): array
    {
        try {
            $sdk = $this->getSDK();
            $sdk = $sdk->util()->generic();
            // 如果传递了 authToken 进行认证
            if ($accessToken !== '') {
                $sdk = $sdk->auth($accessToken);
            }
            $result = $sdk->execute($method, $textParams, $bizParams);
            $responseChecker = new ResponseChecker();

            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }

            return [
                'status' => true,
                'msg'    => 'success',
                'data'   => json_decode($result->httpBody, true),
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * TOOL:支付宝 fileExecute 调用封装
     *
     * @param string $method 调用 API
     * @param array $textParams 没有包装在biz_content下的请求参数集合，例如app_auth_token等参数
     * @param array $bizParams 被包装在biz_content下的请求参数集合
     * @param array $files 文件
     * @return array
     */
    public function fileExecute(string $method, array $textParams = [], array $bizParams = [], array $files = []): array
    {
        try {
            $sdk = $this->getSDK();
            $result = $sdk->util()->generic()->fileExecute($method, $textParams, $bizParams, $files);
            $responseChecker = new ResponseChecker();

            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }

            return [
                'status' => true,
                'msg'    => 'success',
                'data'   => json_decode($result->httpBody, true),
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.trade.refund(统一收单交易退款接口)
     * https://opendocs.alipay.com/apis/api_1/alipay.trade.refund
     * @param string $outTradeNo 订单号
     * @param string $refundAmount 退款金额
     * @return array
     */
    public function refund(string $outTradeNo, string $refundAmount): array
    {
        try {
            $sdk = $this->getSDK();
            $result = $sdk->payment()->common()->refund($outTradeNo, $refundAmount);
            $responseCheck = new ResponseChecker();

            // 退款成功
            if ($responseCheck->success($result)) {
                return [
                    'status' => true,
                    'msg'    => 'success',
                    'data'   => [
                        'out_trade_no' => $result->outTradeNo,
                        'trade_no'     => $result->tradeNo,
                        'fund_change'  => $result->fundChange,
                        'refund_fee'   => $result->refundFee,
                    ],
                ];
            }

            Log::get('AlipayService@refund')->error($result->subMsg);
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                'data'   => [],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.fund.trans.uni.transfer(单笔转账接口)
     * https://opendocs.alipay.com/apis/api_28/alipay.fund.trans.uni.transfer
     * @param string $orderNum
     * @param float $transAmount
     * @param string $redpackName
     * @param string $userId
     * @return array
     */
    public function setTransferB2C(string $orderNum, float $transAmount, string $redpackName, string $userId): array
    {
        try {
            $sdk = $this->getSDK();
            $method = 'alipay.fund.trans.uni.transfer';
            $bizParams = [
                'out_biz_no'      => $orderNum,
                'product_code'    => 'STD_RED_PACKET',
                'trans_amount'    => $transAmount,
                'biz_scene'       => 'DIRECT_TRANSFER',
                'order_title'     => $redpackName,
                'remark'          => '单笔现金红包转账',
                'business_params' => [
                    'sub_biz_scene'   => 'REDPACKET',
                    'payer_show_name' => '积分有礼现金红包' # 这里的名字是用户收到现金后支付宝展示的文案
                ],
                'payee_info'      => [
                    'identity'      => $userId,
                    'identity_type' => 'ALIPAY_USER_ID'
                ]
            ];

            // 该api不允许ISV调用
            $result = $sdk->util()->generic()->execute($method, [], $bizParams);
            $responseChecker = new ResponseChecker();
            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }

            $result = json_decode($result->httpBody)->alipay_fund_trans_uni_transfer_response;
            return [
                'status' => true,
                'msg'    => 'ok',
                'data'   => [
                    'order_id'   => $result->order_id, # 支付宝转账订单号
                    'out_biz_no' => $result->out_biz_no, # 商户订单号
                    'status'     => $result->status, # SUCCESS|FAIL|DEALING|REFUND
                    'trans_date' => $result->trans_date # 订单支付时间，格式为yyyy-MM-dd HH:mm:ss
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败, 原因: ' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.trade.create(统一收单交易创建接口)
     * https://opendocs.alipay.com/apis/api_1/alipay.trade.create
     * @param string $subject
     * @param string $outTradeNo
     * @param string $totalAmount
     * @param string $buyerId
     * @return array
     */
    public function createTrade(string $subject, string $outTradeNo, string $totalAmount, string $buyerId): array
    {
        try {
            $sdk = $this->getSDK();
            $result = $sdk->payment()
                ->common()
                ->optional('timeout_express', (object)'10m') // 付款有效期10分钟
                ->create($subject, $outTradeNo, $totalAmount, $buyerId);
            $responseCheck = new ResponseChecker();

            Log::get('AlipayService@createTrade')->info(json_encode($result, JSON_UNESCAPED_UNICODE));

            if ($responseCheck->success($result)) {
                return [
                    'status' => true,
                    'msg'    => 'success',
                    'data'   => [
                        'out_trade_no' => $result->outTradeNo,
                        'trade_no'     => $result->tradeNo
                    ]
                ];
            }

            Log::get('AlipayService@createTrade')->error($result->subMsg);
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                'data'   => [],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.trade.query(统一收单线下交易查询)
     * https://opendocs.alipay.com/apis/api_1/alipay.trade.query?scene=23
     * @param string $outTradeNo
     * @return array
     */
    public function queryTrade(string $outTradeNo): array
    {
        try {
            $sdk = $this->getSDK();
            $result = $sdk->payment()->common()->query($outTradeNo);
            $responseCheck = new ResponseChecker();

            if ($responseCheck->success($result)) {
                return [
                    'status' => true,
                    'msg'    => 'success',
                    'data'   => [
                        'out_trade_no'       => $result->outTradeNo,
                        'trade_no'           => $result->tradeNo,
                        'buyer_logon_id	' => $result->buyerLogonId,
                        'trade_status'       => $result->tradeStatus,
                        'total_amount'       => $result->totalAmount
                    ]
                ];
            }

            Log::get('AlipayService@queryTrade')->error($result->subMsg);
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                'data'   => [],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.trade.close(统一收单交易关闭接口)
     * https://opendocs.alipay.com/apis/api_1/alipay.trade.close
     * @param string $outTradeNo
     * @return array
     */
    public function closeTrade(string $outTradeNo): array
    {
        try {
            $sdk = $this->getSDK();
            $result = $sdk->payment()->common()->close($outTradeNo);
            $responseCheck = new ResponseChecker();

            if ($responseCheck->success($result)) {
                return [
                    'status' => true,
                    'msg'    => 'success',
                    'data'   => [
                        'out_trade_no' => $result->outTradeNo,
                        'trade_no'     => $result->tradeNo,
                    ]
                ];
            }

            Log::get('AlipayService@queryTrade')->error($result->subMsg);

            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                'data'   => [],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * TOOL:解密敏感数据(手机号)
     * @param string $decrypt
     * @return array
     */
    public function getUserPhone(string $decrypt): array
    {
        try {
            $sdk = $this->getSDK();
            $result = json_decode($sdk->util()->aes()->decrypt($decrypt));
            $responseCheck = new ResponseChecker();

            if ($responseCheck->success($result)) {
                return [
                    'status' => true,
                    'msg'    => 'success',
                    'data'   => ['mobile' => $result->mobile ?? '']
                ];
            }

            Log::get('AlipayService@decrypt')->error($result->subMsg);
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                'data'   => [],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * TOOL: 解密优化运动步数
     * @param string $encryption
     * @return array
     */
    public function getUserWalkCount(string $encryption): array
    {
        try {
            $sdk = $this->getSDK();
            $result = json_decode($sdk->util()->aes()->decrypt($encryption));
            $responseCheck = new ResponseChecker();
            if (!$responseCheck->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }

            return [
                'status' => true,
                'msg'    => 'ok',
                'data'   => [
                    'date'  => $result->countDate,
                    'count' => $result->count
                ]
            ];

        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败, 原因: ' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.user.alipaypoint.send(集分宝发放接口)
     * https://opendocs.alipay.com/apis/api_2/alipay.user.alipaypoint.send
     * @param string $budgetCode
     * @param string $userID
     * @param string $bizNo
     * @param int $amount
     * @return array
     */
    public function sendAlipayPoint(string $budgetCode, string $userID, string $bizNo, int $amount): array
    {
        try {
            $sdk = $this->getSDK();
            $method = 'alipay.user.alipaypoint.send';
            $bizParams = [
                'user_id'        => $userID,
                'budget_code'    => $budgetCode,
                'point_amount'   => $amount,
                'partner_biz_no' => $bizNo
            ];

            if ($this->ISV !== '') {
                $result = $sdk->util()->generic()->agent($this->ISV)->execute($method, [], $bizParams);
            } else {
                $result = $sdk->util()->generic()->execute($method, [], $bizParams);
            }
            $responseChecker = new ResponseChecker();

            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }

            return [
                'status' => true,
                'msg'    => 'ok',
                'data'   => []
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败, 原因: ' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.user.alipaypoint.budgetlib.query(查询集分宝预算库详情)
     * https://opendocs.alipay.com/mini/02pk5c
     * @param string $budgetCode
     * @return array
     */
    public function queryAlipayPoint(string $budgetCode): array
    {
        try {
            $sdk = $this->getSDK();
            $method = 'alipay.user.alipaypoint.budgetlib.query';
            $bizParams = [
                'budget_code' => $budgetCode
            ];
            if ($this->ISV !== '') {
                $result = $sdk->util()->generic()->agent($this->ISV)->execute($method, [], $bizParams);
            } else {
                $result = $sdk->util()->generic()->execute($method, [], $bizParams);
            }
            $responseChecker = new ResponseChecker();

            // 调用失败
            if (!$responseChecker->success($result)) {
                return [
                    'status' => false,
                    'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                    'data'   => [],
                ];
            }

            $result = json_decode($result->httpBody)->alipay_user_alipaypoint_budgetlib_query_response;
            return [
                'status' => true,
                'msg'    => 'ok',
                'data'   => [
                    'budget_desc'       => $result->budget_desc,
                    'budget_code'       => $result->budget_code,
                    'enabled'           => $result->enabled,
                    'cumulative_amount' => $result->cumulative_amount,
                    'remain_amount'     => $result->remain_amount,
                    'start_time'        => $result->start_time,
                    'end_time'          => $result->end_time
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败, 原因: ' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * alipay.open.app.mini.templatemessage.send(小程序发送模板消息)
     * https://opendocs.alipay.com/apis/api_5/alipay.open.app.mini.templatemessage.send
     * @param string $toUserId
     * @param string $userTemplateId
     * @param string $page
     * @param array $data
     * @param string|null $formId
     * @return array
     */
    public function sendTemplateMessage(string $toUserId, string $userTemplateId, string $page, array $data, string $formId = null): array
    {
        try {
            $sdk = $this->getSDK();
            $result = $sdk->marketing()
                ->templateMessage()
                ->send($toUserId, $formId, $userTemplateId, $page, json_encode($data, JSON_UNESCAPED_UNICODE));
            $responseCheck = new ResponseChecker();

            if ($responseCheck->success($result)) {
                return [
                    'status' => true,
                    'msg'    => 'success',
                    'data'   => $result
                ];
            }

            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $result->msg . '，' . $result->subMsg,
                'data'   => $result,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg'    => '调用失败，原因：' . $e->getMessage(),
                'data'   => [],
            ];
        }
    }

    /**
     * TOOL:获取 AlipaySDK 实例
     * @return AlipaySDK
     * @throws Exception
     */
    public function sdk(): AlipaySDK
    {
        return $this->getSDK();
    }
}
