<?php
class Helper
{

    public function validateServices($service_type)
    {

        $services = array('getCategories', 'getProductCount', 'getProducts', 'getProductById', 'addToCart');

        if (in_array($service_type, $services)) {
            return true;
        }

        return false;
    }
    public function validateProductRequest($data)
    {

        $error = false;

        if (isset($data->start) && !is_numeric($data->start)) {

            $error = "Invalid type for input field 'start' (integer required)";

        } elseif (isset($data->range) && !is_numeric($data->range)) {

            $error = "Invalid type for input field 'range' (integer required)";

        } elseif (isset($data->range) && empty($data->range)) {

            $error = " input field 'range' cant be null";

        } elseif (isset($data->categoryid) && !is_numeric($data->categoryid)) {

            $error = "Invalid type for input field 'categoryid' (integer required)";

        }

        return $error;
    }
    public function validateProductById($data)
    {
        $error = false;
        if (!isset($data->pid) || !is_numeric($data->pid)) {

            $error = "Invalid pid (" . $data->pid . ") integer required.";
        }

        return $error;
    }
    public function validateAddToCart($data)
    {

        $this->db = new Datalayer();

        $error = false;
        if (!isset($data->productdata->pid) || !isset($data->productdata->qty) || !isset($data->productdata->options)) {

            $error = 'Invalid input parameters for addToCart service';

        } elseif (!$this->db->isProductExists($data->productdata->pid)) {

            $error = "Product id : " . $data->productdata->pid . " not exists.";

        } elseif (!isset($data->productdata->session_id)) {

            $error = 'No session id has been given in input';

        }

        return $error;

    }
    public function addToCart($cartData)
    {
        if (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true) {
            $opencartPath = HTTPS_SERVER;
        } else {
            $opencartPath = HTTP_SERVER;
        }

        $url = $opencartPath . 'vcheck.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $version = curl_exec($ch);
        curl_close($ch);
        if ($version >= '2.0.0.0' && $version < '2.1.0.1') {
            $cartData = (array) $cartData;
            foreach ($cartData as $cartArray) {
                $data = (object) $cartArray;
                session_id($data->session_id);
                session_start();
                $product['product_id'] = (int) $data->id;
                if ($data->options) {
                    $product['option'] = $data->options;
                }
                $key = base64_encode(serialize($product));
                if ((int) $data->qty && ((int) $data->qty > 0)) {
                    if (!isset($session->data['cart'][$key])) {
                        $_SESSION['cart'][$key] = (int) $data->qty;
                    } else {
                        $_SESSION['cart'][$key] += (int) $data->qty;
                    }
                }
                if (isset($data->extra_price)) {
                    $_SESSION['cart-design'][$key]['extra-price'] = $data->extra_price;
                }
                if (isset($data->refid)) {
                    $_SESSION['cart-design'][$key]['refid'] = $data->refid;
                }
            }
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                return true;
            }
        } elseif ($version >= '2.1.0.1' && $version < '2.3.0.0') {
            $cartData = (array) $cartData;
            $status = false;
            foreach ($cartData as $cartArray) {
                $data = (object) $cartArray;
                session_id($data->session_id);

                session_start();

                if (isset($_SESSION['default']['customer_id'])) {
                    $customer = $_SESSION['default']['customer_id'];
                } else {
                    $customer = 0;
                }

                $product_id = (int) $data->id;
                if ($data->options) {
                    $option = $data->options;
                }
                $conn = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

                $query = mysqli_query($conn, "SELECT cart_id, COUNT(*) AS total FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $customer . "' AND session_id = '" . $data->session_id . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . json_encode($option) . "'");
                $row = mysqli_fetch_assoc($query);

                if (!$row['total']) {
                    $status = mysqli_query($conn, "INSERT " . DB_PREFIX . "cart SET customer_id = '" . (int) $customer . "', session_id = '" . $data->session_id . "', product_id = '" . (int) $product_id . "', recurring_id = '" . (int) $recurring_id . "', `option` = '" . json_encode($option) . "', quantity = '" . (int) $data->qty . "', date_added = NOW()");
                    $cart_id = mysqli_insert_id($conn);
                } else {
                    $status = mysqli_query($conn, "UPDATE " . DB_PREFIX . "cart SET quantity = (quantity + " . (int) $data->qty . ") WHERE customer_id = '" . (int) $customer . "' AND session_id = '" . $data->session_id . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
                    $cart_id = $row['cart_id'];
                }
                if (isset($data->extra_price)) {
                    $_SESSION['cart-design'][$cart_id]['extra-price'] = $data->extra_price;
                }
                if (isset($data->refid)) {
                    $_SESSION['cart-design'][$cart_id]['refid'] = $data->refid;
                }
            }
            if ($status) {
                return true;
            }
        } else {
            $cartData = (array) $cartData;
            $status = false;
            foreach ($cartData as $cartArray) {
                $data = (object) $cartArray;
                session_id($data->session_id);

                session_start();
                if (isset($_SESSION[$data->session_id]['customer_id'])) {
                    $customer = $_SESSION[$data->session_id]['customer_id'];
                } else {
                    $customer = 0;
                }

                $product_id = (int) $data->id;
                if ($data->options) {
                    $option = $data->options;
                }
                $conn = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

                $query = mysqli_query($conn, "SELECT cart_id, COUNT(*) AS total FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($_SESSION['data']['api_id']) ? (int) $_SESSION['data']['api_id'] : 0) . "' AND customer_id = '" . (int) $customer . "' AND session_id = '" . $data->session_id . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . json_encode($option) . "'");
                $row = mysqli_fetch_assoc($query);

                if (!$row['total']) {
                    $status = mysqli_query($conn, "INSERT " . DB_PREFIX . "cart SET api_id = '" . (isset($_SESSION['data']['api_id']) ? (int) $_SESSION['data']['api_id'] : 0) . "', customer_id = '" . (int) $customer . "', session_id = '" . $data->session_id . "', product_id = '" . (int) $product_id . "', recurring_id = '" . (int) $recurring_id . "', `option` = '" . json_encode($option) . "', quantity = '" . (int) $data->qty . "', date_added = NOW()");
                    $cart_id = mysqli_insert_id($conn);
                } else {
                    $status = mysqli_query($conn, "UPDATE " . DB_PREFIX . "cart SET quantity = (quantity + " . (int) $data->qty . ") WHERE api_id = '" . (isset($_SESSION['data']['api_id']) ? (int) $_SESSION['data']['api_id'] : 0) . "' AND customer_id = '" . (int) $customer . "' AND session_id = '" . $data->session_id . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
                    $cart_id = $row['cart_id'];
                }
                if (isset($data->extra_price)) {
                    $_SESSION['cart-design'][$cart_id]['extra-price'] = $data->extra_price;
                }
                if (isset($data->refid)) {
                    $_SESSION['cart-design'][$cart_id]['refid'] = $data->refid;
                }
            }
            if ($status) {
                return true;
            }
        }
        return false;
    }
    public function log($text, $append = true, $fileName = '')
    {
        $file = 'log_cart.log';
        if ($fileName) {
            $file = $fileName;
        }

        $file = dirname(__FILE__) . '/../' . $file;

        // Write the contents to the file,
        // using the FILE_APPEND flag to append the content to the end of the file
        // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
        //file_put_contents($file, $text, FILE_APPEND | LOCK_EX);

        if ($append) {
            file_put_contents($file, $text . PHP_EOL, FILE_APPEND | LOCK_EX);
        } else {
            file_put_contents($file, $text);
        }

    }
}
