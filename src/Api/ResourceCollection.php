<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

use CodeCloud\Bundle\ShopifyBundle\Api\Response\ResponseInterface;

class ResourceCollection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    private $items = [];

    private $nextLinkParams;

    public function __construct(array $items, ?ResponseInterface $response = null)
    {
        $this->items = $items;

        if ($response !== null) {
            $links = $response->getHttpResponse()->getHeader('Link');

            foreach ($links as $link) {
                if (strpos($link, '; rel="next"') !== false) {
                    $link = preg_replace('/^.*<(.+)>.*$/', '$1', $link);
                    $queryString = parse_url($link, PHP_URL_QUERY);

                    parse_str($queryString, $params);

                    $this->nextLinkParams = $params;

                    break;
                }
            }
        }
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function count()
    {
        return count($this->items);
    }

    public function getNextLinkParams(): ?array
    {
        return $this->nextLinkParams;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
