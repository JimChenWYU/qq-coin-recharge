<?php

namespace JimChen\Recharge\Gateways;

use JimChen\Recharge\Exceptions\GatewayErrorException;
use JimChen\Recharge\Exceptions\InvalidArgumentException;
use JimChen\Recharge\Support\Arr;
use JimChen\Recharge\Support\Collection;
use JimChen\Recharge\Traits\HasHttpRequest;

class JisuGateway extends Gateway
{
    use HasHttpRequest;

    protected $payload;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->payload = [
            'appkey'    => $this->config->get('appkey'),
            'appsecret' => $this->config->get('appsecret'),
            'typeid'    => $this->config->get('typeid', 1),
        ];

        if (empty($this->payload['appkey'])) {
            throw new InvalidArgumentException("Missing Jisu Config -- [appkey]");
        }
        if (empty($this->payload['appsecret'])) {
            throw new InvalidArgumentException("Missing Jisu Config -- [appsecret]");
        }
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        return 'https://api.jisuapi.com';
    }

    /**
     * 充值
     *
     * @param array $payload
     * @return Collection
     * @throws GatewayErrorException
     * @throws InvalidArgumentException
     */
    public function recharge(array $payload)
    {
        $requestParams = [
            'appkey'     => $this->payload['appkey'],
            'qq'         => $payload['account'],
            'num'        => $payload['buynum'],
            'outorderno' => $payload['sporderid'],
            'typeid'     => Arr::get($payload, 'typeid', $this->payload['typeid']),
        ];

        $signParams = [
            'num'        => $requestParams['num'],
            'outorderno' => $requestParams['outorderno'],
            'qq'         => $requestParams['qq'],
            'typeid'     => $requestParams['typeid'],
        ];
        $requestParams['sign'] = $this->generateSign($signParams, $this->payload['appsecret']);

        $contents = $this->wrapContents($this->get('tencentrecharge/recharge', $requestParams));

        if (0 != $contents['status']) {
            throw new GatewayErrorException($contents['msg'], $contents['status']);
        }

        return new Collection(Arr::get($contents, 'result', []));
    }

    /**
     * 查找订单
     *
     * @param $order
     * @return Collection
     * @throws GatewayErrorException
     */
    public function find($order)
    {
        $requestParams = array_merge([
            'appkey' => $this->payload['appkey'],
        ], is_array($order) ? $order : [
            'outorderno' => $order,
        ]);

        $contents = $this->wrapContents($this->post('tencentrecharge/orderdetail', $requestParams));

        if (0 != $contents['status']) {
            throw new GatewayErrorException($contents['msg'], $contents['status']);
        }

        return new Collection(Arr::get($contents, 'result', []));
    }

    /**
     * 验证那个签名
     *
     * @param array $params
     * @return bool
     * @throws InvalidArgumentException
     */
    public function verify(array $params)
    {
        if (! isset($params['sign'])) {
            return false;
        }

        $sign = $params['sign'];

        unset($params['sign']);

        $generateSign = $this->generateSign($params, $this->payload['appsecret']);

        return $sign === $generateSign;
    }

    /**
     * 生成签名
     *
     * @param array $payload
     * @param null  $key
     */
    protected function generateSign(array $payload, $key = null)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException('Missing Jisu Config -- [key]');
        }

        return md5($this->getSignContent($payload) . $key);
    }

    /**
     * 生成待签名内容
     *
     * @param array $data
     *
     * @return string
     */
    protected function getSignContent($data)
    {
        ksort($data, SORT_STRING);

        return implode($data);
    }

    /**
     * 极速 API 的 Response 请求头缺乏对返回数据格式描述，故需要手动进行 Json 格式化处理
     *
     * @param $contents
     * @return mixed
     */
    protected function wrapContents($contents)
    {
        if (is_string($contents)) {
            $contents = json_decode($contents, true);
        }

        return $contents;
    }
}
