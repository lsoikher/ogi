<?php
class DBModel
{
  private static $dbModel = null;
  public $prefix = null;
  public $charsetCollate = null;
  public $optionsTable = null;
  public $pagesTable = null;
  public $debugTable = null;
  
  function __construct()
  {
    $this->prefix = Connector::getSelectedConnector()->getDBPrefix();
    $this->charsetCollate = Connector::getSelectedConnector()->getCharsetCollate();
    $this->optionsTable = $this->prefix . 'instapage_options';
    $this->pagesTable = $this->prefix . 'instapage_pages';
    $this->debugTable = $this->prefix . 'instapage_debug';
  }

  public static function getInstance()
  {
    if( self::$dbModel === null )
    {
      self::$dbModel = new DBModel();
    }

    return self::$dbModel;
  }

  public function query( $sql )
  {
    $args = func_get_args();
    array_shift( $args );
    
    if( isset( $args[ 0 ] ) && is_array( $args[ 0 ] )  )
    {
      $args = $args[ 0 ];
    }

    return Connector::getSelectedConnector()->query( $sql, $args );
  }

  public function lastInsertId()
  {
    return Connector::getSelectedConnector()->lastInsertId();
  }

  public function getRow( $sql )
  {
    $args = func_get_args();
    array_shift( $args );
    
    if( isset( $args[ 0 ] ) && is_array( $args[ 0 ] )  )
    {
      $args = $args[ 0 ];
    }

    return Connector::getSelectedConnector()->getRow( $sql, $args ); 
  }

  public static function getResults( $sql )
  {
    $args = func_get_args();
    array_shift( $args );
    
    if( isset( $args[ 0 ] ) && is_array( $args[ 0 ] )  )
    {
      $args = $args[ 0 ];
    }

    return Connector::getSelectedConnector()->getResults( $sql, $args ); 
  }

  public function initPluginTables()
  {
    $this->initOptionsTable();
    $this->initPagesTable();
    $this->initDebugTable();
    $this->updateDB();
  }

  private function initOptionsTable()
  {
    $sql = sprintf( 'CREATE TABLE IF NOT EXISTS %s(' . 
    'id MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT, ' . 
    'plugin_hash VARCHAR(255) DEFAULT \'\', ' . 
    'api_keys TEXT NULL, ' .
    'user_name VARCHAR(255) DEFAULT \'\', ' . 
    'config TEXT NULL, ' . 
    'metadata TEXT NULL, ' . 
    'UNIQUE KEY id (id)) %s', $this->optionsTable, $this->charsetCollate );

    $this->query( $sql );
  }

  private function initPagesTable()
  {
    $sql = sprintf( 'CREATE TABLE IF NOT EXISTS %s(' .
    'id MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT, ' . 
    'instapage_id INT UNSIGNED NOT NULL, ' . 
    'slug VARCHAR(255) DEFAULT \'\' NOT NULL, ' .
    'type VARCHAR(4) DEFAULT \'page\' NOT NULL, ' .
    'time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, ' .
    'stats_cache TEXT NULL, ' . 
    'stats_cache_expires INT UNSIGNED, ' . 
    'UNIQUE KEY id (id)) %s', $this->pagesTable, $this->charsetCollate  );

    $this->query( $sql );
  }

  private function initDebugTable()
  {
    $sql = sprintf( 'CREATE TABLE IF NOT EXISTS %s(' .
    'id MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT, ' . 
    'time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, ' .
    'text TEXT NULL, ' . 
    'caller VARCHAR(255) DEFAULT \'\' NOT NULL, ' . 
    'name VARCHAR(255) DEFAULT \'\' NOT NULL, ' .
    'UNIQUE KEY id (id)) %s', $this->debugTable, $this->charsetCollate  );

    $this->query( $sql );
  }

  private function updateDB()
  {
    $db_version = intval( InstapageHelper::getMetadata( 'db_version', 0 ), 10 );
    
    if( $db_version < 300000010 )
    {
      InstapageHelper::writeDiagnostics( $db_version, 'Current db version. Doing update.');
      $sql = 'SHOW COLUMNS FROM ' . $this->optionsTable . ' LIKE %s';
      $metadata_exists = $this->getRow( $sql, 'metadata' );
      $sql = 'SHOW COLUMNS FROM ' . $this->pagesTable . ' LIKE %s';
      $enterprise_url_exists = $this->getRow( $sql, 'enterprise_url' );
      
      if( !$metadata_exists )
      {
        $sql = sprintf( 'ALTER TABLE %s ADD metadata TEXT NULL', $this->optionsTable );
        $this->query( $sql );  
      }

      if( !$enterprise_url_exists )
      {
        $sql = sprintf( 'ALTER TABLE %s ADD enterprise_url VARCHAR(255) DEFAULT \'\' NOT NULL', $this->pagesTable );
        $this->query( $sql );  
      }
      
      $sql = sprintf( 'ALTER TABLE %s MODIFY api_keys TEXT NULL', $this->optionsTable );
      $this->query( $sql );
      $sql = sprintf( 'ALTER TABLE %s MODIFY config TEXT NULL', $this->optionsTable );
      $this->query( $sql );
      $sql = sprintf( 'ALTER TABLE %s MODIFY slug VARCHAR(255) DEFAULT \'\' NOT NULL', $this->pagesTable );
      $this->query( $sql );
      $sql = sprintf( 'ALTER TABLE %s MODIFY time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL', $this->pagesTable );
      $this->query( $sql );
      $sql = sprintf( 'ALTER TABLE %s MODIFY time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL', $this->debugTable );
      $this->query( $sql );

      InstapageHelper::updateMetadata( 'db_version', 300000010 );
    }
  }

  public function removePluginTables()
  {
    $this->query( 'DROP TABLE IF EXISTS ' . $this->optionsTable );
    $this->query( 'DROP TABLE IF EXISTS ' . $this->pagesTable );
    $this->query( 'DROP TABLE IF EXISTS ' . $this->debugTable );
  }
}
