<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;

class InventoryLevelEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|GenericEntity[]
     * @throws \CodeCloud\Bundle\ShopifyBundle\Api\Request\Exception\FailedRequestException
     */
    public function findAll(array $query)
    {
        $request = new GetJson('/admin/inventory_levels.json', $query);
        $response = $this->send($request);

        return $this->createCollection($response->get('inventory_levels'));
    }

    public function adjust(GenericResource $adjust)
    {
        $request = new PostJson('/admin/inventory_levels/adjust.json', $adjust->toArray());
        $response = $this->send($request);

        return $this->createCollection($response->get('inventory_level'));
    }

    public function set(GenericResource $resource)
    {
        $request = new PostJson('/admin/inventory_levels/set.json', $resource->toArray());
        $response = $this->send($request);

        return $this->createCollection($response->get('inventory_level'));
    }
}