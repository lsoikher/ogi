<?php

/**
 * @version 2.3.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. https://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */
// no direct access
function_exists('add_action') or die;

class PWebContact_GoogleApi
{

    protected $options;
    protected $http;
    protected static $instance = null;

    public static function getInstance()
    {
        // Automatically instantiate the singleton object if not already done.
        if (empty(self::$instance))
        {
            self::$instance = new PWebContact_GoogleApi();
        }
        return self::$instance;
    }

    public function __construct($options = null)
    {
        $this->options = array(
            'clientid' => '300313169789-tjp5sq43n8shqra9okb1j06ovlsrh0b6.apps.googleusercontent.com',
            'clientsecret' => 'M-RH9S2zGtGv12PdCzQ-LKJs',
            'sendheaders' => false,
            'authmethod' => 'bearer',
            'requestparams' => array('access_type' => 'offline', 'approval_prompt' => 'auto'),
            'authurl' => 'https://accounts.google.com/o/oauth2/auth',
            'tokenurl' => 'https://accounts.google.com/o/oauth2/token',
            'tokenstore' => 'pwebcontact_googledocs_token',
            'redirecturi' => 'urn:ietf:wg:oauth:2.0:oob',
            'userefresh' => true,
            'usecookie' => false,
            'cookiename' => '',
            'timeout' => 15,
            'sslverify' => true
        );

        if (is_array($options))
            $this->options = array_merge($this->options, (array) $options);

        $this->http = new WP_Http;
    }

