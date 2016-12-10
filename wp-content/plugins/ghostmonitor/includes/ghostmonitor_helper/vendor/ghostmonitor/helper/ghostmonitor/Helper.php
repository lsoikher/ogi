<?php namespace Ghostmonitor;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Ghostmonitor\Logger;

class Helper
{
    public $ghostmonitor_domain;
    public $ghostmonitor_id;
    public $api_url;
    public $api_base_url;
    public $cdn_url;
    public $url_params = array (
        'ghostmonitor_session_id',
        'utm_source',
        'utm_medium',
        'utm_term',
        'utm_content',
        'utm_campaign',
    );

    public $api_version = 'v1';

    private $logger;

    public function __construct($ghostmonitor_id, $ghostmonitor_domain = false, $api_url = false, $cdn_url = false)
    {
        $this->ghostmonitor_domain = $ghostmonitor_domain;
        $this->ghostmonitor_id = $ghostmonitor_id;

        $this->api_base_url = ($api_url ?: 'https://tracking.ghostmonitor.com') . '/';
        $this->api_url = $this->api_base_url . $this->api_version . '/';
        $this->cdn_url = $cdn_url  ?: 'https://cdn.ghostmonitor.com';

        $this->logger = new Logger($this->ghostmonitor_id, $this->ghostmonitor_domain, $this->setGhostmonitorSessionId());
    }

    public function ghost_init($is_cart_empty = true, $additional_lines = '')
    {
        $is_cart_empty = $is_cart_empty ? 'true' : 'false';

        $domain = $this->ghostmonitor_domain[0] === '.' ? $this->ghostmonitor_domain : '.' . $this->ghostmonitor_domain;

        $js_domain = strlen($domain) < 2 ? '' : "_ghostmonitor.push(['setDomain', '" . $domain . "']);";

        $loginfo = $this->logger->getLoggingInfo();

        return "
            <!-- Ghostmonitor Init Script -->
            <script type='text/javascript'>
                var _ghostmonitor = _ghostmonitor || [];
                _ghostmonitor.push(['isCartEmpty', '" . $is_cart_empty . "']);
                _ghostmonitor.push(['setAccount', '" . $this->ghostmonitor_id . "']);
                $js_domain

                (function() {
                    var s = document.createElement('script');
                    s.type = 'text/javascript';
                    s.async = true;
                    s.src = '" . $this->cdn_url . "/loader.js?'+g();
                    var x = document.getElementsByTagName('script')[0];
                    x.parentNode.insertBefore(s, x);
                    function g(){var t=new Date,e=t.getHours();return e=e%2===0?e:e-1,String(t.getFullYear())+t.getMonth()+t.getDate()+e};
                })();$loginfo
            </script>
            $additional_lines
        ";
    }

    public function sendGhostData($ghostmonitorData, $version = 1)
    {
        $this->logDebug('HELPER sendGhostData() VERSION: ' . $version);

        if (false === $ghostmonitorData) {
            return false;
        }

        $this->validateData($ghostmonitorData);

        switch ($version) {
            case 1:
                $this->sendCartData($ghostmonitorData['setCartData']);
                $this->sendCartItem($ghostmonitorData['setCartItem']);
                break;
            case 2:
                $ghostmonitorData = $this->convertGMDataToJson($ghostmonitorData);
                $this->postGmData($ghostmonitorData, $this->ghostmonitor_id . '/bulk', true);
                break;
        }

        return true;
    }

    public function sendConversionData($session_id = false)
    {
        $this->logDebug('HELPER sendConversionData()');
        $conversionData = $this->getConversionData($session_id);

        $this->postGmData($conversionData, $this->ghostmonitor_id . '/setconversion');

        return true;
    }

    public function sendShopData($data = array())
    {
        if (!$this->ghostmonitor_id) {
            $data['site_url'] = $this->ghostmonitor_domain;
        } else {
            $data['ghostmonitor_site_id'] = $this->ghostmonitor_id;
        }

        $this->postGmData($data, 'install_info');
    }

