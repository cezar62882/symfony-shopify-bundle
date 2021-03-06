<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\Exception\FailedRequestException;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\ResourceCollection;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use CodeCloud\Bundle\ShopifyBundle\Api\Response\ErrorResponse;
use CodeCloud\Bundle\ShopifyBundle\Api\Response\HtmlResponse;
use CodeCloud\Bundle\ShopifyBundle\Api\Response\JsonResponse;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Uri;

abstract class AbstractEndpoint
{
    const TOO_MANY_REQUEST_ERROR_CODE = 429;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param RequestInterface $request
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\Response\ResponseInterface
     * @throws FailedRequestException
     */
    protected function send(RequestInterface $request)
    {
        $response = $this->process($request);

        if (! $response->successful()) {
            throw new FailedRequestException('Failed request. ' . $response->getHttpResponse()->getReasonPhrase());
        }

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @param string $rootElement
     * @return ResponseCollection
     * @throws FailedRequestException
     */
    protected function sendPaged(RequestInterface $request, $rootElement)
    {
        return $this->processPaged($request, $rootElement);
    }

    /**
     * @param array $items
     * @param GenericResource|null $prototype
     * @return array
     */
    protected function createCollection($items, GenericResource $prototype = null)
    {
        if (! $prototype) {
            $prototype = new GenericResource();
        }

        foreach ($items as $index => $item) {
            $newItem = clone $prototype;
            $newItem->hydrate($item);
            $items[$index] = $newItem;
        }

        return $items;
    }

    /**
     * @param array $data
     * @return GenericResource
     */
    protected function createEntity($data)
    {
        $entity = new GenericResource();
        $entity->hydrate($data);

        return $entity;
    }

    /**
     * @param RequestInterface $request
     *
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\Response\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function process(RequestInterface $request)
    {
        try {
            $guzzleResponse = $this->client->send($request);
        } catch (ClientException $exception) {
            if ($exception->getCode() === self::TOO_MANY_REQUEST_ERROR_CODE) {
                if (strpos($exception->getResponse()->getBody()->getContents(), 'Daily variant creation limit reached') !== false) {
                    throw $exception;
                }

                // Sleep 1 sec
                sleep(1);

                return $this->process($request);
            } else {
                throw $exception;
            }
        }

        try {
            switch ($request->getHeaderLine('Content-type')) {
                case 'application/json':
                    $response = new JsonResponse($guzzleResponse);
                    break;
                default:
                    $response = new HtmlResponse($guzzleResponse);
            }
        } catch (ClientException $e) {
            $response = new ErrorResponse($guzzleResponse, $e);
        }

        return $response;
    }

    /**
     * Loop through a set of API results that are available in pages, returning the full resultset as one array
     * @param RequestInterface $request
     * @param string $rootElement
     * @param array $params
     * @return ResponseCollection
     */
    protected function processPaged(RequestInterface $request, $rootElement, array $params = array())
    {
        $requestUrl = $request->getUri();
        $parts = parse_url($requestUrl);

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);

            if (array_key_exists('limit', $query) || array_key_exists('page_info', $query)) {
                $response = $this->process($request->withUri(new Uri($requestUrl)));

                return new ResourceCollection($response->get($rootElement), $response);
            }
        }

        if (empty($params['limit'])) {
            $params['limit'] = 250;
        }

        $allResults = array();
        $paramDelim = strstr($requestUrl, '?') ? '&' : '?';

        do {
            $pagedRequest = $request->withUri(new Uri($requestUrl . $paramDelim . http_build_query($params)));
            $response = $this->process($pagedRequest);
            $resourceCollection = new ResourceCollection($response->get($rootElement), $response);
            $allResults = array_merge($allResults, $resourceCollection->getItems());
            $params = $resourceCollection->getNextLinkParams();
        } while ($params !== null);

        return new ResourceCollection($allResults);
    }
}
