<?php

namespace App\Services;

interface MerchandiseService
{
    public function dashboard();
    public function getCategories();
    public function postCategory(array $data);
    public function putCategory(array $data);
    public function deleteCategory($id);
    public function getProducts();
    public function showProduct($id);
    public function postProduct(array $data);
    public function putProduct(array $data);
    public function deleteProduct($id);
    public function getInventory();
    public function getSellingPrices();
    public function getSellingPriceHistory($variantId);
    public function updateSellingPrice(array $data, $userId);
    public function getStoreSettings();
    public function updateStoreSettings(array $data);
    public function getEmailTemplates();
    public function updateEmailTemplate(string $key, array $data);
    public function getCatalogProducts();
    public function updateCatalogProduct(array $data);
    public function getPromotions();
    public function getPromotionFormData();
    public function showPromotion($id);
    public function postPromotion(array $data);
    public function putPromotion(array $data);
    public function deletePromotion($id);
    public function getOrders(array $filters = []);
    public function showOrder($id);
    public function getPublicStore($cartKey, array $filters = []);
    public function getPublicCategories();
    public function getPublicProduct($productId, $cartKey);
    public function getCart($cartKey);
    public function getCheckout(
        $cartKey,
        ?string $promotionCode = null,
        ?int $giftVariantId = null,
        ?int $promotionId = null
    );
    public function previewPromotion($cartKey, string $code);
    public function previewPromotionGift($cartKey, ?string $code, int $giftVariantId);
    public function previewPromotionSelection($cartKey, ?string $code, int $promotionId, ?int $giftVariantId = null);
    public function addToCart($cartKey, $variantId, $quantity);
    public function updateCart($cartKey, array $items);
    public function removeCartItem($cartKey, $itemId);
    public function createOrderAndInvoice($cartKey, array $data);
    public function processOrderPaymentCallback(string $externalId, array $callbackData);
    public function getStockIns($limit = null);
    public function getStockInVariants();
    public function postStockIn(array $data, $userId);
    public function getStockOpnames();
    public function getStockOpnameVariants();
    public function showStockOpname($id);
    public function postStockOpname(array $data, $userId);
    public function getReports(array $filters = []);
}
