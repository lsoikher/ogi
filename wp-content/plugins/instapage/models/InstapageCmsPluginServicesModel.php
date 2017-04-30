<?php
class InstapageCmsPluginServicesModel
{
  private static $servicesModel = null;
  
  public static function getInstance()
  {
    if( self::$servicesModel === null )
    {
      self::$servicesModel = new InstapageCmsPluginServicesModel();
    }

    return self::$servicesModel;
  }

  public function isServicesRequest()
  {
    if( strpos( $_SERVER[ 'REQUEST_URI' ], 'instapage-proxy-services' ) !== false )
    {
      InstapageCmsPluginHelper::writeDiagnostics( $_SERVER[ 'REQUEST_URI' ], 'Proxy services URL' );

      return true;
    }

    return false;
  }

  public function stripSlashesGpc( &$value )
  {
    $value = stripslashes( $value );
  }

  public function processProxyServices()
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $url = @InstapageCmsPluginHelper::getVar( $_GET[ 'url' ], '' );

    if( strpos( $url, 'ajax/pageserver/email' ) === false )
    {
      throw new Exception( 'Unsupported endpoint: ' . $url );
    }

    $url = INSTAPAGE_PROXY_ENDPOINT . $url;
    
    array_walk_recursive( $_POST, array( $this, 'stripSlashesGpc' ) );

    if ( isset( $_POST ) && !empty( $_POST ) )
    {
      $_POST[ 'user_ip' ] = $_SERVER[ 'REMOTE_ADDR' ];
    }

    $data = $_POST;
    $data[ 'ajax' ] = 1;
    $response = $api->remotePost( $url, $data );

    if( isset( $response[ 'response' ][ 'code' ] ) && $response[ 'response' ][ 'code' ] !== 200 )
    {
      $this->disableCrossOriginProxy();
      $matches = array();
      $pattern = '/email\/(\d*)/';
      preg_match( $pattern, $url, $matches );
      $instapage_id = isset( $matches[ 1 ] ) ? $matches[ 1 ] : 0;
    }

    InstapageCmsPluginHelper::writeDiagnostics( $url, 'Proxy services URL' );
    InstapageCmsPluginHelper::writeDiagnostics( $data, 'Proxy data' );
    InstapageCmsPluginHelper::writeDiagnostics( $response, 'Proxy response' );

    $status = @InstapageCmsPluginHelper::getVar( $response->status );
    $response_code = @InstapageCmsPluginHelper::getVar( $response[ 'response' ][ 'code' ], 200 );
    
    if ( $status === 'ERROR' )
    {
      $error_message = @InstapageCmsPluginHelper::getVar( $response->message );

      if ( !empty( $error_message ) )
      {
        throw new Exception( $error_message );
      }
      else
      {
        throw new Exception( '500 Internal Server Error' );
      }
    }

    ob_start();
    ob_end_clean();
    header( 'Content-Type: text/json; charset=UTF-8' );
    echo trim( @InstapageCmsPluginHelper::getVar( $response[ 'body' ], '') );
    status_header( $response_code );

    exit;
  }

  private function disableCrossOriginProxy()
  {
    $options = InstapageCmsPluginHelper::getOptions();
    $options->config->crossOrigin = 0;
    InstapageCmsPluginHelper::updateOptions( $options );
  }

  private function notifyCustomerSupport( $instapage_id )
  {
    $to = INSTAPAGE_SUPPORT_EMAIL;
    $subject = InstapageCmsPluginConnector::lang( 'Instapage WP Plugin: Problem with Cross-Origin Proxy' );
    $message = InstapageCmsPluginConnector::lang( 'There is a problem with sending leads.' ) . "\n";
    $message .= InstapageCmsPluginConnector::lang( 'Domain: ' ) . InstapageCmsPluginConnector::getHomeURL() . "\n";
    $pageModel = InstapageCmsPluginPageModel::getInstance();
    
    if( $instapage_id )
    {
      $message .= InstapageCmsPluginConnector::lang( 'Page ID: ' ) . $instapage_id . "\n";
      $pages = $pageModel->getByInstapageId( $instapage_id, array( 'slug' ) );
      
      foreach( $pages as $item )
      {
        $message .= InstapageCmsPluginConnector::lang( 'Page URL: ' ) . InstapageCmsPluginConnector::getHomeURL() . '/' . $item->slug . "\n";
      }

      $message .= 'Cross-Origin proxy automatically disabled.';
    }

    InstapageCmsPluginHelper::writeDiagnostics( $message, 'Proxy Services Disabled' );
    InstapageCmsPluginConnector::mail( $to, $subject, $message );
  }
}
