<?php

class WC_Autoship_Pipey_Exception extends Exception {
	private $_response;
	private $_url;

	/**
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * @param mixed $response
	 */
	public function setResponse($response)
	{
		$this->_response = $response;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url)
	{
		$this->_url = $url;
	}
}