<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Contracts;

interface GatewayInterface
{
    public function recharge(array $config_biz);

    public function find($order);
}
