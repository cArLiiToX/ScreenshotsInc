<?php
include_once 'function.php';
require_once 'xeconfig.php';
$redirect_path = XEPATH . 'xetool/index.php';
use Magento\Framework\App\Bootstrap;

$accessToken = '';
$msg = '';
if (!empty($_POST) && isset($_POST['type'])) {
    extract($_POST);

    include '../app/bootstrap.php';
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();

    //$state = $objectManager->get('Magento\Framework\App\State');
    //$state->setAreaCode('frontend');

    if ($type == 'n') {
        $integrationExists = $objectManager->get('Magento\Integration\Model\IntegrationFactory')->create()->load($name, 'name')->getData();
        if (empty($integrationExists)) {
            $integrationData = array(
                'name' => $name,
                'email' => $email,
                'status' => '1',
                'endpoint' => XEPATH,
                'setup_type' => '0',
            );
            try {
                // Code to create Integration
                $integrationFactory = $objectManager->get('Magento\Integration\Model\IntegrationFactory')->create();
                $integration = $integrationFactory->setData($integrationData);
                $integration->save();
                $integrationId = $integration->getId();
                $consumerName = 'Integration' . $integrationId;

                // Code to create consumer
                $oauthService = $objectManager->get('Magento\Integration\Model\OauthService');
                $consumer = $oauthService->createConsumer(['name' => $consumerName]);
                $consumerId = $consumer->getId();
                $integration->setConsumerId($consumer->getId());
                $integration->save();

                // Code to grant permission
                $authrizeService = $objectManager->get('Magento\Integration\Model\AuthorizationService');
                $authrizeService->grantAllPermissions($integrationId);

                // Code to authorize
                $token = $objectManager->get('Magento\Integration\Model\Oauth\Token');
                $uri = $token->createVerifierToken($consumerId);
                $token->setType('access');
                $token->save();
                $accessToken = $token->getToken();
            } catch (Exception $e) {
                $msg = $e->getMessage();
                xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 4th Step : ' . $conn->error . ' : ' . $msg . "\n");
                header('Location: ' . $redirect_path . '?action=soap&msg=' . $msg);die();
            }
        } else {
            //if($integrationExists['name'] == $name && $integrationExists['email'] == $email)
            $msg = 'Duplicate Integration is not allowed.';
            xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 4th Step : ' . $conn->error . ' : ' . $msg . "\n");
            header('Location: ' . $redirect_path . '?action=soap&msg=' . $msg);die();
        }
    } else if ($type == 'e') {
        $consumerId = $objectManager->get('Magento\Integration\Model\IntegrationFactory')->create()->load($integration_id)->getConsumerId();
        //$token = $objectManager->get('Magento\Integration\Model\Oauth\Token')->getData();

        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION');
        $tableName = $connection->getTableName('oauth_token');
        $token = $connection->fetchAll("SELECT token FROM " . $tableName . " WHERE consumer_id='" . $consumerId . "' LIMIT 1;");
        $accessToken = $token[0]['token'];
    }
} else {
    $msg = 'Fill up the form with correct information';
    header('Location: ' . $redirect_path . '?action=soap&msg=' . $msg);die();
}

if ($accessToken) {
    $sourceF = 'xeconfig.xml';
    $dom = new DomDocument();
    $dom->load($sourceF);
    $dom->getElementsByTagName('accessToken')->item(0)->nodeValue = $accessToken;
    $dom->save($sourceF);

    header('Location: ' . $redirect_path . '?action=setdb');
} else {
    $msg = 'Access token is unable to generated.';
    xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 4th Step : ' . $msg . "\n");
    header('Location: ' . $redirect_path . '?action=soap&msg=' . $msg);die();
}