    public function setGhostmonitorSessionId()
    {
        // TODO: debug logging error
        // $this->logError('XYZ') results in Call to a member function logError() on null
        return isset($_COOKIE['ghostmonitor_session_id']) ? $_COOKIE['ghostmonitor_session_id'] : false;
    }
    
    public function getConversionData($session_id = false)
    {
        $conversionData = array();

        if (is_array($session_id)) {
            $conversionData = $session_id;
        } else {
            $conversionData['session_id'] = $session_id ?: $this->setGhostmonitorSessionId();
            $conversionData['fromGhostmonitor'] = isset($_COOKIE['ghostmonitor_utm_source']) ? 'true' : 'false';
        }

        try {
            Assertion::isArray($conversionData, '$conversionData has to be an array');
            Assertion::notEmpty($conversionData, '$conversionData can\'t be empty');
            Assertion::keyExists($conversionData, 'session_id', '$conversionData has to contain a session_id key');
            Assertion::keyExists($conversionData, 'fromGhostmonitor', '$conversionData has to contain a fromGhostmonitor key');
            Assertion::notEmptyKey($conversionData, 'session_id', 'session_id can\'t be empty');
            Assertion::notEmptyKey($conversionData, 'fromGhostmonitor', 'fromGhostmonitor key can\'t be empty');
        } catch (AssertionFailedException $ex) {
            $exceptions = array($ex->getValue(), $ex->getConstraints(), $ex->getMessage());
            $this->logError($exceptions);
        }

        $this->logDebug(array('HELPER getConversionData()', 'CONVERSION DATA', $conversionData));

        return $conversionData;
    }

    public function validateDiscount($gm_session_id)
    {
        # TODO: add validation logic

        return array(
            'valid' => false,
            'amount' => 20,
            'discount_type' => 'percent',
            'discount_code' => $gm_session_id,
            'discount_name' => 'Ghostmonitor Discount',
        );
    }

