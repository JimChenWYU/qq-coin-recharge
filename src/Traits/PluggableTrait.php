<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Traits;

use BadMethodCallException;
use JimChen\Recharge\Contracts\GatewayInterface;
use JimChen\Recharge\Exceptions\PluginNotFoundException;
use JimChen\Recharge\Plugin\PluginInterface;
use LogicException;

trait PluggableTrait
{
    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * Register a plugin.
     *
     * @param PluginInterface $plugin
     *
     * @throws LogicException
     *
     * @return $this
     */
    public function addPlugin(PluginInterface $plugin)
    {
        if (!method_exists($plugin, 'handle')) {
            throw new LogicException(get_class($plugin) . ' does not have a handle method.');
        }
        $this->plugins[$plugin->getMethod()] = $plugin;

        return $this;
    }

    /**
     * Find a specific plugin.
     *
     * @param string $method
     *
     * @throws PluginNotFoundException
     *
     * @return PluginInterface
     */
    protected function findPlugin($method)
    {
        if (!isset($this->plugins[$method])) {
            throw new PluginNotFoundException('Plugin not found for method: ' . $method);
        }

        return $this->plugins[$method];
    }

    /**
     * Invoke a plugin by method name.
     *
     * @param string           $method
     * @param array            $arguments
     * @param GatewayInterface $gateway
     *
     * @throws PluginNotFoundException
     *
     * @return mixed
     */
    protected function invokePlugin($method, array $arguments, GatewayInterface $gateway)
    {
        $plugin = $this->findPlugin($method);
        $plugin->setGateway($gateway);
        $callback = [$plugin, 'handle'];

        return call_user_func_array($callback, $arguments);
    }

    /**
     * Plugins pass-through.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        try {
            return $this->invokePlugin($method, $arguments, $this);
        } catch (PluginNotFoundException $e) {
            throw new BadMethodCallException(
                'Call to undefined method '
                . get_class($this)
                . '::' . $method
            );
        }
    }
}
