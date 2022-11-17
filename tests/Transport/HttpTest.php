<?php

namespace tests\Transport;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use VerifyMyContent\Commons\Transport\HTTP;
use VerifyMyContent\Commons\Transport\InvalidStatusCodeException;

// @covers \VerifyMyContent\Commons\Transport\HTTP
class HttpTest extends TestCase
{
    public function testGet()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("GET"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => null,
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 200,
                "getBody" => $this->createConfiguredMock(\Psr\Http\Message\StreamInterface::class, [
                    "getContents" => "Hello World"
                ])
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $response = $http->get("/");
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello World", $response->getBody()->getContents());
    }

    public function testGetInvalidStatusCode()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("GET"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => null,
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 404,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 404");
        $this->expectExceptionCode(404);

        $http->get("/");
    }

    public function testGetInvalidStatusCodeArray(){
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("GET"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => null,
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 204,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 204");
        $this->expectExceptionCode(204);

        $http->get("/", [], [200]);
    }

    public function testPost()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("POST"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 200,
                "getBody" => $this->createConfiguredMock(\Psr\Http\Message\StreamInterface::class, [
                    "getContents" => "Hello World"
                ])
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $response = $http->post("/", ["foo" => "bar"]);
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello World", $response->getBody()->getContents());
    }

    public function testPostInvalidStatusCode()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("POST"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 400,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 400");
        $this->expectExceptionCode(400);

        $http->post("/", ["foo" => "bar"]);
    }

    public function testPostInvalidStatusCodeArray(){
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("POST"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 204,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 204");
        $this->expectExceptionCode(204);

        $http->post("/", ["foo" => "bar"], [], [201]);
    }

    public function testGetShouldThrowIfGuzzleThrowsIt(){
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("GET"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => null,
                ])
            )
            ->willThrowException(new \Exception("Testing exception", 123));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Testing exception");
        $this->expectExceptionCode(123);

        $http->get("/");
    }

    public function testPut()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("PUT"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 200,
                "getBody" => $this->createConfiguredMock(\Psr\Http\Message\StreamInterface::class, [
                    "getContents" => "Hello World"
                ])
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $response = $http->put("/", ["foo" => "bar"]);
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello World", $response->getBody()->getContents());
    }

    public function testPutInvalidStatusCode()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("PUT"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 400,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 400");
        $this->expectExceptionCode(400);

        $http->put("/", ["foo" => "bar"]);
    }

    public function testPutInvalidStatusCodeArray(){
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("PUT"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 204,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 204");
        $this->expectExceptionCode(204);

        $http->put("/", ["foo" => "bar"], [], [200]);
    }

    public function testPatch()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("PATCH"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 200,
                "getBody" => $this->createConfiguredMock(\Psr\Http\Message\StreamInterface::class, [
                    "getContents" => "Hello World"
                ])
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $response = $http->patch("/", ["foo" => "bar"]);
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello World", $response->getBody()->getContents());
    }

    public function testPatchInvalidStatusCode()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("PATCH"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 400,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 400");
        $this->expectExceptionCode(400);

        $http->patch("/", ["foo" => "bar"]);
    }

    public function testPatchInvalidStatusCodeArray(){
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("PATCH"),
                $this->equalTo("https://example.com/"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => ["foo" => "bar"],
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 204,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 204");
        $this->expectExceptionCode(204);

        $http->patch("/", ["foo" => "bar"], [], [200]);
    }

    public function testDelete()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("DELETE"),
                $this->equalTo("https://example.com/delete/ABC-123"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => null,
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 200,
                "getBody" => $this->createConfiguredMock(\Psr\Http\Message\StreamInterface::class, [
                    "getContents" => "Hello World"
                ])
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $response = $http->delete("/delete/ABC-123");
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello World", $response->getBody()->getContents());
    }

    public function testDeleteInvalidStatusCode()
    {
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("DELETE"),
                $this->equalTo("https://example.com/delete/ABC-123"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => null,
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 400,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 400");
        $this->expectExceptionCode(400);

        $http->delete("/delete/ABC-123");
    }

    public function testDeleteInvalidStatusCodeArray(){
        $mockGuzzleClient = $this->createMock(GuzzleClient::class);
        $mockGuzzleClient->expects($this->once())
            ->method("request")
            ->with(
                $this->equalTo("DELETE"),
                $this->equalTo("https://example.com/delete/ABC-123"),
                $this->equalTo([
                    "headers" => [
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                        "User-Agent" => "USER_AGENT",
                    ],
                    "http_errors" => false,
                    "json" => null,
                ])
            )
            ->willReturn($this->createConfiguredMock(\Psr\Http\Message\ResponseInterface::class, [
                "getStatusCode" => 204,
            ]));

        $http = new HTTP("https://example.com/", "USER_AGENT");
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = $mockGuzzleClient;

        $this->expectException(InvalidStatusCodeException::class);
        $this->expectExceptionMessage("Invalid status code: 204");
        $this->expectExceptionCode(204);

        $http->delete("/delete/ABC-123", [], [200]);
    }

    public function testShouldThrowAnErrorIfSettingInvalidUnsafeGuzzleClient()
    {
        $http = new HTTP("https://example.com/", "USER_AGENT");
        $this->expectException(\TypeError::class);
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = "foo";
    }

    public function testShouldThrowAnErrorIfSettingInvalidUnsafeGuzzleClient2()
    {
        $http = new HTTP("https://example.com/", "USER_AGENT");
        $this->expectException(\TypeError::class);
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = new \stdClass();
    }

    public function testShouldThrowAnErrorIfSettingInvalidUnsafeGuzzleClient3()
    {
        $http = new HTTP("https://example.com/", "USER_AGENT");
        $this->expectException(\TypeError::class);
        $http->{HTTP::__UNSAFE_GUZZLE_CLIENT} = [];
    }

    public function testShouldThrowAnErrorIfSetInvalidProperty()
    {
        $http = new HTTP("https://example.com/", "USER_AGENT");
        $this->expectException(\InvalidArgumentException::class);
        $http->foo = "bar";
    }

    public function testShouldUpdateBaseURL(){
        $http = new HTTP("https://example.com/", "USER_AGENT");
        $this->assertEquals("https://example.com", $http->getBaseURL());

        $this->assertEquals("https://other.com", $http->setBaseURL("https://other.com")->getBaseURL());
    }
}