    public function validateData($ghostmonitorData)
    {
        try {
            Assertion::isArray($ghostmonitorData, 'Ghostmonitor data has to be an array.');
            Assertion::notEmpty($ghostmonitorData, 'Ghostmonitor data can\'t be empty');

            Assertion::keyExists($ghostmonitorData, 'setCartData', 'Ghostmonitor data must has a setCartData key');
            Assertion::keyExists($ghostmonitorData, 'setCartItem', 'Ghostmonitor data must has a setCartItem key');

            Assertion::keyExists($ghostmonitorData['setCartData'], 'value', 'Key "value" must exist in setCartData');
            Assertion::keyExists($ghostmonitorData['setCartData'], 'itemCount', 'Key "itemCount" must exist in setCartData');
            Assertion::keyExists($ghostmonitorData['setCartData'], 'returnUrl', 'Key "returnUrl" must exist in setCartData');
            Assertion::keyExists($ghostmonitorData['setCartData'], 'session_id', 'Key "session_id" must exist in setCartData');

            Assertion::isArray($ghostmonitorData['setCartData'], 'setCartData has to be an array.');
            Assertion::isArray($ghostmonitorData['setCartItem'], 'setCartItem has to be an array.');

            Assertion::notEmptyKey($ghostmonitorData, 'setCartData', 'setCartData can\'t be empty');
            Assertion::notEmptyKey($ghostmonitorData, 'setCartItem', 'setCartItem can\'t be empty');

            Assertion::numeric($ghostmonitorData['setCartData']['value'], 'The value has to be numeric');

            Assertion::notEmpty($ghostmonitorData['setCartData']['itemCount'], 'The item count must exist in setCartData');
            Assertion::integerish($ghostmonitorData['setCartData']['itemCount'], 'The item count has to be an integer');

            Assertion::notEmpty($ghostmonitorData['setCartData']['returnUrl'], 'The returnUrl must not be empty');

            $returnUrl = urldecode(stripslashes($ghostmonitorData['setCartData']['returnUrl']));
            Assertion::url($returnUrl, 'The returnUrl value has to be a valid URL');

            Assertion::notEmpty($ghostmonitorData['setCartData']['session_id'], 'A value has to be given to the session_id in setCartData');
            Assertion::string($ghostmonitorData['setCartData']['session_id'], 'The session_id has to be a string');

            foreach ($ghostmonitorData['setCartItem'] as $cartItem) {
                Assertion::keyExists($cartItem, 'productId', 'Key "productId" must exist in setCartItem');
                Assertion::keyExists($cartItem, 'name', 'Key "name" must exist in setCartItem');
                Assertion::keyExists($cartItem, 'qty', 'Key "qty" must exist in setCartItem');
                Assertion::keyExists($cartItem, 'price', 'Key "price" must exist in setCartItem');
                Assertion::keyExists($cartItem, 'qtyPrice', 'Key "qtyPrice" must exist in setCartItem');
                Assertion::keyExists($cartItem, 'imageUrl', 'Key "imageUrl" must exist in setCartItem');
                Assertion::keyExists($cartItem, 'session_id', 'Key "session_id" must exist in setCartItem');

                Assertion::notEmpty($cartItem['productId']);

                Assertion::notEmpty($cartItem['name']);
                Assertion::string($cartItem['name']);

                Assertion::notEmpty($cartItem['qty']);
                Assertion::integerish($cartItem['qty']);

                Assertion::notEmpty($cartItem['price']);
                Assertion::numeric($cartItem['price']);

                Assertion::notEmpty($cartItem['qtyPrice']);
                Assertion::numeric($cartItem['qtyPrice']);

                Assertion::notEmpty($cartItem['imageUrl']);
                Assertion::url($cartItem['imageUrl']);

                Assertion::notEmpty($cartItem['session_id'], 'A value has to be given to the session_id in setCartItem');
            }
        } catch (AssertionFailedException $ex) {
            $exceptions = array($ex->getValue(), $ex->getConstraints(), $ex->getMessage());
            $this->logError(array('HELPER validateData() failed', $exceptions));
            return false;
        }

        $this->logDebug('HELPER validateData() successful');

        return true;
    }
    
    public function testHTTP()
    {
        $result = $this->postGmData(array(), $this->api_base_url . 'version', false, false);
        error_log('test http: ' . var_export($result, true));
        return $result;
    }

    private function convertGMDataToJson($ghostmonitorData)
    {
        unset($ghostmonitorData['setCartData']['session_id']);
        $convertedData = array();

        array_push($convertedData, array (
            'endpoint' => 'setCartData',
            'args' => array (
                'sessionId' => $ghostmonitorData['session_id'],
                'siteId' => $ghostmonitorData['site_id'],
                'cartData' => $ghostmonitorData['setCartData']
            )
        ));

        foreach ($ghostmonitorData['setCartItem'] as $cartItem) {
            unset($cartItem['session_id']);
            array_push($convertedData, array (
                'endpoint' => 'setCartItem',
                'args' => array (
                    'sessionId' => $ghostmonitorData['session_id'],
                    'siteId' => $ghostmonitorData['site_id'],
                    'cartItem' => $cartItem
                )
            ));
        }

        $json = json_encode($convertedData);

        if (false === $json) {
            $this->logError(array('json encoding error', json_last_error(), json_last_error_msg()));
        }

        return $json;
    }

    private function sendCartData($cartData)
    {
        $this->postGmData($cartData, $this->ghostmonitor_id . '/setcartdata');
        return true;
    }

    private function sendCartItem($cartItem)
    {
        foreach ($cartItem as $cI) {
            $this->postGmData($cI, $this->ghostmonitor_id . '/setcartitem');
        }

        return true;
    }

