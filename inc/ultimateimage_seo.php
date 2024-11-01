<?php
/* Security-Check */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ultimateimage_seo
{
    public $ultimateSEOImage;

    public function initialize()
    {
        // Initialize settings or other necessary properties here
        $this->ultimateSEOImage = new stdClass(); // Assuming this is how your settings are initialized
        $this->ultimateSEOImage->settings = array(
            'enable_lazyload' => true,
            'enable_lazyload_acf' => true,
            'lazyload_threshold' => 100, // Example value
            'disable_srcset' => false,
            'enable_lazyload_styles' => true
        );
        $this->ultimateSEOImage->plugin['url'] = plugins_url('', __FILE__);
        $this->ultimateSEOImage->plugin['file'] = __FILE__;
        $this->updateURL = 'https://example.com/update';
        $this->updatejson = 'update.json';
        $this->updateslug = 'ultimate-seo-image';

        if( ! is_admin() && ! is_feed() ) {

            if( (isset($this->ultimateSEOImage->settings['enable_lazyload']) && $this->ultimateSEOImage->settings['enable_lazyload']) ||
                (isset($this->ultimateSEOImage->settings['enable_lazyload_acf']) && $this->ultimateSEOImage->settings['enable_lazyload_acf']) ) {
                add_action( 'wp_head', array($this, 'thresholdVariable') );
                add_action( 'wp_enqueue_scripts', array($this, 'unveilScript') );
            }

            if( isset($this->ultimateSEOImage->settings['enable_lazyload']) && $this->ultimateSEOImage->settings['enable_lazyload'] ) {
                add_filter( 'post_thumbnail_html', array($this, 'lazyLoadImages') );
                add_filter( 'the_content', array($this, 'lazyLoadImages'), 12 );
                add_filter( 'get_avatar', array($this, 'lazyLoadImages') );
            }

            if( isset($this->ultimateSEOImage->settings['enable_lazyload_acf']) && $this->ultimateSEOImage->settings['enable_lazyload_acf'] ) {
                add_filter( 'acf/load_value/type=textarea', array($this, 'lazyLoadImages'), 19 );
                add_filter( 'acf/load_value/type=wysiwyg', array($this, 'lazyLoadImages'), 19 );

                add_filter( 'acf_load_value-textarea', array($this, 'lazyLoadImages'), 20 );
                add_filter( 'acf_load_value-wysiwyg', array($this, 'lazyLoadImages'), 20 );
            }

            if( isset($this->ultimateSEOImage->settings['disable_srcset']) && $this->ultimateSEOImage->settings['disable_srcset'] ) {
                add_filter( 'wp_calculate_image_srcset', '__return_false' );
                add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
            }
        }

        // Hook your function to the appropriate action inside initialize
        add_action('wp_enqueue_scripts', array($this, 'thresholdVariable'));
    }

    /**
     * Threshold variable for wp_head
     */
    public function thresholdVariable() {
        // Sanitize and validate the lazyload threshold
        $lazyload_threshold = isset($this->ultimateSEOImage->settings['lazyload_threshold']) ? intval($this->ultimateSEOImage->settings['lazyload_threshold']) : 0;

        if ($lazyload_threshold > 0) {
            // Enqueue a dummy script
            wp_enqueue_script('ultimate-seo-image-script', plugins_url('/assets/js/ultimate-seo-image.js', __FILE__), array(), '1.0', true);

            // Prepare and add inline script to set the threshold variable
            $inline_script = 'var pbUnveilThreshold = ' . $lazyload_threshold . ';';
            wp_add_inline_script('ultimate-seo-image-script', $inline_script);
        }
    }

    /**
     * Unveil Script
     */
    public function unveilScript()
    {
        if( isset($this->ultimateSEOImage->settings['enable_lazyload_styles']) && $this->ultimateSEOImage->settings['enable_lazyload_styles'] ) {
            wp_register_style('unveil-css', $this->ultimateSEOImage->plugin['url'].'/assets/css/lazy.css', false, '1.0.0');
            wp_enqueue_style('unveil-css');
        }

        wp_register_script('unveil', $this->ultimateSEOImage->plugin['url'].'/assets/js/unveil.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('unveil');
    }

    public function lazyLoadImages($content)
    {
        /* No lazy images? */
        if( true === apply_filters('uiseo_disable_lazyloadimages', false) ) {
            return $content;
        }

        if ( strpos($content, '<img') === false ) {
            return $content;
        }

        if( get_post_type() == 'tribe_events' || is_feed() ) {
            return $content;
        }

        /* Empty gif */
        $null = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

        preg_match_all('#(<img(.*?)src=["\'](.+?)["\'](.*?)(/?)>)#', $content, $matches, PREG_PATTERN_ORDER);

        if( $matches ) {
            foreach( $matches[0] as $img ) {
                $new_img = $img;

                if(
                    strstr($img, 'lazy') ||
                    strstr($img, 'no-lazy') ||
                    strstr($img, 'image/gif;base64') ||
                    strstr($img, 'blank.gif') ||
                    strstr($img, 'data-src=') ||

                    // MasterSlider
                    strstr($img, 'ms-slide') ||
                    strstr($img, 'ms-thumb')
                ) {
                    continue;
                }

                $new_img = str_replace('src="', 'src="'.$null.'" data-src="', $new_img);

                if( strpos($new_img, 'class=') === false ) {
                    $new_img = str_replace('src="', 'class="ultimate-seo-lazy" src="', $new_img);
                } else {
                    $new_img = str_replace('class="', 'class="ultimate-seo-lazy ', $new_img);
                }

                $content = str_replace($img, $new_img.'<noscript>'.$img.'</noscript>', $content);
            }
        }

        return $content;
    }

    protected function updater()
    {
        try {
            $UpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                $this->updateURL.$this->updatejson,
                $this->ultimateSEOImage->plugin['file'],
                $this->updateslug
            );
        } catch (Exception $e) {
            new WP_Error('broke', 'Puc_v4_Factory failed!');
        }
    }
}