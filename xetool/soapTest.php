<?php
echo (extension_loaded('soap')) ? 'SOAP Enabled' : 'SOAP Disabled';
echo '<br />';
echo (class_exists('SOAPClient')) ? 'SOAP Client Present' : 'SOAP Client Absent';
echo '<br />';
echo (class_exists('SOAPServer')) ? 'SOAP Server Present' : 'SOAP Server Absent';
echo '<br />';

$apiUrl = 'http://your-site.com/';
try {
    $result = apiCall('Product', 'getProductInfo', $productInfo);
    $result = $result->result;
} catch (Exception $e) {
    echo 'Error2 : ' . $e->getMessage();
}

function apiCall($apiUrl, $model, $service, $param)
{
    require_once '../vendor/zendframework/zend-server/src/Client.php';
    require_once '../vendor/zendframework/zend-soap/src/Client.php';
    require_once '../vendor/zendframework/zend-soap/src/Client/Common.php';

    $url = 'html5designCedapi' . $model . 'V1';
    $wsdlUrl = $apiUrl . 'soap?wsdl&services=' . $url;
    $callUrl = $url . ucfirst($service);
    $opts = ['http' => ['header' => "Authorization: Bearer " . ACCESSTOKEN]];

    try {
        $context = stream_context_create($opts);
        $soapClient = new \Zend\Soap\Client($wsdlUrl);
        $soapClient->setSoapVersion(SOAP_1_2);
        $soapClient->setStreamContext($context);

        return $soapResponse = $soapClient->$callUrl($param);
    } catch (Exception $e) {
        echo 'Error1 : ' . $e->getMessage();
    }
}
