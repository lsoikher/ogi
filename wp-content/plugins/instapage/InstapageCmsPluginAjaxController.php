<?php
class InstapageCmsPluginAjaxController
{
  private static $ajaxController = null;

  public static function getInstance()
  {
    if( self::$ajaxController === null )
    {
      self::$ajaxController = new InstapageCmsPluginAjaxController();
    }

    return self::$ajaxController;
  }

  public function doAction( $action, $data = null )
  {
    InstapageCmsPluginHelper::writeDiagnostics( $action, 'AJAX Action');
    
    if( !InstapageCmsPluginConnector::currentUserCanManage() )
    {
      echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'You don\'t have permission to perform that action.' ), 'ERROR' );
      exit;
    }

    switch( $action )
    {
      case 'loginUser':
        $this->loginUser();
      break;
      
      case 'getApiTokens':
        $this->getApiTokens();
      break;

      case 'connectSubAccounts':
        $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
        $subaccount->setSubAccountsStatus( 'connect' );
      break;

      case 'disconnectSubAccounts':
        $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
        $subaccount->setSubAccountsStatus( 'disconnect' );
      break;

      case 'disconnectAccountBoundSubaccounts':
        $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
        $subaccount->disconnectAccountBoundSubaccounts();
      break;

      case 'getAccountBoundSubAccounts':
        $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
        $subaccount->getAccountBoundSubAccounts();
      break;

      case 'updateOptions':
        if( InstapageCmsPluginHelper::updateOptions( $data ) !== false )
        {
          echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'Configuration updated' ), 'OK' );  
        }
        else
        {
          echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'There was an error during configuration save' ), 'ERROR' );   
        }
      break;
      
      case 'getOptions':
        echo json_encode( InstapageCmsPluginHelper::getOptions() );
      break;
      
      case 'getLog':
        $this->getLog();
      break;

      case 'clearLog':
        $log = InstapageCmsPluginDebugLogModel::getInstance();
        $log->clear();
        echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'Log cleared' ), 'OK' );
      break;

      case 'getMasterToken':
        $this->getMasterToken();
      break;

      case 'loadListPages':
        $this->loadListPages();
      break;

      case 'loadEditPage':
        $this->loadEditPage();
      break;

      case 'getLandingPages':
        $this->getLandingPages();
      break;

      case 'getStats':
        $this->getStats();
      break;

      case 'publishPage':
        $this->publishPage();
      break;

      case 'deletePage':
        $this->deletePage();
      break;

      case 'loadSettings':
        echo json_encode( (object) array( 
          'status' => 'OK', 
          'html' => InstapageCmsPluginHelper::loadTemplate( 'settings', false ),
          'initialData' => InstapageCmsPluginHelper::getOptions()
        ) );
      break;

      case 'getProhibitedSlugs':
        $data = InstapageCmsPluginConnector::getSelectedConnector()->getProhibitedSlugs();
        echo json_encode( (object) array( 
          'status' => 'OK', 
          'data' => $data
        ) );
      break;

      case 'validateToken':
        $this->validateToken();
      break;

      case 'migrateDeprecatedData':
        $data = InstapageCmsPluginConnector::getSelectedConnector()->getDeprecatedData();
        $page = InstapageCmsPluginPageModel::getInstance();
        $raport = $page->migrateDeprecatedData( $data );
        $raport_str = implode( '<br />', $raport );
        echo InstapageCmsPluginHelper::formatJsonMessage( $raport_str );
      break;

      default:
        echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'Unsupported InstapageCmsPluginAjaxController action' ), 'ERROR' );
    }
  }

  private function loginUser()
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $post = InstapageCmsPluginHelper::getPostData();
    $email = InstapageCmsPluginHelper::getVar( $post->data->email, '' );
    $password = InstapageCmsPluginHelper::getVar( $post->data->password, '' );
    $response = json_decode( $api->authorise( $email, $password ) );

    if( !InstapageCmsPluginHelper::checkResponse( $response, null, false ) || !$response->success )
    {
      $message = InstapageCmsPluginHelper::getVar( $response->message, '' );
      echo InstapageCmsPluginHelper::formatJsonMessage( $message, 'ERROR' );
      return false;
    }
    else
    {
      echo json_encode( (object) array(
        'status' => 'OK',
        'data' => (object) $response->data
      ) );
    }
  }

  private function validateToken()
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $post = InstapageCmsPluginHelper::getPostData();
    $token = InstapageCmsPluginHelper::getVar($post->data->token, null);
    $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( array( $token ) ) );
    $response = json_decode( $api->apiCall( 'page/get-sub-accounts-list', null, $headers ) );
    $sub_account = @InstapageCmsPluginHelper::getVar( $response->data, null );
    
    if( !InstapageCmsPluginHelper::checkResponse( $response, null, false) || !$response->success || count( $sub_account ) == 0 )
    {
      echo json_encode( (object) array( 
        'status' => 'OK', 
        'valid' => false
      ) );
    }
    else
    {
      echo json_encode( (object) array( 
        'status' => 'OK', 
        'valid' => true
      ) );
    }
  }

  private function getLog()
  {
    $log = InstapageCmsPluginDebugLogModel::getInstance();
    $sitename_sanitized = InstapageCmsPluginConnector::getSitename( true ) ;
    try
    { 
      $data = $log->getLogHTML();
      echo json_encode( (object) array( 
        'status' => 'OK', 
        'data' => $data,
        'sitename' => $sitename_sanitized
      ) );
    }
    catch( Exception $e )
    {
      echo InstapageCmsPluginHelper::formatJsonMessage( $e->getMessage(), 'ERROR' );
    }
  }

  private function getApiTokens()
  {
    $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
    $tokens = $subaccount->getAllTokens();
    echo json_encode( (object) array(  
      'status' => 'OK', 
      'data' => $tokens
    ) );
  }

  private function loadEditPage()
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
    $post = InstapageCmsPluginHelper::getPostData();
    InstapageCmsPluginHelper::writeDiagnostics( $post, 'Edit page POST');
    $tokens = InstapageCmsPluginHelper::getVar( $post->apiTokens, false );

    if( !$tokens )
    {
      $tokens = $subaccount->getAllTokens();
    }

    $page_data = null;
    $sub_accounts = null;
    $data = array();

    if( isset( $post->data->id ) )
    {
      $page_data = $post->data;
      $data[ 'pages' ] = array( $post->data->instapage_id );

    }
    
    $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( $tokens ) );
    $response = json_decode( $api->apiCall( 'page/get-sub-accounts-list', $data, $headers ) );

    if( InstapageCmsPluginHelper::checkResponse( $response ) )
    {
      $sub_accounts = $response->data;
    }
    else
    {
      return false;
    }

    $initialData = array( 'subAccounts' => $sub_accounts, 'page' => $page_data );
    InstapageCmsPluginHelper::writeDiagnostics( $initialData, 'Edit page initialData');

    echo json_encode( (object) array( 
      'status' => 'OK', 
      'html' => InstapageCmsPluginHelper::loadTemplate( 'edit', false ),
      'data' => (object) $initialData
    ) );
  }

  private function loadListPages()
  {
    $request_limit = 300;
    $post = InstapageCmsPluginHelper::getPostData();
    $page = InstapageCmsPluginPageModel::getInstance();
    InstapageCmsPluginHelper::writeDiagnostics( $post, 'List page POST');    
    $api = InstapageCmsPluginAPIModel::getInstance();
    $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
    $local_pages_array = $page->getAll( array( 'id', 'instapage_id', 'slug', 'type', 'stats_cache', 'enterprise_url' ) );
    
    //WP Legacy code - automatic migration
    $automatic_migration = InstapageCmsPluginHelper::getMetadata( 'automatic_migration', false );

    if( empty( $automatic_migration ) && !count( $local_pages_array ) && InstapageCmsPluginConnector::isWP() && InstapageCmsPluginConnector::getSelectedConnector()->legacyArePagesPresent() )
    {
      $data = InstapageCmsPluginConnector::getSelectedConnector()->getDeprecatedData();
      $page = InstapageCmsPluginPageModel::getInstance();
      $page->migrateDeprecatedData( $data );
      $local_pages_array = $page->getAll( array( 'id', 'instapage_id', 'slug', 'type', 'stats_cache', 'enterprise_url' ) );
      InstapageCmsPluginHelper::updateMetadata( 'automatic_migration', time() );
    }
   
    $pages = array();
    
    foreach( $local_pages_array as &$page_object )
    {
      $page_object->stats_cache = json_decode($page_object->stats_cache);
      $pages[] = $page_object->instapage_id;
    }

    $tokens = InstapageCmsPluginHelper::getVar( $post->apiTokens, false );

    if( !$tokens )
    {
      $tokens = $subaccount->getAllTokens();
    }

    if( !count( $tokens ) )
    {
      echo json_encode( (object) array( 
        'status' => 'OK', 
        'html' => InstapageCmsPluginHelper::loadTemplate( 'listing', false ),
        'initialData' => $local_pages_array
      ) );

      return true;
    }


    $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( $tokens ) );
    $responses = array();
    $success = true;

    for( $i = 0; $i * $request_limit < count( $pages ); ++$i )
    {
      $data_slice = array_slice( $pages, $i * $request_limit, $request_limit );
      $data = array( 'pages' => $data_slice );
      $response_json = $api->apiCall( 'page/list', $data, $headers, 'GET' );
      $response = json_decode( $response_json );

      if( InstapageCmsPluginHelper::checkResponse( $response ) && isset( $response->data ) && is_array( $response->data ) )
      {
        $responses[] = $response->data;
      }
      else
      {
        $responses[] = array();
      }
    }

    $merged_response = array();

    foreach( $responses as $r )
    {
      $merged_response = array_merge( $merged_response, $r );
    }
    
    $page->mergeListPagesResults( $local_pages_array, $merged_response );
    InstapageCmsPluginHelper::writeDiagnostics( $local_pages_array, 'List page array');
    echo json_encode( (object) array( 
      'status' => 'OK', 
      'html' => InstapageCmsPluginHelper::loadTemplate( 'listing', false ),
      'initialData' => $local_pages_array
    ) );
  }

  private function getLandingPages()
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $post = InstapageCmsPluginHelper::getPostData();
    $tokens = array( $post->data->subAccountToken );
    $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( $tokens ) );
    $response_json = $api->apiCall( 'page/list', null, $headers );
    $response =json_decode( $response_json );
    $page = InstapageCmsPluginPageModel::getInstance();
    $published_pages = $page->getAll( array( 'instapage_id' ) );
    $self_instapage_id = @InstapageCmsPluginHelper::getVar( $post->data->selfInstapageId, null );

    if( InstapageCmsPluginHelper::checkResponse( $response ) )
    {
      if( is_array( $response->data ) )
      {
        foreach( $response->data as $key => $returned_page )
        {
          foreach( $published_pages as $published_page )
          {
            if( $returned_page->id != $self_instapage_id && $returned_page->id == $published_page->instapage_id )
            {
              unset( $response->data[ $key ] );
              break;
            }
          }
        }

        $response->data = array_values( $response->data );
      }
      else
      {
        $response->data = array();
      }

      echo json_encode( (object) array( 
        'status' => 'OK', 
        'data' => $response->data
      ) );
    }
    else
    {
      return false;
    }
  }

  private function getStats()
  {
    $post = InstapageCmsPluginHelper::getPostData();
    $page = InstapageCmsPluginPageModel::getInstance();
    $api = InstapageCmsPluginAPIModel::getInstance();
    $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
    $pages = InstapageCmsPluginHelper::getVar( $post->data->pages, array() );

    if( !count( $pages ) )
    {
      InstapageCmsPluginHelper::writeDiagnostics( 'Stats cond', 'No pages in request' );
      echo json_encode( (object) array( 
        'status' => 'OK', 
        'data' => array()
      ) );

      return true;
    }

    $cached_stats = $page->getPageStatsCache( $pages );
    InstapageCmsPluginHelper::writeDiagnostics( $cached_stats, 'Cached stats');
    $pages_without_stats = array();

    foreach( $pages as $instapage_id )
    {
      if( !isset( $cached_stats[ $instapage_id ] ) )
      {
        $pages_without_stats[] = $instapage_id;
      }
    }

    if( empty( $pages_without_stats ) )
    {
      echo json_encode( (object) array( 
        'status' => 'OK', 
        'data' => $cached_stats
      ) );

      return true;
    }

    $tokens = InstapageCmsPluginHelper::getVar( $post->apiTokens, false );

    if( !$tokens )
    {
      $tokens = $subaccount->getAllTokens();
    }

    $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( $tokens ) );
    $data = array( 'pages' => $pages_without_stats );
    $response_json = $api->apiCall( 'page/stats', $data, $headers );
    $response =json_decode( $response_json );

    if( InstapageCmsPluginHelper::checkResponse( $response ) )
    {
      $stats = (array) InstapageCmsPluginHelper::getVar( $response->data, array() );
      $page->savePageStatsCache( $stats );
      
      if( count( $stats ) )
      {
        $stats = array_merge( $cached_stats, $stats );
      }
      else
      {
        $stats = $cached_stats;
      }

      echo json_encode( (object) array( 
        'status' => 'OK', 
        'data' => $stats
      ) );
    }
    else
    {
      return false;
    }
  }

  private function publishPage()
  {
    $page = InstapageCmsPluginPageModel::getInstance();
    $post = InstapageCmsPluginHelper::getPostData();
    $data = $post->data;

    echo $page->publishPage( $data );
  }

  private function deletePage()
  {
    $page = InstapageCmsPluginPageModel::getInstance();
    $api = InstapageCmsPluginAPIModel::getInstance();
    $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
    $post = InstapageCmsPluginHelper::getPostData();
    $result = $page->get( $post->data->id, array( 'instapage_id' ) );
    $instapage_id = $result->instapage_id;
    $tokens = InstapageCmsPluginHelper::getVar( $post->apiTokens, false );

    if( !$tokens )
    {
      $tokens = $subaccount->getAllTokens();
    }

    $data = array(
      'page' => $instapage_id,
      'url' => '',
      'publish' => 0
    );
    $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( $tokens ) );
    $response = json_decode( $api->apiCall( 'page/edit', $data, $headers ) );

    $message = '';

    if( !InstapageCmsPluginHelper::checkResponse( $response, null, false) || !$response->success )
    {
      $message .= InstapageCmsPluginConnector::lang( 'Page that you are removing (Instapage ID: %s) doesn\'t exist in your Instapage application\'s dashboard. It could have been deleted from app or created by another user. Deleting this page won\'t affect Instapage application\'s dashboard.', $instapage_id );

      if( isset( $response->message ) && $response->message !== '' )
      {
        $message .= InstapageCmsPluginConnector::lang( ' Instapage app response: ' . $response->message );
      }
    }

    if( isset( $post->data->id ) && $page->delete( $post->data->id ) )
    {
      if( $message )
      {
        echo InstapageCmsPluginHelper::formatJsonMessage( $message );
      }
      else
      {
        echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'Page deleted successfully.' ) );
      }
      
      return true;
    }
    else
    {
      echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'There was a database error during page delete process.' ), 'ERROR' );
      
      return false;
    }
  }
}
