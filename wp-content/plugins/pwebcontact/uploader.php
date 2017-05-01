<?php
/**
 * @version 2.0.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. https://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// no direct access
function_exists('add_action') or die;

require_once (dirname(__FILE__).'/UploadHandler.php');

/**
 * Class that encapsulates the file-upload internals
 */
class PWebContact_Uploader extends UploadHandler
{
	public static function uploader()
	{
		$params = PWebContact::getParams();
		
		// check if upload is enabled
		if (!$params->get('show_upload', 0)) 
		{
			if (PWEBCONTACT_DEBUG) PWebContact::setLog('Uploader disabled');
			return array('status' => 402, 'files' => array());
		}
		
		$path = $params->get('upload_path');
        
        require_once ABSPATH . 'wp-admin/includes/file.php';
        
        if (function_exists('WP_Filesystem') AND WP_Filesystem()) {
            global $wp_filesystem;
            
            if (!$wp_filesystem->is_dir($path)) {
                $wp_filesystem->mkdir($path, 0755);
            }
            if (!$wp_filesystem->is_writable($path)) {
                $wp_filesystem->chmod($path, 0755);
            }
            if (!$wp_filesystem->is_writable($path)) {
                if (PWEBCONTACT_DEBUG) PWebContact::setLog('Upload dir is not writable');
                return array('status' => 403, 'files' => array());
            }
        }
        else {
            if (!is_dir($path)) {
                mkdir($path, 0755);
            }
            if (!is_writable($path)) {
                chmod($path, '0755');
            }
            if (!is_writable($path)) {
                if (PWEBCONTACT_DEBUG) PWebContact::setLog('Upload dir is not writable');
                return array('status' => 403, 'files' => array());
            }
        }

		// load uploader
		$uploader = new PWebContact_Uploader(array(
				'upload_dir' => $params->get('upload_path'),
	            'upload_url' => $params->get('upload_url'),
	            'accept_file_types' => '/(\.|\/)('.$params->get('upload_allowed_ext', '.+').')$/i',
	            'max_file_size' => ((float)$params->get('upload_size_limit', 1) * 1024 * 1024),
	            'image_versions' => array(),
	            // Set the following option to 'POST', if your server does not support
	            // DELETE requests. This is a parameter sent to the client:
	            'delete_type' => 'POST'
			), false, array(
				// translate messages
				1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'pwebcontact'),
                2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'pwebcontact'),
		        3 => __('The uploaded file was only partially uploaded', 'pwebcontact'),
		        4 => __('No file was uploaded', 'pwebcontact'),
		        6 => __('Missing a temporary folder', 'pwebcontact'),
		        7 => __('Failed to write file to disk', 'pwebcontact'),
		        8 => __('A PHP extension stopped the file upload', 'pwebcontact'),
		        'post_max_size' => __('The uploaded file exceeds the post_max_size directive in php.ini', 'pwebcontact'),
		        'max_file_size' => __('File is too large', 'pwebcontact'),
		        'accept_file_types' => __('File type not allowed', 'pwebcontact'),
                'max_number_of_files' => __('Maximum number of files exceeded', 'pwebcontact'),
                'abort' => __('File upload aborted', 'pwebcontact')
			));
		
		$response = $uploader->handleRequest();
		
		if (PWEBCONTACT_DEBUG) PWebContact::setLog('Uploader exit');
		
		return $response;
	}


	public static function deleteAttachments()
	{
		$attachments =  array();
        if (isset($_POST['attachments']) AND is_array($_POST['attachments'])) {
            $attachments = (array)$_POST['attachments'];
        }
		if (count($attachments)) 
		{
			$params = PWebContact::getParams();
			$path = $params->get('upload_path');
            
            require_once ABSPATH . 'wp-admin/includes/file.php';
            
            if (function_exists('WP_Filesystem') AND WP_Filesystem()) {
                global $wp_filesystem;
                
                foreach ($attachments as $file)
                    $wp_filesystem->delete($path . $file);
            }
            else {
                foreach ($attachments as $file)
                    unlink($path . $file);
            }
			
			if (PWEBCONTACT_DEBUG) PWebContact::setLog('Deleted '.count($attachments).' files');
		}
		elseif (PWEBCONTACT_DEBUG) PWebContact::setLog('No files to delete');
	}


	/* extend base methods */
	
	public function handleRequest()
	{
        $response = array();
        switch ($this->get_server_var('REQUEST_METHOD')) 
        {
            case 'OPTIONS':
            case 'HEAD':
                $response = $this->head();
                break;
            case 'GET':
                $response = $this->get();
                break;
            case 'PATCH':
            case 'PUT':
            case 'POST':
                $response = $this->post();
                break;
            case 'DELETE':
                $response = $this->delete();
                break;
            default:
                $this->header('HTTP/1.1 405 Method Not Allowed');
        }
		return $response;
    }

	public function post($print_response = true) 
	{
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->delete($print_response);
        }
		
		if (PWEBCONTACT_DEBUG) PWebContact::setLog('Uploading file');
		return parent::post($print_response);
	}

	public function delete($print_response = true) 
	{
		if (PWEBCONTACT_DEBUG) PWebContact::setLog('Deleting file');
		return parent::delete($print_response);
	}

	protected function body($str)
	{
		// Do not print, will be printed later
    }

	protected function header($str) 
	{
        $header = explode(':', $str);
		if (array_key_exists(1, $header))
			PWebContact::setHeader(trim($header[0]), trim($header[1]), true);
		else
			PWebContact::setHeader($str, null, true);
    }

	protected function get_download_url($file_name, $version = null, $direct = false)
	{
		// Disable download
		return null;
	}

	protected function set_additional_file_properties($file) 
	{
		parent::set_additional_file_properties($file);
		// Do not return delete URL
		$file->deleteUrl = null;
    }
}
