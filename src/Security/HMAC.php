<?php

namespace VerifyMyContent\Commons\Security;

class HMAC {

    private $apiKey;

    private $apiSecret;

    function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Generate the HMAC signature based on input and API keys
     */
    public function generate($input): string
    {
        if (is_array($input)) {
            $input = json_encode($input);
        }

        $hash = hash_hmac('sha256', $input, $this->apiSecret);
        return "{$this->apiKey}:{$hash}";
    }

    /**
     * Validates that a generated HMAC
     * @param $hash
     * @param $input
     * @return bool
     */
    public function validate($hash, $input): bool
    {
        return $this->generate($input) === $this->removePrefix($hash);
    }

    private function removePrefix($header)
    {
        return preg_replace('/^hmac ?/i', '', $header);
    }
}
