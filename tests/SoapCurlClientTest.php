<?php

use PHPUnit\Framework\TestCase;
use Src\SoapCurlClient;
use Src\SoapCurlClientException;

class SoapCurlClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        // Ahora pasamos los tres parámetros al constructor
        $endpoint = "http://example.com/soap";
        $namespaces = [
            "team" => "http://schemas.example.com/team"
        ];
        $curlOptions = [
            CURLOPT_TIMEOUT => 30, // Un ejemplo de opción de cURL
            CURLOPT_RETURNTRANSFER => true,
        ];

        $this->client = new SoapCurlClient($endpoint, $namespaces, $curlOptions);
    }

    public function testBuildSoapBody(): void
    {
        $method = "TestMethod";
        $params = ["param1" => "value1", "param2" => ["subparam" => "subvalue"]];
        $expectedXmlFragment = <<<XML
            <soapenv:Body>
                <team:TestMethod>
                    <param1>value1</param1>
                    <param2>
                        <subparam>subvalue</subparam>
                    </param2>
                </team:TestMethod>
            </soapenv:Body>
        XML;

        $soapBody = $this->invokePrivateMethod('buildSoapBody', [$method, $params]);
        $this->assertStringContainsString($expectedXmlFragment, $soapBody);
    }

    public function testFormatXml(): void
    {
        $rawXml = '<root><child>value</child></root>';
        $formattedXml = $this->client->formatXml($rawXml);

        $this->assertStringContainsString("\n", $formattedXml);
    }

    public function testCallHandlesCurlError(): void
    {
        $method = "InvalidMethod";
        $params = [];
        $soapAction = "http://example.com/InvalidAction";

        $result = $this->client->call($method, $params, $soapAction);
        $this->assertNull($result);
    }

    public function testThrowExceptionOnCurlError(): void
    {
        $this->expectException(SoapCurlClientException::class);
        $this->expectExceptionMessage("Curl error occurred");

        // Trigger the exception by simulating a cURL error
        $this->client->call("InvalidMethod", [], "http://example.com/InvalidAction");
    }

    public function testThrowExceptionOnInvalidSoapAction(): void
    {
        $this->expectException(SoapCurlClientException::class);
        $this->expectExceptionMessage("Invalid SOAP Action");

        // Trigger the exception by passing an invalid SOAP action
        $this->client->call("TestMethod", [], "http://example.com/InvalidAction");
    }

    /**
     * Invokes a private method from the class.
     *
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     */
    private function invokePrivateMethod(string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->client, $parameters);
    }
}