    public function getOption($key)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : null;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function log($message = null, $level = E_USER_NOTICE)
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG)
            return;

        switch ($level)
        {
            case E_USER_ERROR:
                $prefix = '   Error     ';
                break;
            case E_USER_WARNING:
                $prefix = '   Warning   ';
                break;
            case E_USER_NOTICE:
            default:
                $prefix = '   Notice    ';
        }

        error_log("\r\n" . date('Y-m-d H:i:s') . $prefix . $message, 3, WP_CONTENT_DIR . '/debug.log');
    }

    /**
     * Get the access token or redict to the authentication URL.
     *
     * @return  WP_Error|string The access token or WP_Error on failure.
     */
    public function authenticate()
    {
        $this->log(__METHOD__);

        if (isset($_GET['code']) AND ( $data['code'] = $_GET['code']))
        {
            $data['grant_type']    = 'authorization_code';
            $data['redirect_uri']  = $this->getOption('redirecturi');
            $data['client_id']     = $this->getOption('clientid');
            $data['client_secret'] = $this->getOption('clientsecret');

            $response = $this->http->post($this->getOption('tokenurl'), array('body' => $data, 'timeout' => $this->getOption('timeout'), 'sslverify' => $this->getOption('sslverify')));

            if (is_wp_error($response))
            {
                return $response;
            }
            elseif ($response['response']['code'] >= 200 AND $response['response']['code'] < 400)
            {
                if (isset($response['headers']['content-type']) AND strpos($response['headers']['content-type'], 'application/json') !== false)
                {
                    $token = array_merge(json_decode($response['body'], true), array('created' => time()));
                }
                else
                {
                    parse_str($response['body'], $token);
                    $token = array_merge($token, array('created' => time()));
                }

                $this->setToken($token);

                return $token;
            }
            else
            {
                return new WP_Error('oauth_failed', 'Error code ' . $response['response']['code'] . ' received requesting access token: ' . $response['body'] . '.');
            }
        }

        if ($this->getOption('sendheaders'))
        {
            // If the headers have already been sent we need to send the redirect statement via JavaScript.
            if (headers_sent())
            {
                echo "<script>document.location.href='" . str_replace("'", "&apos;", $this->createUrl()) . "';</script>\n";
            }
            else
            {
                // All other cases use the more efficient HTTP header for redirection.
                header('HTTP/1.1 303 See other');
                header('Location: ' . $this->createUrl());
                header('Content-Type: text/html; charset=utf-8');
            }

            die();
        }
        return false;
    }

    /**
     * Verify if the client has been authenticated
     *
     * @return  boolean  Is authenticated
     */
    public function isAuthenticated()
    {
        $this->log(__METHOD__);

        $token = $this->getToken();

        if (!$token || !array_key_exists('access_token', $token))
        {
            return false;
        }
        elseif (array_key_exists('expires_in', $token) && $token['created'] + $token['expires_in'] < time() + 20)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Create the URL for authentication.
     *
     * @return  WP_Error|string The URL or WP_Error on failure.
     */
    public function createUrl()
    {
        $this->log(__METHOD__);

        if (!$this->getOption('authurl') || !$this->getOption('clientid'))
        {
            return new WP_Error('oauth_failed', 'Authorization URL and client_id are required');
        }

        $url = $this->getOption('authurl');

        if (strpos($url, '?'))
        {
            $url .= '&';
        }
        else
        {
            $url .= '?';
        }

        $url .= 'response_type=code';
        $url .= '&client_id=' . urlencode($this->getOption('clientid'));

        if ($this->getOption('redirecturi'))
        {
            $url .= '&redirect_uri=' . urlencode($this->getOption('redirecturi'));
        }

        if ($this->getOption('scope'))
        {
            $scope = is_array($this->getOption('scope')) ? implode(' ', $this->getOption('scope')) : $this->getOption('scope');
            $url .= '&scope=' . urlencode($scope);
        }

        if ($this->getOption('state'))
        {
            $url .= '&state=' . urlencode($this->getOption('state'));
        }

        if (is_array($this->getOption('requestparams')))
        {
            foreach ($this->getOption('requestparams') as $key => $value)
            {
                $url .= '&' . $key . '=' . urlencode($value);
            }
        }

        return $url;
    }

    public function hasToken()
    {
        $token = $this->getToken();
        return (is_array($token) && !empty($token['access_token']) && !empty($token['refresh_token']));
    }

    public function getToken()
    {
        $token = $this->getOption('accesstoken');
        if (empty($token))
        {
            $token = (array) get_option($this->getOption('tokenstore'), array());
            $this->setOption('accesstoken', $token);
        }
        return $token;
    }

    public function setToken($token)
    {
        if (is_array($token))
        {
            // backup the old token
            $old_token = (array) $this->getOption('accesstoken');

            if (!array_key_exists('expires_in', $token) && array_key_exists('expires', $token))
            {
                $token['expires_in'] = $token['expires'];
                unset($token['expires']);
            }
            // make sure that refresh token was not removed
            if (!array_key_exists('refresh_token', $token) && array_key_exists('refresh_token', $old_token))
            {
                $token['refresh_token'] = $old_token['refresh_token'];
            }

            // set a new token
            $this->setOption('accesstoken', $token);

            // save the new token
            update_option($this->getOption('tokenstore'), $token);
        }

        return $this;
    }

    public function setAccessCode($code)
    {
        $_GET['code'] = $code;
        return $this->authenticate();
    }

    public function createAccessCodeUrl($scope = null)
    {
        return $this->getOption('authurl')
                . '?access_type=offline'
                . '&approval_prompt=force'
                . '&client_id=' . urlencode($this->getOption('clientid'))
                . '&redirect_uri=' . urlencode($this->getOption('redirecturi'))
                . '&response_type=code'
                . '&scope=' . urlencode($scope);
    }

    /**
     * Refresh the access token instance.
     *
     * @param   string  $token  The refresh token
     *
     * @return  WP_Error|array  The new access token or WP_Error on failure.
     */
    public function refreshToken($token = null)
    {
        $this->log(__METHOD__);

        if (!$this->getOption('userefresh'))
        {
            return new WP_Error('oauth_failed', 'Refresh token is not supported for this OAuth instance.');
        }

        if (!$token)
        {
            $token = $this->getToken();

            if (!array_key_exists('refresh_token', $token))
            {
                return new WP_Error('oauth_failed', 'No refresh token is available.');
            }
            $token = $token['refresh_token'];
        }
        $data['grant_type']    = 'refresh_token';
        $data['refresh_token'] = $token;
        $data['client_id']     = $this->getOption('clientid');
        $data['client_secret'] = $this->getOption('clientsecret');

        $response = $this->http->post($this->getOption('tokenurl'), array('body' => $data, 'timeout' => $this->getOption('timeout'), 'sslverify' => $this->getOption('sslverify')));

        if (is_wp_error($response))
        {
            $this->log(__METHOD__ . '. Rrequest error: ' . $response->get_error_message(), E_USER_ERROR);
            return $response;
        }
        elseif ($response['response']['code'] >= 200 || $response['response']['code'] < 400)
        {
            if (strpos($response['headers']['content-type'], 'application/json') !== false)
            {
                $token = array_merge(json_decode($response['body'], true), array('created' => time()));
            }
            else
            {
                parse_str($response['body'], $token);
                $token = array_merge($token, array('created' => time()));
            }

            $this->setToken($token);

            return $token;
        }
        else
        {
            return new WP_Error('oauth_failed', 'Error code ' . $response['response']['code'] . ' received refreshing token: ' . $response['body'] . '.');
        }
    }

    /**
     * Send a signed Oauth request.
     *
     * @param   string  $url      The URL forf the request.
     * @param   mixed   $data     The data to include in the request
     * @param   array   $headers  The headers to send with the request
     * @param   string  $method   The method with which to send the request
     * @param   int     $timeout  The timeout for the request
     *
     * @return  WP_Error|array The response or WP_Error on failure.
     */
    public function query($url = null, $data = null, $headers = array(), $method = 'get', $timeout = null)
    {
        $this->log(__METHOD__ . '. URL: ' . $url . ' ' . print_r($data, true));

        $token = $this->getToken();
        if (is_array($token) && array_key_exists('expires_in', $token) && $token['created'] + $token['expires_in'] < time() + 20)
        {
            if (!$this->getOption('userefresh'))
            {
                return false;
            }
            $token = $this->refreshToken($token['refresh_token']);
        }

        if (!$this->getOption('authmethod') || $this->getOption('authmethod') == 'bearer')
        {
            $headers['Authorization'] = 'Bearer ' . $token['access_token'];
        }
        elseif ($this->getOption('authmethod') == 'get')
        {
            if (strpos($url, '?'))
            {
                $url .= '&';
            }
            else
            {
                $url .= '?';
            }
            $url .= $this->getOption('getparam') ? $this->getOption('getparam') : 'access_token';
            $url .= '=' . $token['access_token'];
        }

        $args = array(
            'method' => $method,
            'headers' => $headers,
            'timeout' => $timeout > 0 ? $timeout : $this->getOption('timeout'),
            'sslverify' => $this->getOption('sslverify')
        );

        switch ($method)
        {
            case 'get':
            case 'delete':
            case 'trace':
            case 'head':
                break;
            case 'post':
            case 'put':
            case 'patch':
                $args['body'] = $data;
                break;
            default:
                return new WP_Error('oauth_failed', 'Unknown HTTP request method: ' . $method . '.');
        }

        $response = $this->http->request($url, $args);

        $this->log(__METHOD__ . '. ' . print_r($response, true));

        if (is_wp_error($response))
        {
            $this->log(__METHOD__ . '. Request error: ' . $response->get_error_message(), E_USER_ERROR);
        }
        elseif ($response['response']['code'] < 200 OR $response['response']['code'] >= 400)
        {
            $error = __METHOD__ . '. Response code ' . $response['response']['code'] . ' received requesting data: ' . $response['body'] . '.';
            $this->log($error, E_USER_ERROR);
            return new WP_Error('oauth_failed', $error);
        }
        elseif (isset($response['headers']['content-type']) AND strpos($response['headers']['content-type'], 'application/json') !== false)
        {
            $response['body'] = json_decode($response['body']);
        }

        return $response;
    }

    /**
     * Make a REST request
     *
     * @param   string  $url      The URL for the request.
     * @param   mixed   $data     The data to include in the request.
     * @param   array   $headers  The headers to send with the request.
     * @param   string  $method   The type of http request to send.
     *
     * @return  mixed  Data from Google.
     *
     * @since   12.3
     */
    public function makeRESTRequest($url, $data = null, $headers = array(), $method = 'get', $timeout = null)
    {
        if (!isset($headers['Content-Type']))
        {
            $headers['Content-Type'] = 'application/json; charset=UTF-8';
        }
        if (!isset($headers['Accept-Encoding']))
        {
            $headers['Accept-Encoding'] = 'gzip';
        }

        if (!empty($data) && !is_scalar($data))
        {
            $data = json_encode($data);
        }

        return $this->query($url, $data, $headers, $method);
    }

    public function addRowToSpreadsheet($spreadsheet_id, $sheet_id, $row_values = array())
    {
        $values = array();
        foreach ($row_values as $value)
        {
            $values[] = array(
                'userEnteredValue' => array(
                    (is_numeric($value) ? 'numberValue' : 'stringValue') => $value
                )
            );
        }

        $data = array(
            'requests' => array(
                0 => array(
                    'appendCells' => array(
                        'sheetId' => $sheet_id,
                        'fields' => 'userEnteredValue',
                        'rows' => array(
                            'values' => $values
                        )
                    )
                )
            )
        );

        return $this->makeRESTRequest(
                'https://sheets.googleapis.com/v4/spreadsheets/' . $spreadsheet_id . ':batchUpdate'
                , $data
                , array()
                , 'post'
        );
    }

}
