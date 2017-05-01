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
 * @package     WC-Stamps-Com/Exporter
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Stamps.com Exporter Class
 *
 * Handles client download of XML file
 *
 * @since 2.0
 */
class WC_Stamps_Com_Exporter {


	/** @var array order IDs to export */
	public $order_ids;

	/** @var string file name for export or download */
	public $file_name;


	/**
	 * Initializes the export object from an array of valid order IDs and sets the filename for export or download
	 *
	 * @since 2.0
	 * @param array $order_ids orders to export / download
	 * @return \WC_Stamps_Com_Exporter
	 */
	public function __construct( $order_ids ) {

		// handle single order exports
		if ( ! is_array( $order_ids ) ) {
			$order_ids = array( $order_ids );
		}

		$this->order_ids = $order_ids;

		// set file name
		$this->file_name = $this->replace_file_name_variables( get_option( 'wc_stamps_com_export_file_name' ) );
	}


	/**
	 * Downloads the given orders in Stamps.com XML format
	 *
	 * @since 2.0
	 */
	public function download() {

		// try to set unlimited script timeout
		@set_time_limit( 0 );

		// set headers for download
		header( apply_filters( 'wc_stamps_com_download_content_type', 'Content-Type: application/xml; charset=UTF-8' ) );
		header( sprintf( 'Content-Disposition: attachment; filename="%s"', $this->file_name ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// allow plugins to add additional headers
		do_action( 'wc_stamps_com_download_after_headers' );

		// clear the output buffer
		@ob_clean();

		// open the output buffer for writing
		$fp = fopen( 'php://output', 'w' );

		// instantiate the XML generator
		$xml = new WC_Stamps_Com_XML_Writer();

		// write the generated XML to the output buffer
		fwrite( $fp, $xml->get_order_export_xml( $this->order_ids ) );

		// close the output buffer
		fclose( $fp );

		exit;
	}


	/**
	 * Replaces variables in file name setting (e.g. %%timestamp%% becomes 2013_03_20_16_22_14 )
	 *
	 * @since 2.0
	 * @param string $pre_replace_file_name file name before variable replacement
	 * @return string filename with variables replaced
	 */
	private function replace_file_name_variables( $pre_replace_file_name ) {

		$variables   = array( '%%timestamp%%' );
		$replacement = array( date( 'Y_m_d_H_i_s' ) );

		$post_replace_file_name = str_replace( $variables, $replacement, $pre_replace_file_name );

		return apply_filters( 'wc_stamps_com_export_file_name', $post_replace_file_name, $pre_replace_file_name );
	}


} //end \WC_Stamps_Com_Exporter class
