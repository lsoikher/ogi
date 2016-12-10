<?php

/**
 * @version 2.1.4
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. https://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */
// no direct access
function_exists('add_action') or die;

class PWebContact_Captcha
{

    private $public_key = '6LcPnAoTAAAAAELHJ46n697mYqOKzC_kyvhmmu5s';
    private $private_key = '6LcPnAoTAAAAAO9ZxguoPNVGHwa-62AazCFVcJQe';
    private $options = array('theme' => 'light');

    public function __construct($options = null)
    {
        if (is_array($options))
        {
            $this->options = array_merge($this->options, $options);
        }
    }

    public function display($id, $class = '')
    {
        return '<div id="' . $id . '" class="' . $class . '" style="min-height:78px;min-width:304px"></div>'
                . '<script type="text/javascript">'
                . 'jQuery(document).ready(function($){'
                . '$(window).load(function(){'
                // Get captcha widget ID
                . 'var grecaptchaId='
                // Render captcha
                . 'grecaptcha.render("' . $id . '",{'
                . 'sitekey:"' . $this->public_key . '"'
                . ',theme:"' . $this->options['theme'] . '"'
                . ($this->options['form_id'] ? ',"expired-callback":function(){pwebContact' . $this->options['form_id'] . '.captchaExpired()}' : '')
                . '});'
                // Store captcha widget ID
                . '$("#pwebcontact' . $this->options['form_id'] . '_captcha").data("grecaptchaId",grecaptchaId)'
                . '})'
                . '});'
                . '</script>';
    }

    public function checkAnswer()
    {
        $remoteip = PWebContact::detectIP();
        $answer = isset($_POST['g-recaptcha-response']) ? (string) $_POST['g-recaptcha-response'] : null;

        // Discard spam submissions
        if (empty($remoteip) || empty($answer))
        {
            return false;
        }

        $http = new WP_Http;
        $response = $http->post('https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret' => $this->private_key,
                'response' => $answer,
                'remoteip' => $remoteip
            ),
            'timeout' => 5,
            'sslverify' => false)
        );

        if (is_wp_error($response))
        {
            throw new Exception($response);
        }
        elseif ($response['response']['code'] >= 200 AND $response['response']['code'] < 400)
        {
            $result = json_decode($response['body'], true);
            if (isset($result['success']) && $result['success'] == true)
            {
                return true;
            }
            elseif (isset($result['error-codes']) && $result['error-codes'])
            {
                throw new Exception(implode(', ', $result['error-codes']));
            }
        }

        return false;
    }

}
