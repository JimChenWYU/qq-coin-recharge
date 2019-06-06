<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Plugin;

use JimChen\Recharge\Contracts\GatewayInterface;

interface PluginInterface
{
    /**
     * 方法名
     *
     * @return string
     */
    public function getMethod();

    /**
     * 设置充值网关
     *
     * @param GatewayInterface $gateway
     * @return void
     */
    public function setGateway(GatewayInterface $gateway);
}
