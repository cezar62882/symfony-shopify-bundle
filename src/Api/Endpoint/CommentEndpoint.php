<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ResourceCollection;

class CommentEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|ResourceCollection|GenericResource[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('comments.json', $query);
        $response = $this->sendPaged($request, 'comments');
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('comments/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $commentId
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function findOne($commentId)
    {
        $request = new GetJson('comments/' . $commentId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('comment'));
    }

    /**
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $comment
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create(GenericResource $comment)
    {
        $request = new PostJson('comments.json', array('comment' => $comment->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('comment'));
    }

    /**
     * @param int $commentId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $comment
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($commentId, GenericResource $comment)
    {
        $request = new PutJson('comments/' . $commentId . '.json', array('comment' => $comment->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('comment'));
    }

    /**
     * @param int $commentId
     */
    public function markAsSpam($commentId)
    {
        $request = new PostJson('comments/' . $commentId . '/spam.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function markAsNotSpam($commentId)
    {
        $request = new PostJson('comments/' . $commentId . '/not_spam.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function approve($commentId)
    {
        $request = new PostJson('comments/' . $commentId . '/approve.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function remove($commentId)
    {
        $request = new PostJson('comments/' . $commentId . '/remove.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function restore($commentId)
    {
        $request = new PostJson('comments/' . $commentId . '/restore.json');
        $this->send($request);
    }
}
