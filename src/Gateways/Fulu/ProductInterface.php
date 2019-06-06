<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Gateways\Fulu;

interface ProductInterface
{
    /**
     * 商品价格
     *
     * @return int|string|float
     */
    public function getPrice();

    /**
     * 商品ID
     *
     * @return int
     */
    public function getId();
}
