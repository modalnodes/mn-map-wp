<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://ingmmo.com
 * @since      1.0.0
 *
 * @package    Mn_Map_Wp
 * @subpackage Mn_Map_Wp/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mn_Map_Wp
 * @subpackage Mn_Map_Wp/includes
 * @author     Marco Montanari/Modal Nodes <marco.montanari@gmail.com>
 */
class Mn_Map_Wp_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mn-map-wp',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
