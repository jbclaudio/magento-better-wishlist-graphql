<?php

namespace Mageplaza\BetterWishlistGraphQl\Plugin\Model\Api;

use Mageplaza\BetterWishlist\Model\Api\BetterWishlistRepository as mpBetterWishlistRepository;
use Magento\Wishlist\Model\Item;

class BetterWishlistRepository
{
    public function afterAddExtraWishlistData(
        mpBetterWishlistRepository $subject,
        ?bool $result,
        Item $wishlistItem,
        array $extraData
    ): Item {
        foreach ($extraData as $key => $value) {
            $wishlistItem->setData($key, $value);
        }

        return $wishlistItem;
    }
}
