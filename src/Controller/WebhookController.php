<?php

namespace CodeCloud\Bundle\ShopifyBundle\Controller;

use CodeCloud\Bundle\ShopifyBundle\Event\WebhookEvent;
use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;
use CodeCloud\Bundle\ShopifyBundle\Service\WebhookVerifier;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebhookController
{
    /**
     * @var ShopifyStoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $webhookVerifier;

    /**
     * @param ShopifyStoreManagerInterface $storeManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ShopifyStoreManagerInterface $storeManager,
        EventDispatcherInterface $eventDispatcher,
        WebhookVerifier $webhookVerifier
    ) {
        $this->storeManager = $storeManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->webhookVerifier = $webhookVerifier;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleWebhook(Request $request)
    {
        $topic     = $request->query->get('topic');
        $storeName = $request->query->get('store');

        if (!$topic || !$storeName) {
            throw new NotFoundHttpException();
        }

        if (!$this->storeManager->storeExists($storeName)) {
            throw new NotFoundHttpException();
        }

        $content = $request->getContent();
        $signature = $request->headers->get('X-Shopify-Hmac-SHA256', '');

        if (! $this->webhookVerifier->verify($content, $signature)) {
            throw new BadRequestHttpException('Invalid HMAC Signature');
        }

        $payload = \GuzzleHttp\json_decode($content, true);

        $this->eventDispatcher->dispatch(WebhookEvent::NAME, new WebhookEvent(
            $topic,
            $storeName,
            $payload
        ));

        return new Response('Shopify Webhook Received');
    }
}
