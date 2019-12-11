<?php

namespace CodeCloud\Bundle\ShopifyBundle\Service;

class WebhookVerifier
{
    private $sharedSecret;

    public function __construct(string $sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;
    }

    public function verify(string $data, string $signature): bool
    {
        $expectedSignature = base64_encode(hash_hmac('sha256', $data, $this->sharedSecret, true));

        return hash_equals($signature, $expectedSignature);
    }
}
