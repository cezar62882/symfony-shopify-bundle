<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;

class LocationEndpoint extends AbstractEndpoint
{
    /**
     * @return array|GenericEntity[]
     */
    public function findAll()
    {
        $request = new GetJson('locations.json');
        $response = $this->send($request);
        return $this->createCollection($response->get('locations'));
    }

    /**
     * @param int $locationId
     * @return GenericEntity
     */
    public function findOne($locationId)
    {
        $request = new GetJson('locations/' . $locationId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('location'));
    }
}
