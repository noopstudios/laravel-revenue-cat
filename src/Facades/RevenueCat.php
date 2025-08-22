<?php

namespace NoopStudios\LaravelRevenueCat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getCustomer(string $appUserId, bool $withAttributes = true, array $queryParams = [])
 * @method static array createCustomer(string $appUserId, array $attributes = [])
 * @method static array updateCustomer(string $appUserId, array $attributes)
 * @method static array deleteCustomer(string $appUserId)
 * @method static array getCustomers(array $queryParams = [])
 * @method static array getCustomerAliases(string $appUserId, array $queryParams = [])
 * @method static array getCustomerAttributes(string $appUserId, array $queryParams = [])
 * @method static array getOfferings(bool $expand = true, array $queryParams = [])
 * @method static array getOffering(string $offeringId, bool $expand = true, array $queryParams = [])
 * @method static array getProduct(string $productId, bool $expand = true, array $queryParams = [])
 * @method static array getProducts(bool $expand = true, array $queryParams = [])
 * @method static array getEntitlements(bool $expand = true, array $queryParams = [])
 * @method static array getEntitlement(string $entitlementId, bool $expand = true, array $queryParams = [])
 * @method static array getProductsFromEntitlement(string $entitlementId, array $queryParams = [])
 * @method static array getCustomerActiveEntitlements(string $appUserId)
 * @method static array getCustomerPurchases(string $appUserId)
 * @method static array getUserSubscriptions(string $appUserId)
 * @method static ?array getCustomerActiveSubscription(string $appUserId)
 * @method static array getCustomerSubscriptions(string $appUserId)
 * @method static string getSubscriptionName(array $entitlement)
 * @method static \GuzzleHttp\Client setClient(\GuzzleHttp\Client $client)
 *
 * @see \NoopStudios\LaravelRevenueCat\RevenueCat
 */
class RevenueCat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NoopStudios\LaravelRevenueCat\RevenueCat::class;
    }
}
