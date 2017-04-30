<?php
//Drupal 7 hooks

function instapage_cms_plugin_permission() {
  return array(
    'administer instapage_cms_plugin settings' => array(
      'title' => t( 'Administer Instapage settings' ),
      'description' => t( 'Allow users to administer Instapage settings.'),
    )
  );
}

function instapage_cms_plugin_menu() {

  $items = array();
  $items[ 'admin/structure/instapage_cms_plugin' ] = array(
    'title' => 'Instapage Plugin',
    'description' => 'The best way for Drupal to seamlessly publish landing pages as a natural extension of your website.',
    'page callback' => 'load_instapage_cms_plugin_dashboard',
    'access arguments' => array( 'administer instapage_cms_plugin settings' ),
    'type' => MENU_NORMAL_ITEM
  );

  return $items;
}

function instapage_cms_plugin_init()
{
  if( !menu_get_item( $_GET[ 'q' ] ) )
  {
    InstapageCmsPluginConnector::getSelectedConnector()->check404();
  }
}

function instapage_cms_plugin_install()
{
  user_role_grant_permissions( DRUPAL_AUTHENTICATED_RID, array( 'administer instapage_cms_plugin settings' ) );
}

function instapage_cms_plugin_uninstall()
{
  InstapageCmsPluginConnector::getSelectedConnector()->removePlugin();
}

function load_instapage_cms_plugin_dashboard()
{
  $js_dir = base_path() . drupal_get_path( 'module', InstapageCmsPluginConnector::getPluginDirectoryName() ) . '/core/assets/js'; 
  $knockout_dir = base_path() . drupal_get_path( 'module', InstapageCmsPluginConnector::getPluginDirectoryName() ) . '/core/knockout'; 
  $language_file = base_path() . drupal_get_path( 'module', InstapageCmsPluginConnector::getPluginDirectoryName() ) . '/core/assets/lang/' . InstapageCmsPluginConnector::getSelectedLanguage() . '.js'; 
  $options = array( 'scope' => 'footer', 'defer' => false, 'preprocess' => 'false' );
  drupal_add_js( 'var INSTAPAGE_AJAXURL = \'' . InstapageCmsPluginConnector::getAjaxURL() . '\';', array( 'type' => 'inline', 'scope' => 'header' ) );
  drupal_add_js( $language_file, $options );
  drupal_add_js( $js_dir . '/InstapageCmsPluginLang.js', $options );
  drupal_add_js( $knockout_dir . '/core/knockout-3.4.0.js', $options );
  drupal_add_js( $js_dir . '/knockout-no-conflict.js', $options );
  drupal_add_js( $knockout_dir . '/core/knockout.simpleGrid.3.0.js', $options );
  drupal_add_js( $js_dir . '/download.js', $options );
  drupal_add_js( $js_dir . '/InstapageCmsPluginAjax.js', $options );
  drupal_add_js( $knockout_dir . '/view_models/InstapageCmsPluginPagedGridModel.js', $options );
  drupal_add_js( $knockout_dir . '/view_models/InstapageCmsPluginEditModel.js', $options );
  drupal_add_js( $knockout_dir . '/view_models/InstapageCmsPluginSettingsModel.js', $options );
  drupal_add_js( $knockout_dir . '/view_models/InstapageCmsPluginMessagesModel.js', $options );
  drupal_add_js( $knockout_dir . '/view_models/InstapageCmsPluginToolbarModel.js', $options );
  drupal_add_js( $knockout_dir . '/view_models/InstapageCmsPluginMasterModel.js', $options );

  // UI KIT
  drupal_add_js( 'https://code.jquery.com/jquery-2.2.4.min.js', $options );
  drupal_add_js( $js_dir . '/mrwhite.js', $options );
  drupal_add_js( $js_dir . '/dropdowns.js', $options );
  drupal_add_js( $js_dir . '/expand-collapse.js', $options );
  drupal_add_js( $js_dir . '/input.js', $options );
  drupal_add_js( $js_dir . '/jq.hoverintent.js', $options );
  drupal_add_js( $js_dir . '/jquery.tmpl.min.js', $options );
  drupal_add_js( $js_dir . '/ripple.js', $options );
  drupal_add_js( $js_dir . '/select2.min.js', $options );
  drupal_add_js( $js_dir . '/snack-bars.js', $options );
  drupal_add_js( $js_dir . '/tabs.js', $options );

  $options = array( 'preprocess' => 'false' );
  $css_dir = drupal_get_path( 'module', InstapageCmsPluginConnector::getPluginDirectoryName() ) . '/core/assets/css';
  drupal_add_css( $css_dir . '/mrwhite-reset.css', $options );
  drupal_add_css( $css_dir . '/mrwhite-ui-kit.css', $options );
  drupal_add_css( $css_dir . '/general.css', $options );

  ob_start();
  InstapageCmsPluginConnector::getSelectedConnector()->loadPluginDashboard();
  $contents = ob_get_contents();
  ob_end_clean();

  return $contents;
}