    private function postGmData($data, $api_path, $json = false, $post = true)
    {
        $content_type = $json ? 'Content-type: application/json' : 'Content-type: application/x-www-form-urlencoded';

        $url = strpos($api_path, 'http') === 0 ? $api_path : $this->api_url . $api_path;

        error_log('POST URL: ' . $url);

        $post_data = $json ? $data : http_build_query((array)$data);
        
        // check if curl is loaded and curl_exec function is enabled
        $send_with_curl = extension_loaded('curl') && strpos(ini_get('disable_functions'), 'curl_exec') === false;

        $this->logDebug(array(
            'HELPER postGmData() before sending',
            'METHOD: ' . ($send_with_curl ? 'CURL' : 'FILE_GET_CONTENTS'),
            'POST DATA', $post_data,
            'POST URL', $url
        ));

        if ($send_with_curl) {

            $ch = curl_init();

            // TODO: URL validáció!
        
            // redefine constant if not exists for PHP 5.3 compatibility
            if (!defined('CURLOPT_TIMEOUT_MS')) {
                define('CURLOPT_TIMEOUT_MS', 156);
            }

            // set curl opts for GET request
            $opts = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT_MS => 5000,
            );

            // if post is true configure curl for post req
            if ($post === true) {
                $opts = $opts + array(
                    CURLOPT_POST       => $post,
                    CURLOPT_POSTFIELDS => $post_data,
                    CURLOPT_NOSIGNAL   => 1,
                    CURLOPT_HTTPHEADER => array($content_type)
                );
            }

            curl_setopt_array($ch, $opts);

            $result = curl_exec($ch);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            $curl_info  = curl_getinfo($ch);
            curl_close($ch);

            $this->logDebug(array('CURL INFO', $curl_info));

            if ($curl_errno != "" || $curl_error != "") {
                $this->logError('CURL ERROR: ' . $curl_errno . ' - ' . $curl_error);
                return false;
            }

            return $result;
        }
        
        if (ini_get('allow_url_fopen') == 0) {
            return false;
        }

        $options = array('http' => array (
            'method'  => $post ? 'POST' : 'GET',
            'timeout' => (float)5,
            'ignore_errors' => true,
            'protocol_version' => (float)1.1,
        ));

        if ($post === true) {
            $options['http'] = $options['http'] + array(
                'header'  => $content_type,
                'content' => $post_data
            );
        }

        $context = stream_context_create($options);
        $result  = file_get_contents($url, false, $context);

        $this->logDebug(array('FILE_GET_CONTENTS RESULT', $result));

        if (false === $result) {
            $this->logError(array('FILE_GET_CONTENTS_ERROR','last_error' => error_get_last()));
            return false;
        }

        return $result;
    }

    public function getInlineGMScripts($ghostmonitorData)
    {
        if (empty($ghostmonitorData['setCartData']) || empty($ghostmonitorData['setCartItem'])) {
            return false;
        }
        $script = '<script type=\'text/javascript\'>' . PHP_EOL;

        $gm_push = '_ghostmonitor.push([%s, %s]);' . PHP_EOL;

        // add setCartData
        unset($ghostmonitorData['setCartData']['session_id']);

        $script .= sprintf($gm_push, '\'setCartData\'', json_encode($ghostmonitorData['setCartData']));

        // add setCartItems
        foreach ($ghostmonitorData['setCartItem'] as $cartItem) {
            unset($cartItem['session_id']);

            $script .= sprintf($gm_push, '\'setCartItem\'', json_encode($cartItem));
        }

        $script = $script . '</script>' . PHP_EOL;

        return $script;
    }

    // Helper functions for logging
    public function logDebug($line)
    {
        $this->logger->logDebug($line);
    }

    // Helper functions for logging
    public function logError($line)
    {
        $this->logger->logError($line);
    }

    public function log($line, $severity = 'Error')
    {
        $this->logger->log($line, $severity);
    }

    public function setLogPath($path = '')
    {
        $this->logger->log_path = $path;
    }

    public function setLogentriesToken($path = '')
    {
        $this->logger->logentries_token = $path;
    }

    public function isLoggingEnabled()
    {
        return $this->logger->isLoggingEnabled();
    }
}
