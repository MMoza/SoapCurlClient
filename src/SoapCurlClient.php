<?php

/**
 * Class SoapCurlClient
 *
 * A cURL-based SOAP client to handle requests and responses, including 
 * XML-to-JSON conversion and namespace handling.
 *
 * @author Miguel Ángel Moza Barquill
 * @version 1.0
 * @date 2024-12-03
 */
class SoapCurlClient
{
    /**
     * @var string $endpoint The SOAP service endpoint URL.
     */
    private string $endpoint;

    /**
     * @var array $namespaces An associative array of namespaces with prefixes as keys and URIs as values.
     */
    private array $namespaces;

    /**
     * @var array $curlOptions Additional cURL options for customization.
     */
    private array $curlOptions;

    /**
     * Constructor to initialize the SOAP client.
     *
     * @param string $endpoint The SOAP service endpoint URL.
     * @param array $namespaces An associative array of namespaces with prefixes as keys and URIs as values.
     * @param array $curlOptions Additional cURL options for customization.
     */
    public function __construct(string $endpoint, array $namespaces, array $curlOptions = [])
    {
        $this->endpoint = $endpoint;
        $this->namespaces = $namespaces;

        $env = getenv('APP_ENV') ?: 'dev';
        $configFile = __DIR__ . '/../config/curl_' . $env . '.php';

        $defaultCurlOptions = include $configFile;
        $this->curlOptions = array_merge($defaultCurlOptions, $curlOptions);
    }

/**
 * Makes a SOAP request using cURL.
 *
 * @param string $method The SOAP method to call.
 * @param array $params An associative array of parameters for the SOAP request.
 * @param string $soapAction The SOAP action to include in the headers.
 * @return array|null The response from the SOAP request as an array, or null if an error occurs.
 * @throws SoapCurlClientException if there is an error with the request.
 */
public function call(string $method, array $params, string $soapAction): ?array
{
    $soapBody = $this->buildSoapBody($method, $params);

    $curl = curl_init();
    $defaultCurlOptions = [
        CURLOPT_URL => $this->endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $soapBody,
        CURLOPT_HTTPHEADER => [
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: ' . $soapAction
        ],
        CURLOPT_TIMEOUT => 30
    ];

    $curlOptions = array_merge($defaultCurlOptions, $this->curlOptions);

    curl_setopt_array($curl, $curlOptions);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error = 'cURL Error: ' . curl_error($curl);
        $this->logRequestResponse($soapBody, null, $error, 'ERROR');
        curl_close($curl);
        throw new SoapCurlClientException($error);
    }

    curl_close($curl);

    // Ensure response is valid before returning
    $jsonResponse = $this->convertXmlToJson($response);
    if ($jsonResponse === false) {
        $this->logRequestResponse($soapBody, $response, 'ERROR', '500');
        throw new SoapCurlClientException("Failed to convert XML to JSON.");
    }

    $this->logRequestResponse($soapBody, $response, 'SUCCESS', '200');

    return $jsonResponse;
}


    /**
     * Logs the request and response in XML and JSON format with status.
     *
     * @param string $requestXml The SOAP request in XML format.
     * @param string|null $responseXml The SOAP response in XML format, or null if no response.
     * @param string $status The status of the request (SUCCESS or ERROR).
     * @param string $statusCode The HTTP status code or error message.
     */
    private function logRequestResponse(string $requestXml, ?string $responseXml, string $status, string $statusCode): void
    {
        $logDirectory = __DIR__ . '/logs';
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        $timestamp = date('Ymd_His');
        file_put_contents("$logDirectory/request_$timestamp.xml", $requestXml);

        if ($responseXml) {
            $formattedResponseXml = $this->formatXml($responseXml);
            file_put_contents("$logDirectory/response_$timestamp.xml", $formattedResponseXml);
            $jsonResponse = json_encode($this->convertXmlToJson($responseXml), JSON_PRETTY_PRINT);
            file_put_contents("$logDirectory/response_$timestamp.json", $jsonResponse);
        }

        $logEntry = [
            'timestamp'     => $timestamp,
            'status'        => $status,
            'statusCode'    => $statusCode,
            'requestFile'   => "request_$timestamp.xml",
            'responseFile'  => $responseXml ? "response_$timestamp.xml" : null
        ];
        file_put_contents("$logDirectory/log_$timestamp.txt", print_r($logEntry, true));
    }

    /**
     * Formats XML for pretty printing.
     *
     * @param string $xmlString The raw XML string.
     * @return string The formatted XML string.
     */
    public function formatXml(string $xmlString): string
    {
        $dom = new DOMDocument('1.0', 'ISO-8859-1');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlString);
        return $dom->saveXML();
    }

    /**
     * Converts a SOAP XML response to a clean JSON format by removing namespaces.
     *
     * @param string $xml The raw SOAP XML response.
     * @return array An associative array representation of the JSON response.
     */
    private function convertXmlToJson(string $xml): array
    {
        $cleanXml = preg_replace('/xmlns[^=]*="[^"]*"/', '', $xml);
        $responseXml = simplexml_load_string($cleanXml);
        $jsonResponse = json_encode($responseXml);
        $arrayResponse = json_decode($jsonResponse, true);

        return $this->cleanNamespaces($arrayResponse);
    }

    /**
     * Recursively removes namespaces from array keys.
     *
     * @param array $array The array with namespaced keys.
     * @return array The array with clean keys.
     */
    private function cleanNamespaces(array $array): array
    {
        $cleanedArray = [];
        foreach ($array as $key => $value) {
            $cleanedKey = preg_replace('/^[a-zA-Z0-9]+:/', '', $key);
            $cleanedArray[$cleanedKey] = is_array($value) ? $this->cleanNamespaces($value) : $value;
        }
        return $cleanedArray;
    }

    /**
     * Builds the SOAP XML body for the request.
     *
     * @param string $method The SOAP method to call.
     * @param array $params An associative array of parameters.
     * @return string The SOAP XML body as a string.
     */
    private function buildSoapBody(string $method, array $params): string
    {
        $namespaces = '';
        foreach ($this->namespaces as $prefix => $uri) {
            $namespaces .= " xmlns:$prefix=\"$uri\"";
        }

        $paramsXml = $this->buildParamsXml($params);

        return <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"{$namespaces}>
            <soapenv:Header/>
            <soapenv:Body>
                <team:{$method}>
                    {$paramsXml}
                </team:{$method}>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;
    }

    /**
     * Recursively builds XML from an associative array of parameters.
     *
     * @param array $params The parameters to include in the SOAP request.
     * @return string The XML representation of the parameters.
     */
    private function buildParamsXml(array $params): string
    {
        $xml = '';
        foreach ($params as $key => $value) {
            $xml .= is_array($value)
                ? "<{$key}>" . $this->buildParamsXml($value) . "</{$key}>"
                : "<{$key}>{$value}</{$key}>";
        }
        return $xml;
    }
}