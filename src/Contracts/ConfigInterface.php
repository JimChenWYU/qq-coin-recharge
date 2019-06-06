<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Contracts;

use JimChen\Recharge\Support\Config;

interface ConfigInterface
{
    /**
     * 获取配置.
     *
     * @return Config
     */
    public function getConfig();

    /**
     * 设置配置.
     *
     * @param Config $config
     *
     * @return ConfigInterface
     */
    public function setConfig(Config $config);
}