class InstapageCmsPluginDrupal7Connector
{
  var $name = 'drupal';

  public function getPluginDirectoryName()
  {
    return 'instapage_cms_plugin';
  }
  
  public function getCMSName()
  {
    return 'Drupal';
  }

  public function currentUserCanManage()
  {
    return true;
  }

  private function prepareFunctionArgs( $args = array() )
  {
    if( isset( $args[ 0 ] ) && is_array( $args[ 0 ] ) )
    {
      $args = $args[ 0 ];
    }

    return $args;
  }

  private function prepare( $sql )
  {
    $sql = str_replace( array( '\'%s\'', '%s' ), '?', $sql );

    return $sql;
  }

  public function query( $sql )
  {
    $args = func_get_args();
    array_shift( $args );
    $args = $this->prepareFunctionArgs( $args );
    $sql = $this->prepare( $sql );
    
    try
    {
      db_query( $sql, $args );

      return true;
    }
    catch( Exception $e )
    {
      $this->logDbError( $e, $sql );

      return false;
    }
  }

  public function lastInsertId()
  {
    $sql = 'SELECT LAST_INSERT_ID() as last_insert_id';
    $result = $this->getRow( $sql );
    
    return isset( $result->last_insert_id ) ? $result->last_insert_id : false;
  }

  public function getRow( $sql )
  {
    $args = func_get_args();
    array_shift( $args );
    $args = $this->prepareFunctionArgs( $args );
    $sql = $this->prepare( $sql );

    try
    {
      $result = db_query( $sql, $args );

      return $result->fetchObject();
    }
    catch( Exception $e )
    {
      $this->logDbError( $e, $sql );

      return false;
    }
  }

  public function getResults( $sql )
  {
    $args = func_get_args();
    array_shift( $args );
    $args = $this->prepareFunctionArgs( $args );
    $sql = $this->prepare( $sql );
    
    try
    {
      $result = db_query( $sql, $args );
      $result_array = $result->fetchAll( PDO::FETCH_OBJ );

      if( !is_array( $result_array ) )
      {
        return array();
      }

      return $result_array;
    }
    catch( Exception $e )
    {
      $this->logDbError( $e, $sql );

      return false;
    }
  }
  
  public function getDBPrefix()
  {
    global $databases;

    $connection_key = Database::getConnection()->getKey();
    $settings = isset( $databases[ $connection_key ] ) ? $databases[ $connection_key ] : null;

    if( !$settings )
    {
      return null;
    }
    
    if( !isset( $settings[ 'prefix' ] ) && is_array( $settings ) )
    {
      $settings = array_pop( $settings );
    }

    if( isset( $settings[ 'prefix' ] ) && is_array( $settings[ 'prefix' ] ) )
    {
      $settings[ 'prefix' ] = array_pop( $settings[ 'prefix' ] );
    }

    return isset( $settings[ 'prefix' ] ) ? $settings[ 'prefix' ] : '';
  }

  public function getCharsetCollate()
  {
    global $databases;

    $connection_key = Database::getConnection()->getKey();
    $settings = isset( $databases[ $connection_key ] ) ? $databases[ $connection_key ] : null;

    if( !$settings )
    {
      return null;
    }
    
    if( !isset( $settings[ 'collation' ] ) && is_array( $settings ) )
    {
      $settings = array_pop( $settings );
    }

    if( isset( $settings[ 'collation' ] ) && is_array( $settings[ 'collation' ] ) )
    {
      $settings[ 'collation' ] = array_pop( $settings[ 'collation' ] );
    }

    $collation = isset( $settings[ 'collation' ] ) ? $settings[ 'collation' ] : 'utf8mb4_general_ci';

    return 'COLLATE ' . $collation;
  }

