<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Plugin;

use JimChen\Recharge\Gateways\Fulu\ProductInterface;
use JimChen\Recharge\Gateways\FuluGateway;

class AddProduct extends AbstractPlugin
{
    /**
     * 方法名.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'addProduct';
    }

    /**
     * 处理方法
     *
     * @param ProductInterface $product
     */
    public function handle(ProductInterface $product)
    {
        if ($this->gateway instanceof FuluGateway) {
            if (!$this->gateway->getProducts()->has($key = get_class($product))) {
                $this->gateway->getProducts()->add($key, $product);
                $this->gateway->sort();
            }
        }
    }
}
