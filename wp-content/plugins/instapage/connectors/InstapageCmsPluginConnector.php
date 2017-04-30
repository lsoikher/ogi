<?php
class InstapageCmsPluginConnector
{

  private static $selectedConnector = null;
  private static $selectedLanguage = 'en-GB';
  private static $languageArray = null;

  public static function getSelectedLanguage()
  {
    return self::$selectedLanguage;
  }

  public static function isWP()
  {
    if( defined( 'ABSPATH' ) && defined( 'WPINC' ) && defined( 'WP_CONTENT_DIR' ) )
    {
      return true;  
    }
    
    return false;
  }

  public static function isDrupal8()
  {
    if( class_exists( '\Drupal\Core\DrupalKernel' ) )
    {
      return true;  
    }
    
    return false;
  }

  public static function isDrupal7()
  {
    if( defined( 'VERSION' ) && VERSION > 7.0 && VERSION < 8.0 )
    {
      return true;  
    }
    
    return false;
  }

  public static function getSelectedConnector()
  {
    if( self::$selectedConnector === null )
    {
      switch( true )
      {
        case self::isWP():
          require_once( INSTAPAGE_PLUGIN_PATH . '/connectors/InstapageCmsPluginWPConnector.php' );
          self::$selectedConnector = new InstapageCmsPluginWPConnector();
        break;

        case self::isDrupal7():
          require_once( INSTAPAGE_PLUGIN_PATH . '/connectors/InstapageCmsPluginDrupal7Connector.php' );
          self::$selectedConnector = new InstapageCmsPluginDrupal7Connector();
        break;

        case self::isDrupal8():
          require_once( INSTAPAGE_PLUGIN_PATH . '/connectors/InstapageCmsPluginDrupal8Connector.php' );
          self::$selectedConnector = new InstapageCmsPluginDrupal8Connector();
        break;

        default:
          //TODO - add system message
          die( 'Unsupported CMS');
      }
    }

    return self::$selectedConnector;
  }

  public static function getPluginDirectoryName()
  {
    return self::getSelectedConnector()->getPluginDirectoryName();
  }

  public static function currentUserCanManage()
  {
    return self::getSelectedConnector()->currentUserCanManage();
  }

  public static function lang()
  {
    return self::getSelectedConnector()->lang( func_get_args() );
  }

  public static function getSitename( $sanitized = false )
  {
    return self::getSelectedConnector()->getSitename( $sanitized );
  }

  public static function getSiteURL( $protocol = true )
  {
    return self::getSelectedConnector()->getSiteURL( $protocol );
  }

  public static function getHomeURL( $protocol = true )
  {
    return self::getSelectedConnector()->getHomeURL( $protocol );
  }

  public static function getCMSName()
  {
    return self::getSelectedConnector()->getCMSName();
  }

  public static function mail( $to, $subject, $message, $headers = '', $attachments = array() )
  {
    return self::getSelectedConnector()->mail( $to, $subject, $message, $headers, $attachments );
  }

  public static function getAjaxURL()
  {
    return self::getSelectedConnector()->getAjaxURL();
  }

  public static function addAdminJS( $handle, $file, $in_footer = false )
  {
    return self::getSelectedConnector()->addAdminJSaddAdminJS( $handle, $file, $in_footer );
  }

  public static function addAdminCSS( $handle, $file )
  {
    return self::getSelectedConnector()->addAdminCSS( $handle, $file );
  }

  public static function escapeHTML( $html )
  {
    return self::getSelectedConnector()->escapeHTML( $html );
  }

  public static function checkPage( $type, $slug = '' )
  {
    return self::getSelectedConnector()->checkPage( $type, $slug );
  }

  public static function isHtmlReplaceNecessary()
  {
    return self::getSelectedConnector()->isHtmlReplaceNecessary();
  }

  public static function initPlugin()
  {
    $db = InstapageCmsPluginDBModel::getInstance();
    $db->initPluginTables();
    self::getSelectedConnector()->initPlugin();
  }

  public static function removePlugin()
  {
    return self::getSelectedConnector()->removePlugin();
  }

  public static function getLandingPageSlugs()
  {
    $db = InstapageCmsPluginDBModel::getInstance();
    $sql = 'SELECT id, slug, \'\' AS editUrl FROM ' . $db->pagesTable . ' WHERE type = \'page\' AND slug <> \'\'';
    $results = $db->getResults( $sql );

    return $results;
  }
  
  public static function ajaxCallback()
  {
    ini_set( 'display_errors',0 );
    header( 'Content-Type: application/json' );
    $post = isset( $_POST[ 'data' ] ) ? json_decode( urldecode( $_POST[ 'data' ] ) ) : array();
    $post->data = isset( $post->data ) ? $post->data : null;
    
    if( !empty( $post->action ) )
    {
      InstapageCmsPluginAjaxController::getInstance()->doAction( $post->action, $post->data );
    }

    die();
  }

  public static function getSettingsModule()
  {
    return self::getSelectedConnector()->getSettingsModule();
  }
}