  public function remoteRequest( $url, $data, $headers = array(), $method = 'POST' )
  {
    $data_string = '';

    if( !isset( $headers[ 'Content-type' ] ) )
    {
      $headers[ 'Content-Type'] = 'application/x-www-form-urlencoded';
      InstapageCmsPluginHelper::writeDiagnostics( $data, 'Request (' . $method . ') data empty. Ping added.' );
    }

    if( $method == 'POST' && ( !is_array( $data ) || !count( $data ) ) )
    {
      $data = array( 'ping' => true );
    }

    $data_string = http_build_query( $data, '', '&' );

    if( $method == 'GET' )
    {
      $url .= '?' . urldecode( $data_string );
      $data_string = '';
    }

    $cookies = @InstapageCmsPluginHelper::getVar( $data[ 'cookies' ], array() );
    $cookie_string = '';

    foreach( $cookies as $key => $cookie )
    {
      $cookie_string .= $key . '=' . urlencode( $cookie ) . ';';
    }

    if( $cookie_string )
    {
      $headers[ 'Cookie' ] = $cookie_string;
    }

    $options = array(
      'headers' => $headers,
      'method' => $method,
      'data' => $data_string,
      'max_redirects' => 5,
      'timeout' => 45
    );

    $request = drupal_http_request( $url, $options );

    if( isset( $request->code ) && $request->code == 200 )
    {
      return $this->prepareResponse( $request );
    }
    else
    {
      return array( 'status' => 'ERROR', 'message' => InstapageCmsPluginConnector::lang( 'Request failed with status %s.', $request->code ) );
    }
  }

  public function remotePost( $url, $data, $headers = array() )
  {
    return $this->remoteRequest( $url, $data, $headers, 'POST' );
  }

  public function remoteGet( $url, $data, $headers = array() )
  {
    return $this->remoteRequest( $url, $data, $headers, 'GET' );
  }

  public function getSiteURL( $protocol = true )
  {
    $url = $_SERVER[ 'HTTP_HOST' ];

    if( $protocol )
    {
      if( isset( $_SERVER[ 'HTTPS' ] ) && !empty( $_SERVER[ 'HTTPS' ] ) )
      {
        $url = 'https://' . $url;
      }
      else
      {
        $url = 'http://' . $url;
      }
    }

    return $url;
  }

  public function getHomeURL( $protocol = true )
  {
    $url = $this->getSiteURL( $protocol );

    return $url;
  }

  public function getAjaxURL()
  {
    return $this->getSiteURL() . '/index.php?action=instapage_ajax_call';
  }

  public function lang()
  {
    $arguments = func_get_arg( 0 );
    
    if( !count( $arguments ) )
    {
      return null;
    }

    $text = $arguments[ 0 ];
    $variables = array_slice( $arguments, 1 );

    if( !count( $variables ) )
    {
      return $text;
    }

    return vsprintf( $text, $variables );
  }

  public function initPlugin()
  {
    $action = InstapageCmsPluginHelper::getVar( $_GET[ 'action' ], '' );
    
    if( $action == 'instapage_ajax_call' )
    {
      $this->ajaxCallback();
    }
    else
    {
      InstapageCmsPluginHelper::writeDiagnostics( $_SERVER[ 'REQUEST_URI' ], 'Instapage plugin initiated. REQUEST_URI' );
      $this->checkProxy();
      $this->checkHomepage();
      $this->checkCustomUrl();
    }
  }

  public function removePlugin()
  {
    $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
    $db = InstapageCmsPluginDBModel::getInstance();
    $subaccount->disconnectAccountBoundSubaccounts( true );
    $db->removePluginTables();
  }
  
  public function loadPluginDashboard()
  {
    InstapageCmsPluginHelper::loadTemplate( 'messages' );
    InstapageCmsPluginHelper::loadTemplate( 'toolbar' );
    InstapageCmsPluginHelper::loadTemplate( 'base' );
  }

  public function ajaxCallback()
  {
    InstapageCmsPluginConnector::ajaxCallback();
  }

  public function isLoginPage()
  {
    $request_url = $_SERVER[ 'REQUEST_URI' ];
    
    if( strpos( $request_url, '/user' ) === 0 || $_GET[ 'q' ] == 'user' )
    {
      return true;
    }
    
    return false;
  }

  public function isAdmin()
  {
    $request_url = $_SERVER[ 'REQUEST_URI' ];
    
    if( strpos( $request_url, '/admin' ) === 0 || $_GET[ 'q' ] == 'admin' )
    {
      return true;
    }

    return false;
  }

  
  public function checkPage( $type, $slug = '' )
  {
    $page = InstapageCmsPluginPageModel::getInstance();
    $result = $page->check( $type, $slug );

    if( !$result )
    {
      return;
    }
    
    if( $type == '404' )
    {
      $page->display( $result, 404 );
    }
    else
    {
      $page->display( $result );
    }
  }

