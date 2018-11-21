<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;

class RefundEndpoint extends AbstractEndpoint
{
    /**
     * @param int $orderId
     * @param int $refundId
     * @param array $fields
     * @return GenericEntity
     */
    public function findOne($orderId, $refundId, array $fields = array())
    {
        $params = $fields ? array('fields' => implode(',', $fields)) : array();
        $request = new GetJson('/admin/orders/' . $orderId . '/refunds/' . $refundId . '.json', $params);
        $response = $this->send($request);
        return $this->createEntity($response->get('refund'));
    }

    /**
     * @param int $orderId
     * @param array $refund
     * @return GenericResource
     */
    public function create($orderId, GenericResource $refund)
    {
        $request = new PostJson("/admin/orders/$orderId/refunds.json", array('refund' => $refund->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('refund'));
    }
}
