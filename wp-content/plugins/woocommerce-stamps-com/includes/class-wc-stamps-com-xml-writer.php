<?php
/**
 * WooCommerce Stamps.com Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Stamps.com Export to newer
 * versions in the future. If you wish to customize WooCommerce Stamps.com Export for your
 * needs please refer to http://docs.woothemes.com/document/stamps-com-xml-file-export/ for more information.
 *
 * @package     WC-Stamps-Com/XML-Writer
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Stamps.com XML Writer Class
 *
 * Converts customer/order data into XML in the format required by Stamps.com
 *
 * @since 2.0
 * @extends \XMLWriter
 */
class WC_Stamps_Com_XML_Writer extends XMLWriter {


	/**
	 * Open XML document in memory for writing and set version/encoding
	 *
	 * @since 2.0
	 * @param string $xml_version
	 * @param string $xml_encoding
	 * @return \WC_Stamps_Com_XML_Writer
	 */
	public function __construct( $xml_version = '1.0', $xml_encoding = 'UTF-8' ) {

		// Create XML document in memory
		$this->openMemory();

		// allow modification
		$xml_version  = apply_filters( 'wc_stamps_com_xml_version', $xml_version );
		$xml_encoding = apply_filters( 'wc_stamps_com_xml_encoding', $xml_encoding );

		// Set XML version & encoding
		$this->startDocument( $xml_version, $xml_encoding );
	}


	/**
	 * Build XML for customers/orders in the default format
	 *
	 * @since 2.0
	 * @param array $order_ids
	 * @return string generated XML
	 */
	public function get_order_export_xml( $order_ids ) {

		// get order data in array format for passing to array_to_xml() function
		$orders = $this->get_orders( $order_ids );

		// allow array format to be modified or changed completely
		$xml_array = apply_filters( 'wc_stamps_com_order_export_format', array(
			'Print' => array(
				'@attributes' => array( 'xmlns' => 'http://stamps.com/xml/namespace/2009/8/Client/BatchProcessingV1' ),
				'Item'        => $orders,
			),
		), $orders );

		// get root element (first key in array)
		$keys         = array_keys( $xml_array );
		$root_element = reset( $keys );

		// generate xml starting with the root element and recursively generating child elements
		$this->array_to_xml( $root_element, $xml_array[ $root_element ] );

		return $this->output_xml();
	}


	/**
	 * Convert array into XML by recursively generating child elements
	 *
	 * @since 2.0
	 * @param string|array $element_key name for element, e.g. <OrderID>
	 * @param string|array $element_value value for element, e.g. 1234
	 * @return string generated XML
	 */
	private function array_to_xml( $element_key, $element_value = array() ) {

		if ( is_array( $element_value ) ) {

			// handle attributes
			if ( '@attributes' == $element_key ) {
				foreach ( $element_value as $attribute_key => $attribute_value ) {
					$this->addAttribute( $attribute_key, $attribute_value );
				}
				return;
			}

			// handle multi-elements (e.g. multiple <Order> elements)
			if ( is_numeric( key( $element_value ) ) ) {

				// recursively generate child elements
				foreach ( $element_value as $child_element_key => $child_element_value ) {

					$this->startElement( $element_key );

					foreach ( $child_element_value as $sibling_element_key => $sibling_element_value ) {
						$this->array_to_xml( $sibling_element_key, $sibling_element_value );
					}

					$this->endElement();
				}

			} else {

				// start root element
				$this->startElement( $element_key );

				// recursively generate child elements
				foreach ( $element_value as $child_element_key => $child_element_value ) {
					$this->array_to_xml( $child_element_key, $child_element_value );
				}

				// end root element
				$this->endElement();
			}

		} else {

			// handle single elements
			if ( '@value' == $element_key ) {
				$this->text( $element_value );
			} else {
				$this->writeElement( $element_key, $element_value );
			}

			return;
		}
	}


	/**
	 * End the XML document and output the XML stream
	 *
	 * @since 2.0
	 * @return string generated XML
	 */
	private function output_xml() {
		$this->endDocument();

		return $this->outputMemory();
	}


	/**
	 * Add a complete attribute to element
	 *
	 * @since 2.0
	 * @param string $id attribute key
	 * @param string $value attribute value
	 */
	private function addAttribute( $id, $value ) {
		$this->startAttribute( $id );
		$this->text( $value );
		$this->endAttribute();
	}


