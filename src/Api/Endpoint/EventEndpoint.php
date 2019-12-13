<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\ResourceCollection;

class EventEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|ResourceCollection|GenericEntity[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('events.json', $query);
        $response = $this->sendPaged($request, 'events');
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('events/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $eventId
     * @return GenericEntity
     */
    public function findOne($eventId)
    {
        $request = new GetJson('events/' . $eventId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('event'));
    }
}
