<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Contracts;

use JimChen\Recharge\Support\Collection;

interface GatewayInterface
{
    /**
     * @param array $config_biz
     * @return Collection
     */
    public function recharge(array $config_biz);

    /**
     * @param string|array $order
     * @return Collection
     */
    public function find($order);

    /**
     * @param array $params
     * @return bool
     */
    public function verify(array $params);
}
