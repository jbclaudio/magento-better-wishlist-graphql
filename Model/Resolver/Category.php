<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterWishlistGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\BetterWishlistGraphQl\Model\Resolver;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Mageplaza\BetterWishlist\Model\CategoryFactory as mpWishlistCategoryFactory;
use Mageplaza\BetterWishlistGraphQl\Model\Api\BetterWishlistRepository;

/**
 * Class Category
 * @package Mageplaza\BetterWishlistGraphQl\Model\Resolver
 */
abstract class Category implements ResolverInterface
{
    public function __construct(
        protected BetterWishlistRepository $wishlistRepository,
        protected mpWishlistCategoryFactory $mpWishlistCategoryFactory,
        protected GetCustomer $getCustomer,
        protected ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @param $context
     *
     * @return int|null
     * @throws GraphQlInputException
     * @throws Exception
     */
    public function checkLogin($context)
    {
        if ($context->getExtensionAttributes()->getIsCustomer() === false) {
            throw new GraphQlInputException(__('Please login to access the feature!'));
        }
        $customer = $this->getCustomer->execute($context);

        return $customer->getId();
    }

    /**
     * @param $args
     *
     * @return \Mageplaza\BetterWishlist\Model\Category
     */
    public function createCategoryInput($args)
    {
        $categoryData = isset($args['input']) ? $args['input'] : [];

        return $this->mpWishlistCategoryFactory->create()->setData($categoryData);
    }

    /**
     * @param array $args
     * @param string $key
     *
     * @return string
     */
    public function checkItemInput($args, $key)
    {
        return isset($args['input'][$key]) ? $args['input'][$key] : '';
    }

    public function getProductBySku(string $sku): ProductInterface
    {
        return $this->productRepository->get($sku);
    }

    public function getProductById(int $id): ProductInterface
    {
        return $this->productRepository->getById($id);
    }
}
