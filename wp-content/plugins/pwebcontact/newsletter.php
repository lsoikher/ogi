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

class PWebContact_Freshmail
{

    public static function getLists($options)
    {
        $response = self::doRequest('subscribers_list/lists', $options);

        $lists = array();
        if ($response && isset($response->lists))
        {
            foreach ($response->lists as $list)
            {
                $lists[$list->subscriberListHash] = $list->name;
            }
        }

        return $lists;
    }

    public static function subscribe($id_list, $email, $name = '', $fields = array(), $options = array())
    {
        $custom_fields = array();
        foreach ($fields as $key => $value)
        {
            if (strpos($key, 'fm_') === 0)
            {
                $subkey = substr($key, 3);
                if ($subkey !== 'email')
                {
                    $custom_fields[$subkey] = $value;
                }
            }
        }

        return self::doRequest('subscriber/add', $options, array(
                    'email' => $email,
                    'list' => $id_list,
                    'custom_fields' => $custom_fields
        ));
    }

    protected static function doRequest($rest_path, $options, $data = null)
    {
        if (!defined('PWEBCONTACT_DEBUG'))
            define('PWEBCONTACT_DEBUG', false);

        if ($data !== null)
        {
            $data = json_encode($data);
        }

        $headers                   = array();
        $headers['X-Rest-ApiKey']  = $options['apikey'];
        $headers['X-Rest-ApiSign'] = sha1($options['apikey']
                . '/rest/' . $rest_path
                . $data
                . $options['secret']
        );
        $headers['Content-Type']   = 'application/json';
        $url                       = 'https://api.freshmail.com/rest/';

        try
        {
            if (empty($data))
                $response = wp_remote_get($url . $rest_path, array(
                    'headers' => $headers,
                    'sslverify' => false
                ));
            else
                $response = wp_remote_post($url . $rest_path, array(
                    'headers' => $headers,
                    'body' => $data,
                    'sslverify' => false
                ));

            if (is_wp_error($response))
            {
                if (PWEBCONTACT_DEBUG)
                    PWebContact::setLog('Freshmail request error: ' . $response->get_error_message());
                return false;
            }
            $result = json_decode($response['body']);
        }
        catch (Exception $e)
        {
            if (PWEBCONTACT_DEBUG)
                PWebContact::setLog('Freshmail HTTP request error: ' . $e->getMessage());
            return false;
        }

        if (PWEBCONTACT_DEBUG && isset($result->errors) && isset($result->errors[0]) && isset($result->errors[0]->message))
        {
            PWebContact::setLog('Freshmail REST response: ' . $result->errors[0]->message . ', REST code: ' . $result->errors[0]->code . ', HTTP code: ' . $response['response']['code']);
        }

        return $result;
    }

}

class PWebContact_Mailchimp
{

    public static function getLists($options)
    {
        $response = self::doRequest('lists/list.json', $options);

        $lists = array();
        if ($response && isset($response->data))
        {
            foreach ($response->data as $list)
            {
                $lists[$list->id] = $list->name;
            }
        }

        return $lists;
    }

    public static function subscribe($id_list, $email, $name = '', $fields = array(), $options = array())
    {
        $data = array(
            'id' => $id_list,
            'double_optin' => (!empty($options['opt']) ? true : false),
            'email' => array(
                'email' => $email
            )
        );

        if (!empty($name))
        {
            $fields['FNAME'] = $name;
        }
        $data['merge_vars'] = $fields;

        return self::doRequest('lists/subscribe.json', $options, $data, array('Content-Type: multipart/form-data'));
    }

    protected static function doRequest($rest_path, $options, $data = array(), $headers = array())
    {
        if (!defined('PWEBCONTACT_DEBUG'))
            define('PWEBCONTACT_DEBUG', false);

        $key_parts = explode('-', $options['apikey']);
        if (empty($options['apikey']) || count($key_parts) < 2)
        {
            if (PWEBCONTACT_DEBUG)
                PWebContact::setLog('Mailchimp error: ' . JText::_('MOD_PWEBCONTACT_BAD_API_KEY'));
            return false;
        }

        $data['apikey'] = $options['apikey'];
        $url            = 'https://' . $key_parts[1] . '.api.mailchimp.com/2.0/';

        try
        {
            $response = wp_remote_post($url . $rest_path, array(
                'headers' => $headers,
                'body' => $data,
                'sslverify' => false
            ));

            if (is_wp_error($response))
            {
                if (PWEBCONTACT_DEBUG)
                    PWebContact::setLog('Mailchimp request error: ' . $response->get_error_message());
                return false;
            }
            $result = json_decode($response['body']);
        }
        catch (Exception $e)
        {
            if (PWEBCONTACT_DEBUG)
                PWebContact::setLog('Mailchimp HTTP request error: ' . $e->getMessage());
            return false;
        }

        if (PWEBCONTACT_DEBUG)
        {
            if (isset($result->errors) && isset($result->errors[0]) && isset($result->errors[0]->error))
                PWebContact::setLog('Mailchimp REST response: ' . $result->errors[0]->error);
            elseif (isset($result->status) && $result->status == 'error')
                PWebContact::setLog('Mailchimp REST response: ' . $result->error);
        }

        return $result;
    }

}
