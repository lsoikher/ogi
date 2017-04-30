<?php
class InstapageCmsPluginSubaccountModel
{
  private static $subaccountModel = null;
  private $subaccount_tokens = null;
  
  public static function getInstance()
  {
    if( self::$subaccountModel === null )
    {
      self::$subaccountModel = new InstapageCmsPluginSubaccountModel();
    }

    return self::$subaccountModel;
  }

  public function getAllTokens()
  {
    if( $this->subaccount_tokens === null )
    {
      $tokens = InstapageCmsPluginHelper::getTokens();
      $account_keys = $this->getAccountBoundTokens();
      $this->subaccount_tokens = array_merge( $tokens, $account_keys );
    }

    return $this->subaccount_tokens;
  }

  public function getAccountBoundTokens()
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $user_token = InstapageCmsPluginHelper::getOption( 'plugin_hash' );
    $account_keys = array();

    if( $user_token )
    {
      $headers = array( 'usertoken' => $user_token );
      $response_json = $api->apiCall( 'page/get-account-keys', null, $headers );
      $response = json_decode( $response_json );

      if( !is_null( $response ) && $response->success )
      {
        $account_keys = $response->data->accountkeys;
      }
    }

    return $account_keys;
  }

  public function getAccountBoundSubAccounts( $format = 'json' )
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $tokens = $this->getAccountBoundTokens();
    $sub_accounts = array();
    
    if( is_array( $tokens ) && count( $tokens ) )
    {
      $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( $tokens ) );
      $response = json_decode( $api->apiCall( 'page/get-sub-accounts-list', null, $headers ) );
      $sub_accounts = @InstapageCmsPluginHelper::getVar( $response->data, null );  
    }
    
    if( $format == 'json' )
    {
      echo json_encode( (object) array( 
        'status' => 'OK', 
        'data' => $sub_accounts
      ) );
    }
    else
    {
      return $sub_accounts;
    }
  }

  public function setSubAccountsStatus( $status = 'connect', $tokens = null, $silent = false )
  {
    $api = InstapageCmsPluginAPIModel::getInstance();
    $subaccount = InstapageCmsPluginSubaccountModel::getInstance();
    $post = InstapageCmsPluginHelper::getPostData();
    
    if( $tokens !== null )
    {
      $selected_subaccounts = $tokens;
    }
    else
    {
      $selected_subaccounts = InstapageCmsPluginHelper::getVar( $post->data->tokens, array() );  
    }
    
    
    if( count( $selected_subaccounts ) )
    {
      $tokens = $subaccount->getAllTokens();
      $headers = array( 'accountkeys' => InstapageCmsPluginHelper::getAuthHeader( $tokens ) );
      $data = array( 
        'accountkeys' => base64_encode( json_encode( $selected_subaccounts ) ),
        'status' => $status,
        'domain' => InstapageCmsPluginConnector::getHomeURL( false )
      );

      $response = json_decode( $api->apiCall( 'page/connection-status', $data, $headers ) );

      if( $silent )
      {
        return;
      }

      if( 
        !InstapageCmsPluginHelper::checkResponse( $response, null, false ) || 
        !$response->success || 
        !isset( $response->data->changed ) || 
        $response->data->changed != count( $selected_subaccounts )
      )
      {
        $actions = array();
        $action[ 0 ] = $status == 'connect' ? 'connected to' : 'disconnected from';
        $action[ 1 ] = $status == 'connect' ? 'connect' : 'disconnect';
        
        if( count( $selected_subaccounts ) > 1 )
        {
          $message = InstapageCmsPluginHelper::getVar( $response->message, InstapageCmsPluginConnector::lang( 'There was an error, selected subaccounts are not properly %s app. Try to %s subaccounts again.', $artion[ 0 ], $action[ 1 ] ) );
        }
        else
        {
          $message = InstapageCmsPluginHelper::getVar( $response->message, InstapageCmsPluginConnector::lang( 'There was an error, selected subaccount is not properly %s app. Try to %s subaccounts again.', $artion[ 0 ], $action[ 1 ] ) );
        }

        echo InstapageCmsPluginHelper::formatJsonMessage( $message, 'ERROR' );
      }
      else
      {
        $action = array();
        $action[ 0 ] = $status == 'connect' ? 'Selected subaccounts' : 'Subaccounts bound to your account';
        $action[ 1 ] = $status == 'connect' ? 'connected' : 'disconnected';

        if( count( $selected_subaccounts ) > 1 )
        {
          $message = InstapageCmsPluginHelper::getVar( $response->message, InstapageCmsPluginConnector::lang( '%s are %s.', $action[ 0 ], $action[ 1 ] ) );
        }
        else
        {
          $message = InstapageCmsPluginHelper::getVar( $response->message, InstapageCmsPluginConnector::lang( 'Selected subaccount is %s.', $action[ 1 ]) );
        }
        
        echo InstapageCmsPluginHelper::formatJsonMessage( $message );
      }
    }
    else
    {
      echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'No subaccounts were connected.' ) );
    }
  }

  public function disconnectAccountBoundSubaccounts( $silent = false )
  {
    $sub_accounts = $this->getAccountBoundSubAccounts( 'array' );

    if( count( $sub_accounts ) )
    {
      $tokens = array();  

      foreach( $sub_accounts as $item )
      {
        $tokens[] = InstapageCmsPluginHelper::getVar( $item->accountkey, '' );
      }

      $this->setSubAccountsStatus( 'disconnect', $tokens, $silent );
    }
    else
    {
      if( !$silent )
      {
        echo InstapageCmsPluginHelper::formatJsonMessage( InstapageCmsPluginConnector::lang( 'Subaccounts bound to your account are dissconnected' ) );
      }
    }
  }
}
