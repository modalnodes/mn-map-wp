<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ingmmo.com
 * @since      1.0.0
 *
 * @package    Mn_Map_Wp
 * @subpackage Mn_Map_Wp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mn_Map_Wp
 * @subpackage Mn_Map_Wp/public
 * @author     Marco Montanari/Modal Nodes <marco.montanari@gmail.com>
 */
class Mn_Map_Wp_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mn_Map_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mn_Map_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mn-map-wp-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mn_Map_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mn_Map_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mn-map-wp-public.js', array( 'jquery' ), $this->version, false );

	}

	public function register_shortcodes(){
		add_shortcode( 'mn-map',    array( $this, 'mn_map_container')  );
		add_shortcode( 'baselayer', array( $this, 'mn_map_baselayer_function')  );
		add_shortcode( 'datalayer', array( $this, 'mn_map_datalayer_function')  );
	}

	function mn_map_container( $atts , $content = null ) {
		$atts = shortcode_atts(
		array(
			'id' => 'map',
			'center' => 'map',
			'zoom' => '16',
			'minzoom' => '1',
			'maxzoom' => '19',
			'bounds' => 'map'
		), $atts, 'mn-map' );

		$ret = "<div id='$atts[\"map\"]'></div>";

		$ret .= "<script>";
		$ret .= "maps['$map_id'] = L.map('$atts['map']').setView($atts['center'], $atts['zoom']);";

		$pre_ret .= do_shortcode($content);
		$pre_ret = str_replace("###MAP###", $map_id);
		$ret .= $pre_ret;
		$ret .= "</script>";

		return $ret;
	}


	function mn_map_baselayer_function( $atts , $content = null ) {
		$base_maps = array(
			"osm" => "",
			"carto_light" => "{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png",
			"carto_dark" => "{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png"
		)
		$atts = shortcode_atts(
		array(
			'map' => 'carto_white',
		), $atts, 'baselayer' );

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
		$url = $base_maps[$atts["map"]];
		return "L.tileLayer('$protocol://$url', {
			maxZoom: 18, attribution: '&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>, &copy;
			<a href=\"https://carto.com/attribution\">CARTO</a>'
		}).addTo(maps['###MAP###']);";	
		
	}


	function mn_map_datalayer_function( $atts , $content = null ) {
		$atts = shortcode_atts(
		array(
			'foo' => 'no foo',
		), $atts, 'datalayer' );
		return '<div>' . $content . '</div>';
	}
	

}