  public function checkHomepage()
  {
    $home_url = str_replace( array( 'http://', 'https://' ), '', rtrim( $this->getHomeURL(), '/' ) );
    $home_url_segments = explode( '/', $home_url );
    $uri_segments = explode( '?', $_SERVER[ 'REQUEST_URI' ] );
    $uri_segments = explode( '/', rtrim( $uri_segments[ 0 ], '/' ) );

    if(
      ( count( $uri_segments ) !== count( $home_url_segments ) ) || 
      ( count( $home_url_segments ) > 1 && $home_url_segments[ 1 ] != $uri_segments[ 1 ] )
    )
    {
      return false;
    }

    $this->checkPage( 'home' );
    
    return true;
  }

  public function check404()
  {
    $this->checkPage( '404' );  
  }

  public function checkCustomUrl()
  {
    $slug = InstapageCmsPluginHelper::extractSlug( $this->getHomeURL() );
    
    if( $slug )
    {
      $this->checkPage( 'page', $slug );
    }

    return true;
  }

  public function checkProxy()
  {
    $services = InstapageCmsPluginServicesModel::getInstance();

    if( $services->isServicesRequest() )
    {
      try
      {
        $services->processProxyServices();

        return;
      }
      catch( Exception $e )
      {
        echo $e->getMessage();
      }
    }
  }

  public function getProhibitedSlugs()
  {
    $result = array_merge( $this->getPostSlugs(), InstapageCmsPluginConnector::getLandingPageSlugs() );

    return $result;
  }

  public function getOptionsDebugHTML()
  {
    return '';
  }

  public function getPluginsDebugHTML()
  {
    return '';
  }

  public function getSitename( $sanitized = false )
  {
    $sitename = variable_get( 'site_name' );

    if( $sanitized )
    {
      return mb_strtolower( str_replace( ' ', '-', $sitename ) );
    }

    return $sitename;
  }

  public function mail( $to, $subject, $message, $headers = '', $attachments = array() )
  {
    global $language_object;
    $module = 'instapage_cms_plugin';
    $key = 'custom_email';
    $params[ 'message' ] = $message;
    $params[ 'subject' ] = $subject;
    $langcode = $language_object->language;
    $send = true;

    return drupal_mail( $module, $key, $to, $langcode, $params, NULL, $send );
  }

  public function getDeprecatedData()
  {
    $pages = variable_get( 'instapage_pages', NULL );
    $results = array();

    foreach( $pages as $key => $slug )
    {
      $page_obj = new stdClass;
      $page_obj->id = 0;
      $page_obj->landingPageId = $key;
      $page_obj->slug = $slug;
      $page_obj->type = 'page';
      $page_obj->enterprise_url = $page_obj->slug ? InstapageCmsPluginConnector::getHomeURL() . '/' . $page_obj->slug : Connector::getHomeURL();
      $results[] = $page_obj;
    }

    return $results;
  }

  public function escapeHTML( $html )
  {
    return htmlspecialchars( $html );
  }

  public function isHtmlReplaceNecessary()
  {
    if( $this->isAdmin() || $this->isLoginPage() || InstapageCmsPluginHelper::isCustomParamPresent() )
    {
      InstapageCmsPluginHelper::writeDiagnostics( 'is_admin || isLoginPage || isCustomParamPresent', 'HTML Replace is not necessary' );

      return false;
    }

    return true;
  }

  public function getSettingsModule()
  {
    return '';
  }

  private function logDbError( $e, $sql )
  {
    $db = InstapageCmsPluginDBModel::getInstance();
    $error_message = $e->getMessage();

    if( strpos( $sql, $db->debugTable ) === false && $error_message !== '' )
    {
      $messages = array(
        'Query: ' . $sql,
        'Error: ' . $error_message
      );

      InstapageCmsPluginHelper::writeDiagnostics( implode( "\n", $messages ), 'DB Error' );
    }
  }

  private function getPostSlugs()
  {
    $edit_url = $this->getSiteURL();
    $db_prefix = $this->getDBPrefix();
    $sql = 'SELECT pid AS id, SUBSTRING( alias, 2 ) AS slug, CONCAT(\'' . $edit_url . '\', source, \'/edit\') AS editUrl FROM ' . $db_prefix . 'url_alias';
    $results = $this->getResults( $sql );

    return $results;
  }

  private function prepareResponse( $request )
  {
    return array(
      'body' => (string) isset( $request->data ) ? $request->data : '',
      'status' => (string) isset( $request->status_message ) ? $request->status_message : '',
      'code' => isset( $request->code ) ? $request->code : '0',
      'headers' => isset( $request->headers ) ? $request->headers : ''
    );
  }
}
