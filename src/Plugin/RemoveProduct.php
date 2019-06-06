<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Plugin;

use JimChen\Recharge\Gateways\FuluGateway;

class RemoveProduct extends AbstractPlugin
{
    /**
     * 方法名.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'removeProduct';
    }

    public function handle($product)
    {
        if ($this->gateway instanceof FuluGateway) {
            $this->gateway->getProducts()->forget($product);
            $this->gateway->sort();
        }
    }
}