	/**
	 * Creates array of given orders in standard format
	 *
	 * Filter in get_order_export_xml() allow modification of parent array
	 * Filter in method allows modification of individual order array format
	 *
	 * @since 2.0
	 * @param array $order_ids order IDs to generate array from
	 * @return array orders in array format required by array_to_xml()
	 */
	private function get_orders( $order_ids ) {

		$order_data = array();

		// loop through each order
		foreach ( $order_ids as $order_id ) {

			// instantiate WC_Order object
			$order = new WC_Order( $order_id );

			// Standard format
			$_data = array(
				'OrderDate' => $order->order_date,
				'OrderID'   => ltrim( $order->get_order_number(), _x( '#', 'hash before order number', WC_Stamps_Com::TEXT_DOMAIN ) ),
				'Recipient' => array(
					'AddressFields' => array(
						'Company'   => $order->shipping_company,
						'FirstName' => ( ! empty( $order->shipping_first_name ) ) ? $order->shipping_first_name : $order->billing_first_name,
						'LastName'  => ( ! empty( $order->shipping_last_name ) ) ? $order->shipping_last_name : $order->billing_last_name,
						'Address1'  => $order->shipping_address_1,
						'Address2'  => $order->shipping_address_2,
						'City'      => $order->shipping_city,
						'State'     => $order->shipping_state,
						'ZIP'       => $order->shipping_postcode,
						'Country'   => preg_replace( '/\s\(\w+\)/', '', isset( SV_WC_Plugin_Compatibility::WC()->countries->countries[ $order->shipping_country ] ) ? SV_WC_Plugin_Compatibility::WC()->countries->countries[ $order->shipping_country ] : $order->shipping_country ),
						'OrderedPhoneNumbers'   => array(
							'Number' => $order->billing_phone,
						),
						'OrderedEmailAddresses' => array(
							'Address' => $order->billing_email,
						),
					)
				),
				'WeightOz'              => $this->get_total_order_weight( $order ),
				'RecipientEmailOptions' => array(
					'ShipmentNotification' => 'false',
				),
			);

			// add customs info if specified by the user or shipping country is non US
			if ( 'yes' === get_option( 'wc_stamps_com_include_customs_info' ) || 'US' !== $order->shipping_country ) {
				$_data['CustomsInfo'] = array(
					'Contents'         => array(
						'Item' => $this->get_line_items( $order ),
					),
					'ContentsType'     => 'other',
					'DeclaredValue'    => $order->get_total() - $order->get_total_tax() - $order->get_total_discount() - SV_WC_Plugin_Compatibility::get_total_shipping( $order ),
					'UserAcknowledged' => 'true',
				);
			}

			$order_data[] = apply_filters( 'wc_stamps_com_order_list_format', $_data, $order );
		}

		return $order_data;
	}


	/**
	 * Creates array of order line items in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual line items array format
	 *
	 * @since 2.0
	 * @param object $order
	 * @return array - line items in array format required by array_to_xml()
	 */
	private function get_line_items( $order ) {

		$items = array();

		// loop through each item in order
		foreach ( $order->get_items() as $item ) {

			// get the product
			$product = $order->get_product_from_item( $item );

			// skip if the product no longer exists
			if ( ! $product ) {
				continue;
			}

			// product SKU
			$sku = $product->get_sku();

			// weight
			$weight = $product->get_weight();

			// convert weight to ounces
			if ( 'oz' !== get_option( 'woocommerce_weight_unit' ) ) {
				$weight = woocommerce_get_weight( $weight, 'oz' );
			}

			// build array
			$items[] = apply_filters( 'wc_stamps_com_order_line_item_format', array(
				'Description' => substr( ! empty( $sku ) ? $sku : $product->get_title(), 0, 31 ),
				'Quantity'    => $item['qty'],
				'Value'       => $item['line_total'],
				'WeightOz'    => number_format( $weight, 2, '.', '' ),
			), $order, $item );
		}

		return $items;
	}


	/**
	 * Returns the total weight for the order in ounces
	 *
	 * @since 2.0
	 * @param object $order
	 * @return float formatted to XXXX.XX
	 */
	private function get_total_order_weight( $order ) {

		$weight = 0;

		// iterate through each order line item
		foreach( $order->get_items() as $item ) {

			$product = $order->get_product_from_item( $item );

			$weight += ( $product && $product->get_weight() > 0 ) ? $product->get_weight() * $item['qty'] : 0;
		}

		// convert units to ounces
		if ( 'oz' !== get_option( 'woocommerce_weight_unit' ) ) {
			$weight = woocommerce_get_weight( $weight, 'oz' );
		}

		// format to XXXX.XX
		return number_format( $weight, 2, '.', '' );
	}


} //end \WC_Stamps_Com_XML_Writer class
