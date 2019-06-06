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

class GetBuyNum extends AbstractPlugin
{
    /**
     * 方法名.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getBuyNum';
    }

    public function handle($fee)
    {
        if ($this->gateway instanceof FuluGateway) {
            $fee = (int) ceil($fee);
            $num = 0;

            /**
             * @var ProductInterface $product
             */
            foreach ($this->gateway->getProducts() as $product) {
                if ($fee >= $product->getPrice() && (int)bcmod($fee, $product->getPrice()) === 0) {
                    $num = bcdiv($fee, $product->getPrice(), 0);
                    break;
                }
            }

            return (int) $num;
        }
    }
}
