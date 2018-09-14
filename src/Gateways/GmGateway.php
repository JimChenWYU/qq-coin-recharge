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
use JimChen\Recharge\Support\Collection;
use JimChen\Recharge\Traits\HasHttpRequest;
use Symfony\Component\HttpFoundation\Request;

class GmGateway extends Gateway
{
    use HasHttpRequest;

    protected $payload;

    const ENDPOINT_URL = 'http://api.gm193.com';

    const TEST_ENDPOINT_URL = 'http://apitest.gm193.com';

    /**
     * Constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->payload = [
            'username' => $this->config->get('username'),
            'gameapi'  => $this->config->get('gameapi', 'ypesqb'),
            'clientip' => $this->config->get('clientip', Request::createFromGlobals()->getClientIp()),
            'returl'   => $this->config->get('returl'),
        ];
    }

    /**
     * Recharge.
     *
     * @param array $payload
     *
     * @return Collection
     *
     * @throws GatewayErrorException
     * @throws InvalidArgumentException
     */
    public function recharge(array $payload)
    {
        $requestParams = array_merge($this->payload, [
            'account'   => $payload['account'],
            'buynum'    => $payload['buynum'],
            'sporderid' => $payload['sporderid'],
        ]);

        $signParams = [
            'username'  => $requestParams['username'],
            'gameapi'   => $requestParams['gameapi'],
            'sporderid' => $payload['sporderid'],
        ];

        $requestParams['sign'] = $this->generateSign($signParams, $this->config->get('api_key'));

        $contents = $this->get('post/gaorder.asp', $requestParams);

        $contents = $this->unwrapResponseContents($contents);

        if ($contents['info']['ret'] != 0) {
            throw new GatewayErrorException($contents['info']['ret_msg'], $contents['info']['ret']);
        }

        return new Collection($contents['gaorder']);
    }

    /**
     * Find order.
     *
     * @param $order
     *
     * @return Collection
     *
     * @throws GatewayErrorException
     * @throws InvalidArgumentException
     */
    public function find($order)
    {
        $requestParams = array_merge([
            'username' => $this->payload['username'],
            'gameapi'  => $this->payload['gameapi'],
        ], is_array($order) ? $order : [
            'sporderid' => $order,
        ]);

        $requestParams['sign'] = $this->generateSign($requestParams, $this->config->get('api_key'));

        $contents = $this->get('post/gasearch.asp', $requestParams);

        $contents = $this->unwrapResponseContents($contents);

        if ($contents['info']['ret'] != 0) {
            throw new GatewayErrorException($contents['info']['ret_msg'], $contents['info']['ret']);
        }

        return new Collection($contents['gasearch']);
    }

    /**
     * Verify signature
     *
     * @param array $params
     * @return bool
     * @throws InvalidArgumentException
     */
    public function verify(array $params)
    {
        $sign = $params['sign'];
        unset($params['sign'], $params['money']);

        $generateSign = $this->generateSign($params, $this->config->get('api_key'));

        return $sign === $generateSign;
    }
    
    /**
     * @return string
     */
    protected function getBaseUri()
    {
        if ($this->config->get('mode') === 'dev') {
            return self::TEST_ENDPOINT_URL;
        }

        return self::ENDPOINT_URL;
    }

    /**
     * Generate sign
     *
     * @param array  $payload
     * @param string $key
     * @return string
     * @throws InvalidArgumentException
     */
    protected function generateSign(array $payload, $key = null)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException('Missing Gm Config -- [key]');
        }

        return md5($this->getSignContent($payload) . '||' . $key);
    }

    /**
     * Generate sign content.
     *
     * @param array $data
     *
     * @return string
     */
    protected function getSignContent($data)
    {
        $buff = '';

        foreach ($data as $k => $v) {
            $buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k . '=' . $v . '&' : '';
        }

        return trim($buff, '&');
    }

    /**
     * unwrap response contents
     *
     * @param $contents
     *
     * @return mixed
     */
    protected function unwrapResponseContents($contents)
    {
        if (is_array($contents)) {
            return $contents;
        }

        $contents = str_replace(PHP_EOL, '', $contents);

        $libxml = libxml_disable_entity_loader(true);
        $contents = json_decode(json_encode(simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
        libxml_disable_entity_loader($libxml);

        return $contents;
    }
}
