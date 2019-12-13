<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ResourceCollection;

class CollectEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|ResourceCollection|GenericResource[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('collects.json', $query);
        $response = $this->sendPaged($request, 'collects');
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('collects/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $collectId
     * @return GenericResource
     */
    public function findOne($collectId)
    {
        $request = new GetJson('collects/' . $collectId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('collect'));
    }

    /**
     * @param GenericResource $collect
     * @return GenericResource
     */
    public function create(GenericResource $collect)
    {
        $request = new PostJson('collects.json', array('collect' => $collect->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('collect'));
    }

    /**
     * @param int $collectId
     */
    public function delete($collectId)
    {
        $request = new DeleteParams('collects/' . $collectId . '.json');
        $this->send($request);
    }
}
