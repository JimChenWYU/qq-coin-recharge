<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests\Fulu;

use JimChen\Recharge\Gateways\Fulu\OneProduct;
use JimChen\Recharge\Gateways\Fulu\ProductInterface;
use JimChen\Recharge\Gateways\FuluGateway;
use PHPUnit\Framework\TestCase;

class FuluGatewayHelperTest extends TestCase
{
    public function testHasProduct()
    {
        $gateway = new TestGateway();
        $this->assertTrue($gateway->getProducts()->has(OneProduct::class));
    }

    public function testAddProduct()
    {
        $gateway = new TestGateway();
        $gateway->addProduct(new TenProduct());

        $this->assertTrue($gateway->getProducts()->has(TenProduct::class));
        $this->assertEquals(2, $gateway->getProducts()->count());

        $gateway->addProduct(new TenProduct());
        $this->assertEquals(2, $gateway->getProducts()->count());
    }

    public function testGetBuyNumAndGetProductId()
    {
        $helper = new TestGateway();
        $this->assertEquals(3, $helper->getBuyNum(3));
        $this->assertEquals($helper->getProduct(OneProduct::class)->getId(), $helper->getProductId(3));

        $this->assertEquals(13, $helper->getBuyNum(13));
        $this->assertEquals($helper->getProduct(OneProduct::class)->getId(), $helper->getProductId(3));

        $this->assertEquals(127, $helper->getBuyNum(127));
        $this->assertEquals($helper->getProduct(OneProduct::class)->getId(), $helper->getProductId(3));

        $helper->addProduct(new TenProduct());
        $this->assertEquals(3, $helper->getBuyNum(3));
        $this->assertEquals($helper->getProduct(OneProduct::class)->getId(), $helper->getProductId(3));

        $this->assertEquals(13, $helper->getBuyNum(13));
        $this->assertEquals($helper->getProduct(OneProduct::class)->getId(), $helper->getProductId(13));

        $this->assertEquals(1, $helper->getBuyNum(10));
        $this->assertEquals($helper->getProduct(TenProduct::class)->getId(), $helper->getProductId(10));

        $this->assertEquals(2, $helper->getBuyNum(20));
        $this->assertEquals($helper->getProduct(TenProduct::class)->getId(), $helper->getProductId(20));

        $helper->addProduct(new ThirtyProduct());
        $this->assertEquals(1, $helper->getBuyNum(30));
        $this->assertEquals($helper->getProduct(ThirtyProduct::class)->getId(), $helper->getProductId(30));

        $this->assertEquals(2, $helper->getBuyNum(60));
        $this->assertEquals($helper->getProduct(ThirtyProduct::class)->getId(), $helper->getProductId(60));

        $this->assertEquals(66, $helper->getBuyNum(66));
        $this->assertEquals($helper->getProduct(OneProduct::class)->getId(), $helper->getProductId(66));
    }

    public function testRemoveProduct()
    {
        $gateway = new TestGateway();
        $gateway->removeProduct(OneProduct::class);
        $this->assertFalse($gateway->getProducts()->has(OneProduct::class));
        $this->assertEquals(0, $gateway->getProducts()->count());
    }
}

class TestGateway extends FuluGateway
{
    public function __construct(
        array $config = [
            'appkey'    => '1111',
            'appsecret' => '2222',
        ]
    ) {
        parent::__construct($config);
    }
}

class TenProduct implements ProductInterface
{
    /**
     * 商品价格
     *
     * @return int|string|float
     */
    public function getPrice()
    {
        return 10;
    }

    /**
     * 商品ID
     *
     * @return int
     */
    public function getId()
    {
        return 1234;
    }
}

class ThirtyProduct implements ProductInterface
{
    /**
     * 商品价格
     *
     * @return int|string|float
     */
    public function getPrice()
    {
        return 30;
    }

    /**
     * 商品ID
     *
     * @return int
     */
    public function getId()
    {
        return 5678;
    }
}
