<?php
/* Security-Check */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ultimateimage_frontend
{
	/** @var ultimateSEOImage */
	var $ultimateSEOImage;

	/** @var ultimateimage_optimizer */
	var $optimizer;

	public function __construct( ultimateSEOImage &$SEO_images )
	{
		$this->ultimateSEOImage = $SEO_images;

      

        if( isset($this->ultimateSEOImage->settings['caching_ttl']) && is_numeric($this->ultimateSEOImage->settings['caching_ttl']) ) {
            $this->caching_ttl = $this->ultimateSEOImage->settings['caching_ttl'];
        }
	}

	/**
	 * initialize frontend functions
	 *
	 * @return void
	 */
	public function initialize()
	{
		// process post thumbnails
		if( isset($this->ultimateSEOImage->settings['optimize_img']) && ($this->ultimateSEOImage->settings['optimize_img'] == 'all' || $this->ultimateSEOImage->settings['optimize_img'] == 'thumbs') ) {

		    if( function_exists('is_woocommerce') && is_woocommerce()  ) {

                add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_attributes' ], 10, 2 );

            } else {
                add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_attributes' ], 10, 2 );
            }

		} else if( function_exists('is_woocommerce') && is_woocommerce() ) {
            add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_attributes' ], 10, 2 );
        }

		// process post images
		if( isset($this->ultimateSEOImage->settings['optimize_img']) && ($this->ultimateSEOImage->settings['optimize_img'] == 'all' || $this->ultimateSEOImage->settings['optimize_img'] == 'post') ) {
			add_filter( 'the_content', [ $this, 'optimize_html' ], 9999, 1 );

			/*
			 * Support for AdvancedCustomFields
			 */
			//add_filter('acf/load_value/type=textarea', [ $this, 'optimize_html' ], 20, 3);
			add_filter('acf/load_value/type=wysiwyg', [ $this, 'optimize_html' ], 20, 3);

			// support for acf below v.4 removed
		}
	}

	/**
	 * Check if the optimizer is already initialized and initialize if not
	 *
	 * @return void
	 */
	private function _maybe_initialize_optimizer()
	{
		if( false === is_a($this->optimizer, 'ultimateimage_optimizer') ) {
			$this->optimizer = new ultimateimage_optimizer( $this->ultimateSEOImage->settings );
		}
	}

    /**
     * Optimize given HTML code
     *
     * @param string $content
     *
     * @param int $post_id
     * @param null|string $field
     * @return string
     */
public function optimize_html( $content, $post_id=0, $field=null )
{
    // Initialize $caching variable
    $caching = false;

    // Check if caching is enabled
    if (defined('WP_CACHE') && WP_CACHE) {
        $caching = true;
    }

    if( $post_id === 0 ) {
        // set post_id
        $post_id = get_the_ID();
    }

    // maybe initialize the optimizer class
    $this->_maybe_initialize_optimizer();

    // optimize html
    $content = $this->optimizer->optimize_html( $content );

    // Set Cache
    if( $caching && isset($cache_key) && isset($cache) ) {
        $cache->set_cache($cache_key, $content);
    }

    // Return the optimized content
    return $content;
}


	/**
	 * Add image title and alt to post thumbnails
	 *
	 * @param array $attr
	 * @param WP_Post $attachment
	 * @return array
	 */
	public function optimize_image_attributes( $attr, $attachment = null )
	{
		// maybe initialize the optimizer class
		$this->_maybe_initialize_optimizer();

		// optimize image attributes
		$attr = $this->optimizer->optimize_image_attributes( $attr, $attachment );

		return $attr;
	}
}