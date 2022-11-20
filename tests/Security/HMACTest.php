<?php

namespace VerifyMyContent\Commons\Security;

use PHPUnit\Framework\TestCase;

// @covers \VerifyMyContent\Commons\Security\HMAC
class HMACTest extends TestCase
{
    private const API_KEY = "api-key";
    private const API_SECRET = "api-secret";

    private function hmac_input($input)
    {
        return hash_hmac('sha256', $input, self::API_SECRET);
    }

    private function hmac_header($input)
    {
        return sprintf("%s:%s", self::API_KEY, $this->hmac_input($input));
    }

    public function testGenerate()
    {
        $hmac = new HMAC(self::API_KEY, self::API_SECRET);
        $this->assertEquals($this->hmac_header("input"), $hmac->generate("input"));
    }

    public function testValidate()
    {
        $hmac = new HMAC(self::API_KEY, self::API_SECRET);
        $this->assertTrue($hmac->validate($this->hmac_header("input"), "input"));
    }

    public function testValidateWithInvalidInput()
    {
        $hmac = new HMAC(self::API_KEY, self::API_SECRET);
        $this->assertFalse($hmac->validate($this->hmac_header("input"), "invalid"));
    }

    public function testValidateWithInvalidHash()
    {
        $hmac = new HMAC(self::API_KEY, self::API_SECRET);
        $this->assertFalse($hmac->validate("invalid", "input"));
    }

    public function testValidateWithInvalidHashAndInput()
    {
        $hmac = new HMAC(self::API_KEY, self::API_SECRET);
        $this->assertFalse($hmac->validate("invalid", "invalid"));
    }

    public function testValidateWithInvalidHashAndInputAndSecret()
    {
        $hmac = new HMAC("invalid", "invalid");
        $this->assertFalse($hmac->validate(
            $this->hmac_header("input"),
            "input"
        ));
    }

    public function testGenerateShouldGenerateJsonIfInputIsArray()
    {
        $hmac = new HMAC(self::API_KEY, self::API_SECRET);
        $this->assertEquals(
            $this->hmac_header(json_encode(["foo" => "bar"])),
            $hmac->generate(["foo" => "bar"])
        );
    }

    public function testGenerateShouldGenerateAsHeader()
    {
        $hmac = new HMAC(self::API_KEY, self::API_SECRET);
        $this->assertEquals(
            'hmac '. $this->hmac_header(json_encode(["foo" => "bar"])),
            $hmac->generate(["foo" => "bar"], true)
        );
    }
}
