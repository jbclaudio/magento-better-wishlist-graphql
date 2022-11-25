<?php

namespace Mageplaza\BetterWishlistGraphQl\Model\Api;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\WishlistFactory;
use Mageplaza\BetterWishlist\Api\BetterWishlistRepositoryInterface;
use Mageplaza\BetterWishlist\Helper\Data;
use Mageplaza\BetterWishlist\Model\Api\BetterWishlistRepository as mpBetterWishlistRepository;
use Mageplaza\BetterWishlist\Model\CategoryFactory;
use Mageplaza\BetterWishlist\Model\WishlistItemFactory;

class BetterWishlistRepository extends mpBetterWishlistRepository implements BetterWishlistRepositoryInterface
{
    public function __construct(
        WishlistFactory $wishlistModel,
        ProductRepositoryInterface $productRepository,
        WishlistItemFactory $mpWishlistItemFactory,
        CategoryFactory $mpWishlistCategoryFactory,
        WishlistHelper $wishlistHelper,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        DateTime $date,
        ItemFactory $itemModel,
        Data $_helperData
    ) {
        parent::__construct(
            $wishlistModel,
            $productRepository,
            $mpWishlistItemFactory,
            $mpWishlistCategoryFactory,
            $wishlistHelper,
            $eventManager,
            $storeManager,
            $date,
            $itemModel,
            $_helperData
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function editItemInCategory($productId, $categoryId, $customerId, $extraData = [])
    {
        /**
         * @var \Magento\Wishlist\Model\Wishlist $wishlist
         */
        $wishlist = $this->wishlistModel->create()->loadByCustomerId($customerId);
        if (!$wishlist) {
            throw new ApiException(__('Page not found.'), 101);
        }

        if (!$productId) {
            throw new ApiException(__('We can\'t specify a product.'), 101);
        }

        $wishlistItem = $this->_helperData->getItemByProductId($productId, $categoryId);

        if (!$wishlistItem || !$wishlistItem->getId()) {
            throw new ApiException(__('The catalog does not contain input product'), 101);
        }

        $wishlistItem = $this->itemModel->create()->load($wishlistItem->getWishlistItemId());

        foreach ($extraData as $key => $value) {
            $wishlistItem->setData($key, $value);
        }

        $wishlistItem->save();

        return true;
    }
}
