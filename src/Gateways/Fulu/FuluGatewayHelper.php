<?php

/*
 * This file is part of the jimchen/gmqcoin.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace JimChen\Recharge\Gateways\Fulu;

use JimChen\Recharge\Plugin\AddProduct;
use JimChen\Recharge\Plugin\GetBuyNum;
use JimChen\Recharge\Plugin\GetProductId;
use JimChen\Recharge\Plugin\RemoveProduct;
use JimChen\Recharge\Support\Collection;
use JimChen\Recharge\Traits\PluggableTrait;

/**
 * Trait FuluGatewayHelper
 *
 * @method void addProduct(ProductInterface $product)
 * @method void removeProduct(string $product)
 * @method int getBuyNum(int $fee)
 * @method int getProductId(int $fee)
 */
trait FuluGatewayHelper
{
    use PluggableTrait;

    /**
     * @var Collection
     */
    protected $products;

    /**
     * @return Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param string $key
     * @return ProductInterface
     */
    public function getProduct($key)
    {
        return $this->products->get($key);
    }

    /**
     * 排序
     */
    public function sort()
    {
        $this->products = $this->products->sort(function (ProductInterface $a, ProductInterface $b) {
            // 相等不变
            if ($a->getPrice() == $b->getPrice()) {
                return 0;
            }
            // 倒序
            return ($a->getPrice() < $b->getPrice()) ? 1 : -1;
        });
    }

    /**
     * 预初始化.
     */
    protected function preInit()
    {
        $this->addPlugin(new AddProduct());
        $this->addPlugin(new RemoveProduct());
        $this->addPlugin(new GetBuyNum());
        $this->addPlugin(new GetProductId());

        if (!$this->products instanceof Collection) {
            $this->products = new Collection();
        }
        if (!$this->products->has(OneProduct::class)) {
            $this->addProduct(
                new OneProduct()
            );
        }
    }
}
