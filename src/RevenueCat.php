<?php

namespace NoopStudios\LaravelRevenueCat;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use NoopStudios\LaravelRevenueCat\Exceptions\RevenueCatException;

class RevenueCat
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.revenuecat.com';

    protected string $projectId;

    protected HttpClient $client;

    public function __construct(string $apiKey, string $projectId)
    {
        $this->apiKey = $apiKey;
        $this->projectId = $projectId;
        $this->client = $this->createDefaultClient();
    }

    public function setClient(HttpClient $client): self
    {
        $this->client = $client;

        return $this;
    }

    protected function createDefaultClient(): HttpClient
    {
        return new HttpClient([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Platform' => 'laravel',
            ],
        ]);
    }

    public function getCustomer(string $appUserId, bool $withAttributes = true, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/customers/{$appUserId}";
        $params = $queryParams;

        if ($withAttributes) {
            $params['expand'] = 'attributes';
        }

        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $this->get($uri);
    }

    public function createCustomer(string $appUserId, array $attributes = []): array
    {
        return $this->post("/v2/projects/{$this->projectId}/customers", array_merge(['app_user_id' => $appUserId], $attributes));
    }

    public function updateCustomer(string $appUserId, array $attributes): array
    {
        return $this->patch("/v2/projects/{$this->projectId}/customers/{$appUserId}", $attributes);
    }

    public function deleteCustomer(string $appUserId): array
    {
        return $this->delete("/v2/projects/{$this->projectId}/customers/{$appUserId}");
    }

    public function getCustomers(array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/customers";
        if (!empty($queryParams)) {
            $uri .= '?' . http_build_query($queryParams);
        }
        return $this->get($uri);
    }

    public function getCustomerAliases(string $appUserId, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/customers/{$appUserId}/aliases";
        if (!empty($queryParams)) {
            $uri .= '?' . http_build_query($queryParams);
        }
        return $this->get($uri);
    }

    public function getCustomerAttributes(string $appUserId, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/customers/{$appUserId}/attributes";
        if (!empty($queryParams)) {
            $uri .= '?' . http_build_query($queryParams);
        }
        return $this->get($uri);
    }

    public function getCustomerActiveEntitlements(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/active_entitlements");
    }

    public function getCustomerPurchases(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/purchases");
    }

    public function getCustomerSubscriptions(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/subscriptions");
    }


    public function getProduct(string $productId, bool $expand = true, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/products/{$productId}";
        $params = $queryParams;

        if ($expand) {
            $params['expand'] = 'app';
        }

        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $this->get($uri);
    }

    public function getProducts(bool $expand = true, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/products";
        $params = $queryParams;

        if ($expand) {
            $params['expand'] = 'items.app';
        }

        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $this->get($uri);
    }

    public function getOfferings(bool $expand = true, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/offerings";
        $params = $queryParams;

        if ($expand) {
            $params['expand'] = 'items.package';
        }

        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $this->get($uri);
    }

    public function getOffering(string $offeringId, bool $expand = true, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/offerings/{$offeringId}";
        $params = $queryParams;

        if ($expand) {
            $params['expand'] = 'package';
        }

        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $this->get($uri);
    }

    public function getEntitlements(bool $expand = true, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/entitlements";
        $params = $queryParams;

        if ($expand) {
            $params['expand'] = 'items.product';
        }

        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $this->get($uri);
    }

    public function getEntitlement(string $entitlementId, bool $expand = true, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/entitlements/{$entitlementId}";
        $params = $queryParams;

        if ($expand) {
            $params['expand'] = 'product';
        }

        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $this->get($uri);
    }

    public function getProductsFromEntitlement(string $entitlementId, array $queryParams = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/entitlements/{$entitlementId}/products";
        if (!empty($queryParams)) {
            $uri .= '?' . http_build_query($queryParams);
        }
        return $this->get($uri);
    }


    public function getUserSubscriptions(string $appUserId): array
    {
        // Use the dedicated active entitlements endpoint for better performance
        $activeEntitlements = $this->getCustomerActiveEntitlements($appUserId);

        return $activeEntitlements['items'] ?? [];
    }

    public function getCustomerActiveSubscription(string $appUserId): ?array
    {
        $subscriptions = $this->getCustomerSubscriptions($appUserId);

        foreach ($subscriptions['items'] ?? [] as $subscription) {
            if (($subscription['gives_access'] ?? false) === true) {
                return $subscription;
            }
        }

        return null;
    }

    /**
     * Get the subscription name from an entitlement or webhook event.
     *
     * @param  array  $data  Either an entitlement object or webhook event data
     */
    public function getSubscriptionName(array $data): string
    {
        // Handle webhook event data
        if (isset($data['entitlement_ids']) && is_array($data['entitlement_ids'])) {
            return $data['entitlement_ids'][0] ?? '';
        }

        // Handle entitlement object
        if (isset($data['identifier'])) {
            return $data['identifier'];
        }

        // Handle entitlement from customer response
        if (isset($data['entitlement_id'])) {
            return $data['entitlement_id'];
        }

        return '';
    }

    protected function get(string $uri): array
    {
        try {
            $response = $this->client->get($uri);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function post(string $uri, array $data = []): array
    {
        try {
            $response = $this->client->post($uri, ['json' => $data]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function patch(string $uri, array $data = []): array
    {
        try {
            $response = $this->client->patch($uri, ['json' => $data]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function delete(string $uri): array
    {
        try {
            $response = $this->client->delete($uri);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
