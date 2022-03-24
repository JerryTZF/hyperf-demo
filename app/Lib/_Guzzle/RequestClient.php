<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/24 17:39
 * Author: JerryTian<tzfforyou@163.com>
 * File: RequestClient.php
 * Desc:
 */


namespace App\Lib\_Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ResponseInterface;

class RequestClient
{
    /**
     * Guzzle 客户端
     * @var Client
     */
    protected Client $client;

    /**
     * HTTP 请求方法
     * @var string
     */
    protected string $method;

    /**
     * HTTP 请求地址
     * @var string
     */
    protected string $url;

    /**
     * HTTP 请求选项
     * \GuzzleHttp\RequestOptions
     * @var array
     */
    protected array $options = [];

    private function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * 获取一个 HTTP 请求实例
     *
     * @param array $options Guzzle 配置 可选
     * @return RequestClient 请求实例
     */
    public static function instance(array $options = []): RequestClient
    {
        $client = new ClientFactory(ApplicationContext::getContainer());
        return new self($client->create($options));
    }

    // ======================================== 设置 Options ========================================

    /**
     * 设置 options
     *
     * 全量替换本实例的 options
     * 具体参数请参考 \GuzzleHttp\RequestOptions
     *
     * @link https://docs.guzzlephp.org/en/stable/request-options.html
     *
     * @param array $options \GuzzleHttp\RequestOptions
     * @return RequestClient 请求实例
     */
    public function setOptions(array $options): RequestClient
    {
        $this->options = $options;
        return $this;
    }

    /**
     * 设置 options.headers
     *
     * 全量替换 options.headers
     *
     * @param array $headers
     * @return RequestClient 请求实例
     */
    public function setHeaders(array $headers): RequestClient
    {
        $this->options['headers'] = $headers;
        return $this;
    }

    /**
     * 设置 options.query
     *
     * 全量替换 options.query
     *
     * @param array $query
     * @return RequestClient 请求实例
     */
    public function setQuery(array $query): RequestClient
    {
        $this->options['query'] = $query;
        return $this;
    }

    /**
     * 设置 options.form
     *
     * 全量替换 options.form
     *
     * @param array $form
     * @return RequestClient 请求实例
     */
    public function setForm(array $form): RequestClient
    {
        $this->options['form_params'] = $form;
        return $this;
    }

    /**
     * 设置 options.json
     *
     * 全量替换 options.json
     *
     * @param array $json
     * @return RequestClient 请求实例
     */
    public function setJson(array $json): RequestClient
    {
        $this->options['json'] = $json;
        return $this;
    }

    // ========================================= 设置 Method ========================================

    /**
     * 使用 GET 请求
     *
     * @param string $url
     * @return RequestClient 请求实例
     */
    public function get(string $url): RequestClient
    {
        return $this->method('GET', $url);
    }

    /**
     * 自定义请求方法
     *
     * @param string $method
     * @param string $url
     * @return RequestClient 请求实例
     */
    public function method(string $method, string $url): RequestClient
    {
        $this->method = $method;
        $this->url = $url;
        return $this;
    }

    /**
     * 使用 POST 请求
     *
     * @param string $url
     * @return RequestClient 请求实例
     */
    public function post(string $url): RequestClient
    {
        return $this->method('POST', $url);
    }

    // ========================================== 结果 ==============================================

    /**
     * 将结果转换为 JSON
     *
     * @return array
     */
    public function json(): array
    {
        $result = $this->result();
        if (is_array($result)) {
            return $result;
        }
        $body = $result->getBody()->getContents();
        $json = json_decode($body, true);
        if ($json === null) {
            return [
                'status' => false,
                'msg'    => 'JSON 解析失败',
                'data'   => $body,
            ];
        }

        return [
            'status' => true,
            'msg'    => '',
            'data'   => $json
        ];
    }

    /**
     * 请求
     *
     * @return ResponseInterface|array
     */
    private function result(): array|ResponseInterface
    {
        $error = [
            'status' => false,
            'msg'    => '请求失败',
            'data'   => [],
        ];
        try {
            return $this->client->request($this->method, $this->url, $this->options);
        } catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $error['data']['code'] = $response->getStatusCode();
                $error['data']['body'] = $response->getBody()->getContents();
            }
            $error['msg'] = $e->getMessage();
        } catch (GuzzleException $e) {
            $error['msg'] = $e->getMessage();
        }
        return $error;
    }

    /**
     * 获取结果字符串
     *
     * @return array
     */
    public function plain(): array
    {
        $result = $this->result();
        if (is_array($result)) {
            return $result;
        }
        return [
            'status' => true,
            'msg'    => '',
            'data'   => $result->getBody()->getContents()
        ];
    }

    /**
     * 获取 ResponseInterface
     *
     * @return array
     */
    public function response(): array
    {
        $result = $this->result();
        if (is_array($result)) {
            return $result;
        }
        return [
            'status' => true,
            'msg'    => '',
            'data'   => $result,
        ];
    }
}