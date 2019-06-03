<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge;

use JimChen\Recharge\Contracts\GatewayInterface;
use JimChen\Recharge\Exceptions\InvalidGatewayException;
use JimChen\Recharge\Support\Str;

/**
 * @method static GatewayInterface gm(array $config = array())
 * @method static GatewayInterface jisu(array $config = array())
 */
class Recharge
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Create a instance.
     *
     * @param $method
     *
     * @return GatewayInterface
     *
     * @throws InvalidGatewayException
     */
    protected function create($method)
    {
        $gateway = __NAMESPACE__.'\\Gateways\\'.Str::studly($method).'Gateway';

        if (class_exists($gateway)) {
            return $this->make($gateway);
        }

        throw new InvalidGatewayException("Gateway [{$method}] Not Exists");
    }

    /**
     * Make a gateway.
     *
     * @param $gateway
     *
     * @return GatewayInterface
     *
     * @throws InvalidGatewayException
     */
    protected function make($gateway)
    {
        $app = new $gateway($this->config);

        if ($app instanceof GatewayInterface) {
            return $app;
        }

        throw new InvalidGatewayException("Gateway [$gateway] Must Be An Instance Of GatewayInterface");
    }

    /**
     * Magic static call.
     *
     *
     * @param string $method
     * @param array  $params
     *
     * @return GatewayInterface
     *
     * @throws InvalidGatewayException
     */
    public static function __callStatic($method, $params)
    {
        $app = new self(array_shift($params));

        return $app->create($method);
    }
}
