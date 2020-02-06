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
		wp_enqueue_style( "leaflet", '//unpkg.com/leaflet@1.2.0/dist/leaflet.css', array(), $this->version, 'all' );

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
		wp_enqueue_script( "leaflet", '//unpkg.com/leaflet@1.2.0/dist/leaflet.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( "mustache_front", '//cdn.jsdelivr.net/npm/mustache@2.3.0/mustache.min.js', array( 'jquery' ), $this->version, false );

	}

	public function register_shortcodes(){

		remove_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'shortcode_unautop', 100 );

		add_shortcode( 'mn-map',    array( $this, 'mn_map_container')  );
		add_shortcode( 'baselayer', array( $this, 'mn_map_baselayer_function')  );
		add_shortcode( 'datalayer', array( $this, 'mn_map_datalayer_function')  );
	}

	function mn_map_container( $atts , $content = null ) {
		$atts = shortcode_atts(
		array(
			'id' => 'map',
			'center' => '[11,22]',
			'zoom' => '16',
			'minzoom' => '1',
			'maxzoom' => '19',
			'bounds' => 'map',
			'localize' => 'true',
			'layerswitcher' => 'no',
			'height' => '400px',
			'width' => '100%'	
		), $atts, 'mn-map' );

		$map_id = "map__{$atts["id"]}";

		$ret = "";
		$ret .= "<div id='{$atts["id"]}' style='width:{$atts["width"]};height:{$atts["height"]};'></div>";
		$ret .= "<script>";
		$ret .= "maps['{$map_id}'] = L.map('{$atts['id']}').setView({$atts['center']}, {$atts['zoom']});";
		$ret .= "maps['{$map_id}']['__baseMaps'] = {};";
		$ret .= "maps['{$map_id}']['__overlayMaps'] = {};";
		$ret .= "maps['{$map_id}']['__baseMaps_list'] = [];";
		$ret .= "maps['{$map_id}']['__overlayMaps_list'] = [];";

		if($atts["localize"] == "true"){
			$ret .= "maps['{$map_id}'].locate({setView: true, maxZoom: {$atts['zoom']}});";
		}
		$ret .= "</script>";

		$pre_ret .= do_shortcode($content);
		$pre_ret = str_replace("###MAP###", $map_id, $pre_ret);
		$ret .= $pre_ret;
		

		if($atts["layerswitcher"] != "no"){
			$ret .= "<script>";
			$ret .= "function load_layerswitcher(){L.control.layers(maps['{$map_id}']['__baseMaps'], maps['{$map_id}']['__overlayMaps']).addTo(maps['{$map_id}']);}";
			$ret .= "setTimeout(load_layerswitcher, 500);";
			$ret .= "</script>";
		}
		//$ret = preg_replace(array("<p>","</p>", "<br />"), "", $ret);

		return $ret;
	}


	function mn_map_baselayer_function( $atts , $content = null ) {
		$base_maps = array(
			"osm" => "{a|b|c}.tile.openstreetmap.org/{z}/{x}/{y}.png",
			"carto_light" => "cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png",
			"carto_dark" => "cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png",
			"stamen_toner" => "a.tile.stamen.com/toner/{z}/{x}/{y}.png",
			"stamen_watercolor" => "c.tile.stamen.com/watercolor/{z}/{x}/{y}.jpg"
		);
		$base_names = array(
			"osm" => "OpenStreetMap",
			"carto_light" => "CartoDB Light",
			"carto_dark" => "CartoDB Dark Matter",
			"stamen_toner" => "Stamen Toner",
			"stamen_watercolor" => "Stamen Watercolor"
		);
		$atts = shortcode_atts(
		array(
			'map' => 'carto_light',
			'mode' => 'raster'
		), $atts, 'baselayer' );

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
		$url = $base_maps[$atts["map"]];
		$nme = $base_names[$atts["map"]];
		$ret = "";
		$ret .= "<script>";
		$ret .= "var idx = maps['###MAP###']['__baseMaps_list'].push(L.tileLayer('$protocol://$url', {
			maxZoom: 18, attribution: 'Copyright: <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>, Copyright: <a href=\"https://carto.com/attribution\">CARTO</a>'
		})) -1;";
		$ret .= "maps['###MAP###']['__baseMaps_list'][idx].addTo(maps['###MAP###']);";
		$ret .= "maps['###MAP###']['__baseMaps']['{$nme}'] = maps['###MAP###']['__baseMaps_list'][idx];";	
		$ret .= "</script>";
		return $ret;
	}


	function mn_map_datalayer_function( $atts , $content = null ) {
		$atts = shortcode_atts(
		array(
			'url' => 'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/all_hour.geojson',
			'name' => 'Quakes (last hour)',
			'format' => 'geojson'
		), $atts, 'datalayer' );

		$cntt = preg_replace('/[\n\r]/', ' ', $content);

		$ret = "";
		$ret .= "<script>";
		$nme = $atts["name"];
		$ret .= "
		jQuery.getJSON('{$atts["url"]}', function(data){
			var idx = maps['###MAP###']['__overlayMaps_list'].push(L.geoJSON(data, {
				onEachFeature: function(feature, layer) {
					if (feature.properties) {
						console.log('FIGA');
						var tl = `{$cntt}`;
						var pc = Mustache.render(tl,feature.properties);
						layer.bindPopup(pc);
					}
				}
			}))-1;
			maps['###MAP###']['__overlayMaps_list'][idx].addTo(maps['###MAP###']);
			maps['###MAP###']['__overlayMaps']['{$nme}'] = maps['###MAP###']['__overlayMaps_list'][idx];
		})";
		$ret .= "</script>";
		return $ret;
	}
	

}
