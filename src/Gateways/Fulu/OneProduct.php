<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Gateways\Fulu;

/**
 * 福禄的1元Q币充值商品
 */
class OneProduct implements ProductInterface
{
    public function getPrice()
    {
        return 1;
    }

    public function getId()
    {
        return 10013922;
    }
}
