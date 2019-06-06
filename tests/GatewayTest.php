<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use JimChen\Recharge\Gateways\Gateway;
use JimChen\Recharge\Support\Collection;
use PHPUnit\Framework\TestCase;

class GatewayTest extends TestCase
{
    public function testGetName()
    {
        $gateway = new FooGateway();
        $this->assertEquals('foo', $gateway->getName());

        $gateway = new FooBarGateway();
        $this->assertEquals('foobar', $gateway->getName());

        $gateway = new BarfooGateway();
        $this->assertEquals('barfoo', $gateway->getName());
    }
}


class FooGateway extends Gateway
{
    public function getName()
    {
        return \strtolower(str_replace([__NAMESPACE__.'\\', 'Gateway'], '', \get_class($this)));
    }

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        // TODO: Implement getBaseUri() method.
    }

    /**
     * @param array $config_biz
     * @return Collection
     */
    public function recharge(array $config_biz)
    {
        // TODO: Implement recharge() method.
    }

    /**
     * @param string|array $order
     * @return Collection
     */
    public function find($order)
    {
        // TODO: Implement find() method.
    }

    /**
     * @param array $params
     * @return bool
     */
    public function verify(array $params)
    {
        // TODO: Implement verify() method.
    }
}

class FooBarGateway extends Gateway
{
    public function getName()
    {
        return \strtolower(str_replace([__NAMESPACE__.'\\', 'Gateway'], '', \get_class($this)));
    }

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        // TODO: Implement getBaseUri() method.
    }

    /**
     * @param array $config_biz
     * @return Collection
     */
    public function recharge(array $config_biz)
    {
        // TODO: Implement recharge() method.
    }

    /**
     * @param string|array $order
     * @return Collection
     */
    public function find($order)
    {
        // TODO: Implement find() method.
    }

    /**
     * @param array $params
     * @return bool
     */
    public function verify(array $params)
    {
        // TODO: Implement verify() method.
    }
}

class BarfooGateway extends Gateway
{
    public function getName()
    {
        return \strtolower(str_replace([__NAMESPACE__.'\\', 'Gateway'], '', \get_class($this)));
    }

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        // TODO: Implement getBaseUri() method.
    }

    /**
     * @param array $config_biz
     * @return Collection
     */
    public function recharge(array $config_biz)
    {
        // TODO: Implement recharge() method.
    }

    /**
     * @param string|array $order
     * @return Collection
     */
    public function find($order)
    {
        // TODO: Implement find() method.
    }

    /**
     * @param array $params
     * @return bool
     */
    public function verify(array $params)
    {
        // TODO: Implement verify() method.
    }
}
