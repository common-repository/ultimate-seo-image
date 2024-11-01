<?php
/**
* Plugin Name: Ultimate SEO Image
* Description: Boost your WordPress website SEO by automatically adding ALT tags and TITLE attributes to post images
* Version: 1.0.0
* Author: WPUnicorn Performance Lab
* Author URI: https://wordpress.org/plugins/ultimate-seo-image
* Domain Path: /lang/
* Text Domain: ultimate-seo-image
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
**/



/* Security-Check */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( ! class_exists('ultimateSEOImage') ):

	class ultimateSEOImage
	{
		/** @var string Plugin version */
		var $version = '1.0.0';

		/** @var array Plugin Settings */
		var $settings = array();

		/** @var array Plugin Data */
		var $plugin = array();

		/** @var string option name */
		var $option_name = 'ultimate-seo-image';

		/** @var string path to pro file  */
		protected $proPath;

		/**
		 * Initialize Ultimate SEO Image
		 * @return void
		 */
		public function initialize()
		{
			
			
			//Defines
			if( !defined( 'ultimate-seo-image' ) ) {
				define( 'ultimate-seo-image', 'ultimate-seo-image' );
			}
			
			
			// Plugin Default Data
			$this->plugin = array(
			
				// urls
				'file'				=> __FILE__,
				'basename'			=> plugin_basename( __FILE__ ),
				'path'				=> plugin_dir_path( __FILE__ ),
				'url'				=> plugin_dir_url( __FILE__ ),
			);

			// Default settings
			$this->settings = array(

			);

			// load plugin textdomain
			$this->load_plugin_textdomain();

			// load settings
			$this->load_settings();

			// pro path
			$this->proPath = $this->plugin['path'] . 'inc/ultimateimage_seo.php';
			
		// Set paths for admin panel files
        $this->adminPath = $this->plugin['path'] . 'admin/options.php';

        // Include Admin panel
		require_once $this->adminPath;

			

			// check if this is pro version and initialize pro class
			if( true === file_exists($this->proPath) ) {

				// load pro class + extend and manipulate basic version
				new ultimateimage_seo( $this );

			} else {
                $this->settings['proVersion']   = false;
            }

		

				// initialize frontend
				$this->initialize_frontend();

		
		}

		/**
		 * Load Plugin Textdomain
		 * @return void
		 */
		public function load_plugin_textdomain()
		{
			load_plugin_textdomain(
				'ultimate-seo-image',
				false,
				dirname( $this->plugin['basename'] ).'/lang/'
			);
		}

		/**
		 * Load Plugin Settings from Options and maybe convert from older Versions
		 * @return void
		 */
		public function load_settings()
		{
			$settings_from_option = get_option('ultimate-seo-image')			;

			if( is_array($settings_from_option) ) {

				$this->settings = array_merge(
					$this->settings,
					$settings_from_option
				);

			}

		}

		/**
		 * initialize frontend functions
		 * @return void
		 */
		public function initialize_frontend()
		{
			$frontend = new ultimateimage_frontend( $this );

			add_action('template_redirect', array($frontend, 'initialize'));
		}


		
	} /* end of ultimateSEOImage */

	/**
	 * Initialize ultimateSEOImage Class
	 */
	$ultimateSEOImage = new ultimateSEOImage();

	/**
	 * Trigger plugins_loaded hook with method ultimateSEOImage->initialize()
	 */
	add_action(
		'plugins_loaded',
		array(
			$ultimateSEOImage,
			'initialize'
		)
	);



endif;

/**
 * Autoloader function
 *
 * @param $class
 *
 * @return WP_Error
 */

if( ! function_exists('ultimateSEOImageAutoload') ) {

    function ultimateSEOImageAutoload($class)
    {
        $allowed_classes = array(
            'ultimateimage_frontend',
            'ultimateimage_optimizer',
            'ultimateimage_seo',
            'ultimateimage_cache'
        );

        if( in_array($class, $allowed_classes) ) {

            $require_once = sprintf(
                '%s/inc/%s.php',

                dirname(__FILE__),
                $class
            );

            if( file_exists( $require_once ) ) {
                require_once( $require_once );
            } else {
                return new WP_Error(
                    'broke',
                    sprintf(
                        esc_html__( 'Can not find: %s', 'ultimate-seo-image' ),
                        $require_once
                    )
                );
            }
        }
    }

    /* Autoload Init */
    spl_autoload_register('ultimateSEOImageAutoload');
}
