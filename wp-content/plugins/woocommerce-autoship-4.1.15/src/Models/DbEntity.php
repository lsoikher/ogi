<?php

class WC_Autoship_Models_DbEntity implements ArrayAccess, JsonSerializable {
	const PREFIX = 'wc_autoship_';

	/**
	 * ID
	 * @var int
	 */
	protected $_id;

	/**
	 * Data
	 * @var array
	 */
	protected $_data;
	
	/**
	 * Editable fields as ( 'key' => required ) pairs
	 * @var array
	 */
	protected $_editable_fields = array();
	
	/**
	 * Read-only fields as ( 'key' => required ) pairs.
	 * Changes to these fields will not be written to the DB.
	 * @var array
	 */
	protected $_readonly_fields = array(
		'created_time' => false,
		'modified_time'	=> false
	);
	
	/**
	 * Table name
	 * @var string
	 */
	protected $_table_name;
	
	public function __construct( $id = NULL ) {
		if ( $id != NULL ) {
			$this->set_id( $id );
		}
	}
	
	/**
	 * @see ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return string|NULL
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset, NULL );
	}
	
	/**
	 * @see ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param string $value
	 */
	public function offsetSet( $offset, $value ) {
		$this->set( $offset, $value );
	}
	
	/**
	 * @see ArrayAccess::offsetUnset()
	 * @param string $offset
	 */
	public function offsetUnset( $offset ) {
		unset( $this->_data[ $offset ] );
	}
	
	/**
	 * @see ArrayAccess::offsetExists()
	 * @param string $offset
	 */
	public function offsetExists( $offset ) {
		return isset( $this->_data[ $offset ] );
	}
	
	public function get( $key, $default = '' ) {
		if ( isset( $this->_data[ $key ] ) ) {
			return $this->_data[ $key ];
		}
		return $default;
	}
	
	public function set( $key, $value ) {
		if ( $key == 'id' ) {
			$this->_id = $value;
			return true;
		}
		if ( isset( $this->_editable_fields[ $key ] )
				||  isset( $this->_readonly_fields[ $key ] ) ) {
			$this->_data[ $key ] = $value;
			return true;
		} 
		return false;
	}
	
	public function get_data() {
		$data = $this->_data;
		$data['id'] = $this->_id;
		return $data;
	}
	
	public function set_data( $data, $merge = true ) {
		if ( $merge ) {
			foreach( $data as $key => $value ) {
				$this->set( $key, $value );
			}
		} else {
			$this->_data = $data;
		}
	}
	
	public function get_id() {
		return $this->_id;
	}
	
	public function set_id( $id ) {
		$this->_id = $id;
		$this->load_data();
	}
	
	public static function get_full_table_name() {
		global $wpdb;
		$self = new static();
		return $wpdb->prefix . WC_Autoship_Models_DbEntity::PREFIX . $self->_table_name;
	}
	
	public function delete() {
		global $wpdb;
		if ( $this->_id == NULL ) return 0;
		$table_name = self::get_full_table_name();
		$result = $wpdb->delete( $table_name, array( 'id' => $this->_id ) );
		do_action( 'wc_autoship_db_delete', $table_name, $result, $this->_id );
		return $result;
	}
	
	
	public static function id_exists( $id ) {
		global $wpdb;
		$table_name = static::get_full_table_name();
		$query = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table_name} WHERE id = %s",
			$id
		);
		$result = $wpdb->get_col( $query, 0 );
		return ( $result[0] > 0 );
	}
	
	public function load_data() {
		global $wpdb;
		
		if ( $this->_id == NULL ) {
			return NULL;
		}
		
		$table_name = self::get_full_table_name();
		
		$data_query = $wpdb->prepare(
			"SELECT *
			FROM {$table_name}
			WHERE id = %s",
			$this->_id
		);
		$this->_data = $wpdb->get_row( $data_query, ARRAY_A );
		return $this->_data;
	}
	
	public function save( $additional_data = array() ) {
		global $wpdb;
		
		if ( $this->_data == NULL ) {
			return false;
		}
		
		$table_name = self::get_full_table_name();
		$now = date('Y-m-d H:i:s');
		
		$data = array();
		foreach ( $this->_editable_fields as $column => $required ) {
			if ( isset( $this->_data[ $column ] ) ) {
				$data[ $column ] = $this->_data[ $column ];
			} elseif ( $required ) {
				return false;
			}
		}
		$data = array_merge( $data, $additional_data );
		if ( count( $data ) < 1 ) {
			return false;
		}
		$data['modified_time'] = $now;
		
		if ( ! empty( $this->_id ) ) {
			if ( self::id_exists( $this->_id ) ) {
				$where = array( 'id' => $this->_id );
				$result = $wpdb->update( $table_name, $data, $where );
				do_action( 'wc_autoship_db_update', $table_name, $data, $result, $this->_id );
				return $result;
			} else {
				$data['created_time'] = $now;
				$data['id'] = $this->_id;
				$result = $wpdb->insert( $table_name, $data );
				do_action( 'wc_autoship_db_insert', $table_name, $data, $result, $this->_id );
				return $result;
			}
		} else {
			$data['created_time'] = $now;
			$affected_rows = $wpdb->insert( $table_name, $data );
			if ( $affected_rows > 0 ) {
				$this->set_id( $wpdb->insert_id );
			}
			do_action( 'wc_autoship_db_insert', $table_name, $data, $affected_rows, $this->_id );
			return $affected_rows;
		}

		return false;
	}

	public function jsonSerialize() {
		return $this->get_data();
	}
}