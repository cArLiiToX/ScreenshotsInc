<?php
use Magento\Framework\App\Bootstrap;

include 'app/bootstrap.php';
class Login
{
    private $email;
    private $password;
    public $sessionId = 0;
    public $data;
    public function __construct()
    {
        $bootstrap = Bootstrap::create(BP, $_SERVER);
        $objectManager = $bootstrap->getObjectManager();
        $this->_accountManagement = $objectManager->get('Magento\Customer\Model\AccountManagement');
        $this->_customerFactory = $objectManager->get('Magento\Customer\Model\CustomerFactory');
        $this->_customerModel = $objectManager->create('Magento\Customer\Model\Customer');
        $this->_storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
    }
    public function setSignin($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
    public function setSignup($fName, $lName)
    {
        $this->firstName = $fName;
        $this->lastName = $lName;
    }
    //Return magento customer session
    public static function magentoSession()
    {
        $bootstrap = Bootstrap::create(BP, $_SERVER);
        $objectManager = $bootstrap->getObjectManager();
        $objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
        return $objectManager->get('Magento\Customer\Model\Session');
    }
    //user logout
    public function userLogout()
    {
        $session = self::magentoSession();
        $session->logout();
        $this->sessionId = 0;
    }
    //authenticate user login
    public function userLogin()
    {
        if (!empty($this->email) && !empty($this->password)) {
            $session = self::magentoSession();
            if (!$session->isLoggedIn()) {
                try {
                    $customer = $this->_accountManagement->authenticate($this->email, $this->password);
                    if ($customer->getId()) {
                        $session->setCustomerDataAsLoggedIn($customer);
                        $session->regenerateId();
                        $sessionId = $customer->getId();
                        $this->data = array('status' => '0', 'customerId' => $sessionId);
                    } else {
                        $this->data = array('status' => 'NA', 'customerId' => '0');
                    }
                    return $this->data;
                } catch (\Exception $e) {
                    return $this->data = array('status' => 'NA', 'customerId' => '0');
                }
            }
        } else {
            $session->addError('Login and password are required.');
        }
    }
    //user sign up
    public function userSignUp()
    {
        $websiteId = $this->_storeManager->getWebsite()->getWebsiteId();
        $this->_customerModel->setWebsiteId($websiteId);
        $this->_customerModel->loadByEmail($this->email);
        if (!$this->_customerModel->getId()) {
            $customer = $this->_customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->setEmail($this->email);
            $customer->setFirstname($this->firstName);
            $customer->setLastname($this->lastName);
            $customer->setPassword($this->password);
            $customer->setAddresses(null);
            $customer->save();
            self::userLogin();
        } else {
            $this->data = array('status' => 'AE', 'customerId' => '0');
        }
        return $this->data;
    }
}
$login = new Login();
if (isset($_POST['email']) && isset($_POST['password'])) {
    $login->setSignin($_POST['email'], $_POST['password']);
    if (isset($_POST['firstName']) && isset($_POST['lastName'])) {
        $login->setSignup($_POST['firstName'], $_POST['lastName']);
        $data = $login->userSignUp();
    } else {
        $data = $login->userLogin();
    }
} else {
    $session = Login::magentoSession();
    $login->sessionId = $session->getId();
    $sessionId = ($login->sessionId) ? $login->sessionId : 0;
    $data = array('status' => '0', 'customerId' => $sessionId);
}
header('Content-Type: application/json');
echo json_encode($data);
