<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ResourceCollection;

class RedirectEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|ResourceCollection|GenericResource[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('redirects.json', $query);
        $response = $this->sendPaged($request, 'redirects');
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('redirects/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $redirectId
     * @param array $fields
     * @return GenericResource
     */
    public function findOne($redirectId, array $fields = array())
    {
        $params = $fields ? array('fields' => implode(',', $fields)) : array();
        $request = new GetJson('redirects/' . $redirectId . '.json', $params);
        $response = $this->send($request);
        return $this->createEntity($response->get('redirect'));
    }

    /**
     * @param GenericResource $redirect
     * @return GenericResource
     */
    public function create(GenericResource $redirect)
    {
        $request = new PostJson('redirects.json', array('redirect' => $redirect->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('redirect'));
    }

    /**
     * @param int $redirectId
     * @param GenericResource $redirect
     * @return GenericResource
     */
    public function update($redirectId, GenericResource $redirect)
    {
        $request = new PutJson('redirects/' . $redirectId . '.json', array('redirect' => $redirect->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('redirect'));
    }

    /**
     * @param int $redirectId
     */
    public function delete($redirectId)
    {
        $request = new DeleteParams('redirects/' . $redirectId . '.json');
        $this->send($request);
    }
}
