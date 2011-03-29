<?php
/**
 * PIE loader class file
 *
 * @author Marshall Sorenson <marshall.sorenson@gmail.com>
 * @link http://marshallsorenson.com/
 * @copyright Copyright (C) 2010 Marshall Sorenson
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package pie
 * @subpackage loader
 * @since 1.0
 */

// check if pie has been included already
if ( defined( 'PIE_EASY_VERSION' ) ) {
	return;
}

// set pie contants
define( 'PIE_EASY_VERSION', '1.0' );
define( 'PIE_EASY_DIR', dirname( __FILE__ ) );
define( 'PIE_EASY_VENDORS_DIR', PIE_EASY_DIR . DIRECTORY_SEPARATOR . 'vendors' );

/**
 * Make loading pie features easy
 */
final class Pie_Easy_Loader
{
	/**
	 * Singleton instance
	 * 
	 * @var Pie_Easy_Loader
	 */
	private static $instance;
	
	/**
	 * Available features
	 *
	 * @var array
	 */
	private $features = array(
		'ajax',
		'collections' =>
			array( 'map', 'map_iterator', 'stack', 'stack_iterator' ),
		'docs',
		'enqueue',
		'features' =>
			array( 'feature' ),
		'files',
		'l10n',
		'markdown',
		'options' =>
			array( 'registry', 'option', 'option_directive', 'option_renderer', 'section', 'uploader', 'walkers' ),
		'init' =>
			array( 'directive' ),
		'schemes' =>
			array( 'scheme', 'scheme_directive', 'scheme_enqueue' )
	);

	/**
	 * Constructor
	 */
	private function __construct()
	{
		// can't create singleton
	}

	/**
	 * Initialize
	 *
	 * @param string $pie_url The full URL to pie files
	 */
	final static public function init( $pie_url )
	{
		// new instance if necessary
		if ( !self::$instance instanceof self ) {
			// define PIE urls
			define( 'PIE_EASY_URL', $pie_url );
			define( 'PIE_EASY_ASSETS_URL', PIE_EASY_URL . '/assets' );
			define( 'PIE_EASY_CSS_URL', PIE_EASY_ASSETS_URL . '/css' );
			define( 'PIE_EASY_IMAGES_URL', PIE_EASY_ASSETS_URL . '/images' );
			define( 'PIE_EASY_JS_URL', PIE_EASY_ASSETS_URL . '/js' );
			// create singleton instance
			self::$instance = new self();
			// handle requirements
			self::init_deps();
		}
	}

	/**
	 * Initialize screen requirements
	 */
	final static private function init_deps()
	{
		// need the enqueuer
		self::$instance->load( 'enqueue' );
		
		// override jQuery UI
		add_action( 'wp_default_scripts', array( Pie_Easy_Enqueue::instance(), 'override_jui' ) );

		// register some styles
		Pie_Easy_Enqueue::register_style(
			'pie-easy-ui-lightness', 'ui-lightness/jquery-ui-custom.css', array('colors') );
		Pie_Easy_Enqueue::register_style(
			'pie-easy-colorpicker', 'colorpicker.css', array('colors') );
		Pie_Easy_Enqueue::register_style(
			'pie-easy-global', 'global.css', array('pie-easy-ui-lightness', 'pie-easy-colorpicker' ) );
	
		// register more scripts
		Pie_Easy_Enqueue::register_script(
			'pie-easy-global', 'global.js', array('jquery-ui-button') );
		Pie_Easy_Enqueue::register_script(
			'pie-easy-colorpicker', 'colorpicker.js', array('jquery') );
		Pie_Easy_Enqueue::register_script(
			'pie-easy-jquery-swfupload', 'jquery.swfupload.js', array('jquery', 'swfupload-all') );
		Pie_Easy_Enqueue::register_script(
			'pie-easy-uploader', 'uploader.js', array('pie-easy-global', 'pie-easy-jquery-swfupload', 'jquery-ui-button') );
	}

	/**
	 * Initialize screen requirements
	 */
	final static public function init_screen()
	{
		// global style and script
		wp_enqueue_style( 'pie-easy-global' );
		wp_enqueue_script( 'pie-easy-global' );
	}

	/**
	 * Load a feature via static call
	 *
	 * @param string $feature,...
	 * @return bool
	 */
	final static public function load( $feature )
	{
		// handle variable number of args
		$features = func_get_args();

		// loop through all features
		foreach ( $features as $feature ) {
			self::$instance->load_feature( $feature );
		}
	}
	
	/**
	 * Load a feature
	 *
	 * @param string $feature
	 * @return boolean
	 */
	final public function load_feature( $feature )
	{
		// make sure feature exists
		if ( array_key_exists( $feature, $this->features ) ) {
			return $this->load_package( $feature );
		} elseif ( in_array( $feature, $this->features, true ) ) {
			return $this->load_file( $feature );
		} else {
			$split = explode( '/', $feature );
			if ( count($split) == 2 ) {
				return $this->load_file( $split[1], $split[0] );
			}
		}

		// sorry
		throw new Exception( sprintf( 'The feature "%s" is not valid.', $feature ) );
	}

	/**
	 * Load a feature package
	 *
	 * @param string $feature
	 * @return true
	 */
	private function load_package( $feature )
	{
		foreach ( $this->features[$feature] as $file ) {
			$this->load_file( $file, $feature );
		}

		return true;
	}

	/**
	 * Load a feature file
	 *
	 * @param string $name
	 * @param string $feature
	 * @return true
	 */
	private function load_file( $name, $feature = null )
	{
		// build up file path
		$file = sprintf(
			'%s%s%s%s.php',
			PIE_EASY_DIR,
			DIRECTORY_SEPARATOR,
			( $feature ) ? $feature . DIRECTORY_SEPARATOR : null,
			$name
		);

		// load it
		require_once $file;
		return true;
	}

}

?>
