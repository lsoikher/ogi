<?php
class InstapageCmsPluginAPIModel
{
  private static $apiModel = null;
  
  public static function getInstance()
  {
    if( self::$apiModel === null )
    {
      self::$apiModel = new InstapageCmsPluginAPIModel();
    }

    return self::$apiModel;
  }

  public function remotePost( $url, $data = array(), $headers = array() )
  {
    return InstapageCmsPluginConnector::getSelectedConnector()->remotePost( $url, $data, $headers );
  }

  public function enterpriseCall( $url, $host = '', $cookies = false )
  {
    $data = array();
    $headers = array();
    $host = $host ? $host : $_SERVER[ 'HTTP_HOST' ];
    $integration = InstapageCmsPluginConnector::getSelectedConnector()->name;
    $data[ 'integration' ] = $integration;
    $data[ 'useragent' ] = $_SERVER[ 'HTTP_USER_AGENT' ];
    $data[ 'ip' ] = $_SERVER[ 'REMOTE_ADDR' ];
    $data[ 'cookies' ] = $cookies;
    $data[ 'custom' ] = @InstapageCmsPluginHelper::getVar( $_GET[ 'custom' ], null );
    $data[ 'variant' ] = @InstapageCmsPluginHelper::getVar( $_GET[ 'variant' ], null );
    $headers[ 'integration' ] = $integration;
    $headers[ 'host' ] = $host;
    $response = InstapageCmsPluginConnector::getSelectedConnector()->remoteRequest( $url, $data, $headers, 'POST' );

    InstapageCmsPluginHelper::writeDiagnostics( $url, 'Enterprise call URL' );
    InstapageCmsPluginHelper::writeDiagnostics( $host, 'Enterprise call host' );
    InstapageCmsPluginHelper::writeDiagnostics( $headers, 'Enterprise call headers' );
    InstapageCmsPluginHelper::writeDiagnostics( $data, 'Enterprise call data');
    InstapageCmsPluginHelper::writeDiagnostics( $response, 'Enterprise call response');
    
    return $response;
  }

  public function apiCall( $action, $data = array(), $headers = array(), $method = 'POST' )
  {
    $integration = InstapageCmsPluginConnector::getSelectedConnector()->name;
    $url = INSTAPAGE_APP_ENDPOINT . '/' . $action;
    $headers[ 'integration' ] = $integration;
    $response = InstapageCmsPluginConnector::getSelectedConnector()->remoteRequest( $url, $data, $headers, $method );

    InstapageCmsPluginHelper::writeDiagnostics( $method . ' : ' . $url, 'API ' . $action . ' URL' );
    InstapageCmsPluginHelper::writeDiagnostics( $data, 'API ' . $action . ' data' );
    InstapageCmsPluginHelper::writeDiagnostics( $headers, 'API ' . $action . ' headers' );
    InstapageCmsPluginHelper::writeDiagnostics( $response, 'API ' . $action . ' response' );

    return isset( $response[ 'body' ] ) ? $response[ 'body' ] : null;
  }

  public function authorise( $email, $password )
  {
    $data = array( 'email' => $email, 'password' => $password );
    $response = $this->apiCall( 'page', $data );
  
    return $response;
  }
}
