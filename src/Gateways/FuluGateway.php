<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Gateways;

use JimChen\Recharge\Exceptions\GatewayErrorException;
use JimChen\Recharge\Exceptions\InvalidArgumentException;
use JimChen\Recharge\Gateways\Fulu\FuluGatewayHelper;
use JimChen\Recharge\Support\Arr;
use JimChen\Recharge\Support\Collection;
use JimChen\Recharge\Traits\HasHttpRequest;
use Symfony\Component\HttpFoundation\Request;

class FuluGateway extends Gateway
{
    use HasHttpRequest;
    use FuluGatewayHelper;

    /**
     * @var array
     */
    protected $payload;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->payload = [
            'appkey'    => $this->config->get('appkey'),
            'appsecret' => $this->config->get('appsecret'),
            'timeStamp' => date('Y-m-d H:i:s'),
            'v'         => '1.0',
            'format'    => 'json',
            'sign_type' => 'md5',
            'clientip'  => Request::createFromGlobals()->getClientIp(),
            'returl'    => $this->config->get('returl'),
            'typeid'    => '',
        ];

        if (empty($this->payload['appkey'])) {
            throw new InvalidArgumentException('Missing Fulu Config -- [appkey]');
        }
        if (empty($this->payload['appsecret'])) {
            throw new InvalidArgumentException('Missing Fulu Config -- [appsecret]');
        }

        $this->preInit();
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        return 'http://api.open.fulu.com/api/';
    }

    /**
     * @param array $config_biz
     *
     * @return Collection
     */
    public function recharge(array $payload)
    {
        $requestParams = array_merge($this->publicRequestParams($payload), [
            'BuyerIP'         => Arr::get($payload, 'clientip', $this->payload['clientip']),
            'NotifyUrl'       => Arr::get($payload, 'returl', $this->payload['returl']),
            'ProductId'       => $this->getProductId($payload['buynum']),
            'BuyNum'          => $this->getBuyNum($payload['buynum']),
            'ChargeAccount'   => $payload['account'],
            'CustomerOrderNo' => $payload['sporderid'],
        ]);

        $requestParams = array_merge($requestParams, Arr::only($payload, [
            'ChargePassword',
            'ChargeGame',
            'ChargeRegion',
            'ChargeServer',
            'ChargeType',
            'RoleName',
            'ContactPhone',
            'ContactQQ',
        ]));

        $requestParams['Sign'] = $this->generateSign($requestParams, $this->payload['appsecret']);

        $contents = $this->postJson('Order/CreateOrder', $requestParams);
        if ('Success' !== $contents['State']) {
            throw new GatewayErrorException($contents['Message'], $contents['Code']);
        }

        return new Collection(Arr::get($contents, 'Result', []));
    }

    /**
     * @param string|array $order
     *
     * @return Collection
     */
    public function find($order)
    {
        $requestParams = array_merge($this->publicRequestParams($order), [
            'ProductId'       => Arr::get($order, 'typeid', $this->payload['typeid']),
            'BuyerIP'         => Arr::get($order, 'clientip', $this->payload['clientip']),
            'CustomerOrderNo' => Arr::get($order, 'sporderid', is_string($order) ? $order : ''),
        ]);
        $requestParams['Sign'] = $this->generateSign($requestParams, $this->payload['appsecret']);

        $contents = $this->postJson('Order/GetOrder', $requestParams);
        if ('Success' !== $contents['State']) {
            throw new GatewayErrorException($contents['Message'], $contents['Code']);
        }

        return new Collection(Arr::get($contents, 'Result', []));
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function verify(array $params)
    {
        if (!isset($params['Sign'])) {
            return false;
        }

        $sign = $params['Sign'];

        unset($params['Sign']);

        $generateSign = $this->generateSign($params, $this->payload['appsecret']);

        return $sign === $generateSign;
    }

    /**
     * 公共请求参数.
     *
     * @param $payload
     *
     * @return array
     */
    protected function publicRequestParams($payload)
    {
        return [
            'AppKey'    => $this->payload['appkey'],
            'V'         => $this->payload['v'],
            'Format'    => $this->payload['format'],
            'SignType'  => $this->payload['sign_type'],
            'TimeStamp' => Arr::get($payload, 'timeStamp', $this->payload['timeStamp']),
        ];
    }

    /**
     * 生成签名.
     *
     * @param array $payload
     * @param null  $key
     */
    protected function generateSign(array $payload, $appSecret = null)
    {
        if (is_null($appSecret)) {
            throw new InvalidArgumentException('Missing Fulu Config -- [appSecret]');
        }

        return md5($this->getSignContent($payload) . $appSecret);
    }

    /**
     * 生成待签名内容.
     *
     * @param array $data
     *
     * @return string
     */
    protected function getSignContent($data)
    {
        ksort($data, SORT_STRING);

        $buff = [];

        foreach ($data as $k => $v) {
            if ('Sign' != $k && '' != $v && !is_null($v) && !is_array($v)) {
                $buff[] = $k . '=' . $v;
            }
        }

        return join('&', $buff);
    }
}
