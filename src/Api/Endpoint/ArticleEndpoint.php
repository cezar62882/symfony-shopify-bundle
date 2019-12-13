<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ResourceCollection;

class ArticleEndpoint extends AbstractEndpoint
{
    /**
     * @param int $blogId
     * @param array $query
     * @return array|ResourceCollection|GenericResource[]
     */
    public function findByBlog($blogId, array $query = array())
    {
        $request = new GetJson('blogs/' . $blogId . '/articles.json', $query);
        $response = $this->sendPaged($request, 'articles');
        return $this->createCollection($response);
    }

    /**
     * @param int $blogId
     * @param array $query
     * @return int
     */
    public function countByBlog($blogId, array $query = array())
    {
        $request = new GetJson('blogs/' . $blogId . '/articles/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $blogId
     * @param int $articleId
     * @return GenericResource
     */
    public function findOne($blogId, $articleId)
    {
        $request = new GetJson('blogs/' . $blogId . '/articles/' . $articleId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('article'));
    }

    /**
     * @param int $blogId
     * @param GenericResource $article
     * @return GenericResource
     */
    public function create($blogId, GenericResource $article)
    {
        $request = new PostJson('blogs/' . $blogId . '/articles.json', array('article' => $article->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('article'));
    }

    /**
     * @param int $blogId
     * @param int $articleId
     * @param GenericResource $article
     * @return GenericResource
     */
    public function update($blogId, $articleId, GenericResource $article)
    {
        $request = new PutJson('blogs/' . $blogId . '/articles/' . $articleId . '.json', array('article' => $article->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('article'));
    }

    /**
     * @param int $blogId
     * @param int $articleId
     */
    public function delete($blogId, $articleId)
    {
        $request = new DeleteParams('blogs/' . $blogId . '/articles/' . $articleId . '.json');
        $this->send($request);
    }

    /**
     * @return array
     */
    public function findAllAuthors()
    {
        $request = new GetJson('articles/authors.json');
        $response = $this->send($request);
        return $response->get('authors');
    }

    /**
     * @param array $query
     * @return array
     */
    public function findAllTags(array $query = array())
    {
        $request = new GetJson('articles/tags.json', $query);
        $response = $this->send($request);
        return $response->get('tags');
    }
}
