<?php

/*
# Admin options
# Since v 1.0.0
*/


/* Security-Check */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add a new menu item and page
add_action('admin_menu', 'usiultimate_ultimate_seo_image_menu');
function usiultimate_ultimate_seo_image_menu()
{
    add_menu_page(
        __('SEO Image', 'ultimate-seo-image'),                // Page title
        __('SEO Image', 'ultimate-seo-image'),                // Menu title
        'manage_options',                                     // Capability
        'ultimate-seo-image',                                 // Menu slug
        'ultimate_seo_image_page',                            // Callback function
        'dashicons-cover-image'                               // Icon
    );
}

// Register settings, sections, and fields
add_action('admin_init', 'ultimate_seo_image_settings');
function ultimate_seo_image_settings()
{
    // Register a new setting for "ultimate-seo-image" page.
    register_setting('ultimate_seo_image_options', 'ultimate_seo_image_options', 'ultimate_seo_image_options_sanitize');

    // Add a new section in the "ultimate-seo-image" page.
    add_settings_section(
        'ultimate_seo_image_settings_section',
        __('Settings', 'ultimate-seo-image'),
        'ultimate_seo_image_section_callback',
        'usiultimate_ultimate-seo-image-settings'
    );

    // Add fields to the section
    add_settings_field(
        'optimize_img',
        __('Optimize images', 'ultimate-seo-image'),
        'usiultimate_seo_image_optimize_img_render',
        'usiultimate_ultimate-seo-image-settings',
        'ultimate_seo_image_settings_section'
    );

    add_settings_field(
        'sync_method',
        __('Sync method', 'ultimate-seo-image'),
        'usiultimate_seo_image_sync_method_render',
        'usiultimate_ultimate-seo-image-settings',
        'ultimate_seo_image_settings_section'
    );

    add_settings_field(
        'override_alt',
        __('Override existing image alt attributes', 'ultimate-seo-image'),
        'usiultimate_seo_image_override_alt_render',
        'usiultimate_ultimate-seo-image-settings',
        'ultimate_seo_image_settings_section'
    );

    add_settings_field(
        'override_title',
        __('Override existing image title attributes', 'ultimate-seo-image'),
        'usiultimate_seo_image_override_title_render',
        'usiultimate_ultimate-seo-image-settings',
        'ultimate_seo_image_settings_section'
    );

    add_settings_field(
        'alt_scheme',
        __('Alt scheme', 'ultimate-seo-image'),
        'ultimate_seo_image_alt_scheme_render',
        'usiultimate_ultimate-seo-image-settings',
        'ultimate_seo_image_settings_section'
    );

    add_settings_field(
        'title_scheme',
        __('Title scheme', 'ultimate-seo-image'),
        'ultimate_seo_image_title_scheme_render',
        'usiultimate_ultimate-seo-image-settings',
        'ultimate_seo_image_settings_section'
    );
	
	add_settings_field(
    'usiultimate_seo_image_alt_title_scheme_callback',
    __('Alt & Title Scheme', 'ultimate-seo-image'),
    'usiultimate_alt_title_scheme_callback_function',
    'usiultimate_ultimate-seo-image-settings',
    'ultimate_seo_image_settings_section'
);
	
	
	

    add_settings_section(
        'ultimate_seo_image_lazyload_section',
        __('Lazy Load Settings', 'ultimate-seo-image'),
        'ultimate_seo_image_lazyload_section_callback',
        'usiultimate_seo-image-lazyload'
    );

    add_settings_field(
        'enable_lazyload',
        __('Enable Lazy Load', 'ultimate-seo-image'),
        'usiultimate_seo_image_enable_lazyload_render',
        'usiultimate_seo-image-lazyload',
        'ultimate_seo_image_lazyload_section'
    );

    add_settings_field(
        'enable_lazyload_acf',
        __('Enable Lazy Load for ACF', 'ultimate-seo-image'),
        'usiultimate_seo_image_enable_lazyload_acf_render',
        'usiultimate_seo-image-lazyload',
        'ultimate_seo_image_lazyload_section'
    );

    add_settings_field(
        'enable_lazyload_styles',
        __('Enable Lazy Load Default Styles', 'ultimate-seo-image'),
        'usiultimate_seo_image_enable_lazyload_styles_render',
        'usiultimate_seo-image-lazyload',
        'ultimate_seo_image_lazyload_section'
    );

    add_settings_field(
        'lazyload_threshold',
        __('Threshold', 'ultimate-seo-image'),
        'usiultimate_seo_image_lazyload_threshold_render',
        'usiultimate_seo-image-lazyload',
        'ultimate_seo_image_lazyload_section'
    );
	
	  add_settings_field(
    'usiultimate_seo_image_lazyload_callback',
    __('Lazy Load Callback', 'ultimate-seo-image'),
    'usiultimate_lazyload_callback_function',
    'usiultimate_seo-image-lazyload',
    'ultimate_seo_image_lazyload_section'
);

}

// Sanitize options
function ultimate_seo_image_options_sanitize($input)
{
    $sanitized_input = array();

    // Default values
    $defaults = array(
        'optimize_img' => 'all',
        'sync_method' => 'both',
        'override_alt' => 0,
        'override_title' => 0,
        'alt_scheme' => '%name - %title',
        'title_scheme' => '%title',
        'enable_lazyload' => 0,
        'enable_lazyload_acf' => 0,
        'enable_lazyload_styles' => 0,
        'lazyload_threshold' => 0,
    );

    // Sanitize each input and use default if not set
    $sanitized_input['optimize_img'] = isset($input['optimize_img']) ? sanitize_text_field($input['optimize_img']) : $defaults['optimize_img'];
    $sanitized_input['sync_method'] = isset($input['sync_method']) ? sanitize_text_field($input['sync_method']) : $defaults['sync_method'];
    $sanitized_input['override_alt'] = isset($input['override_alt']) ? absint($input['override_alt']) : $defaults['override_alt'];
    $sanitized_input['override_title'] = isset($input['override_title']) ? absint($input['override_title']) : $defaults['override_title'];
    $sanitized_input['alt_scheme'] = isset($input['alt_scheme']) ? sanitize_text_field($input['alt_scheme']) : $defaults['alt_scheme'];
    $sanitized_input['title_scheme'] = isset($input['title_scheme']) ? sanitize_text_field($input['title_scheme']) : $defaults['title_scheme'];
    $sanitized_input['enable_lazyload'] = isset($input['enable_lazyload']) ? absint($input['enable_lazyload']) : $defaults['enable_lazyload'];
    $sanitized_input['enable_lazyload_acf'] = isset($input['enable_lazyload_acf']) ? absint($input['enable_lazyload_acf']) : $defaults['enable_lazyload_acf'];
    $sanitized_input['enable_lazyload_styles'] = isset($input['enable_lazyload_styles']) ? absint($input['enable_lazyload_styles']) : $defaults['enable_lazyload_styles'];
    $sanitized_input['lazyload_threshold'] = isset($input['lazyload_threshold']) ? absint($input['lazyload_threshold']) : $defaults['lazyload_threshold'];

    return $sanitized_input;
}

function ultimate_seo_image_section_callback()
{
    echo '<p><b>' . esc_html__('Settings', 'ultimate-seo-image') . '</b></p>';
}

function ultimate_seo_image_lazyload_section_callback()
{
    echo '<p><b>' . esc_html__('Lazy Load Settings', 'ultimate-seo-image') . '</b></p>';
}


// Render functions for the fields
function usiultimate_seo_image_optimize_img_render()
{
    $options = get_option('ultimate_seo_image_options');
    $optimize_img = isset($options['optimize_img']) ? $options['optimize_img'] : 'all'; // Check if the key exists
    ?>
    <select name="ultimate_seo_image_options[optimize_img]">
        <option value="all" <?php selected($optimize_img, 'all'); ?>><?php esc_html_e('Post Thumbnails & Content Images (recommended)', 'ultimate-seo-image'); ?></option>
        <option value="thumbs" <?php selected($optimize_img, 'thumbs'); ?>><?php esc_html_e('Only Post Thumbnails', 'ultimate-seo-image'); ?></option>
        <option value="post" <?php selected($optimize_img, 'post'); ?>><?php esc_html_e('Only images in post content', 'ultimate-seo-image'); ?></option>
    </select>
    <?php
}

function usiultimate_seo_image_sync_method_render()
{
    $options = get_option('ultimate_seo_image_options');
    $sync_method = isset($options['sync_method']) ? $options['sync_method'] : 'both'; // Check if the key exists
    ?>
    <select name="ultimate_seo_image_options[sync_method]">
        <option value="both" <?php selected($sync_method, 'both'); ?>><?php esc_html_e('Alt <=> Title (recommended)', 'ultimate-seo-image'); ?></option>
        <option value="alt" <?php selected($sync_method, 'alt'); ?>><?php esc_html_e('Alt => Title', 'ultimate-seo-image'); ?></option>
        <option value="title" <?php selected($sync_method, 'title'); ?>><?php esc_html_e('Alt <= Title', 'ultimate-seo-image'); ?></option>
    </select>
    <?php
}

function usiultimate_seo_image_override_alt_render()
{
    $options = get_option('ultimate_seo_image_options');
    $override_alt = isset($options['override_alt']) ? $options['override_alt'] : 0; // Check if the key exists
    ?>
    <input type="checkbox" name="ultimate_seo_image_options[override_alt]" value="1" <?php checked($override_alt, 1); ?>>
    <?php
}

function usiultimate_seo_image_override_title_render()
{
    $options = get_option('ultimate_seo_image_options');
    $override_title = isset($options['override_title']) ? $options['override_title'] : 0; // Check if the key exists
    ?>
    <input type="checkbox" name="ultimate_seo_image_options[override_title]" value="1" <?php checked($override_title, 1); ?>>
    <?php
}

function ultimate_seo_image_alt_scheme_render()
{
    $options = get_option('ultimate_seo_image_options');
    $alt_scheme = isset($options['alt_scheme']) ? $options['alt_scheme'] : ''; // Check if the key exists
    ?>
    <input type="text" name="ultimate_seo_image_options[alt_scheme]" value="<?php echo esc_attr($alt_scheme); ?>">
    <?php
}

function ultimate_seo_image_title_scheme_render()
{
    $options = get_option('ultimate_seo_image_options');
    $title_scheme = isset($options['title_scheme']) ? $options['title_scheme'] : ''; // Check if the key exists
    ?>
    <input type="text" name="ultimate_seo_image_options[title_scheme]" value="<?php echo esc_attr($title_scheme); ?>">
    <?php
}

function usiultimate_seo_image_enable_lazyload_render()
{
    $options = get_option('ultimate_seo_image_options');
    $enable_lazyload = isset($options['enable_lazyload']) ? $options['enable_lazyload'] : 0; // Check if the key exists
    ?>
    <input type="checkbox" name="ultimate_seo_image_options[enable_lazyload]" value="1" <?php checked($enable_lazyload, 1); ?>>
    <?php
}

function usiultimate_seo_image_enable_lazyload_acf_render()
{
    $options = get_option('ultimate_seo_image_options');
    $enable_lazyload_acf = isset($options['enable_lazyload_acf']) ? $options['enable_lazyload_acf'] : 0; // Check if the key exists
    ?>
    <input type="checkbox" name="ultimate_seo_image_options[enable_lazyload_acf]" value="1" <?php checked($enable_lazyload_acf, 1); ?>>
    <?php
}

function usiultimate_seo_image_enable_lazyload_styles_render()
{
    $options = get_option('ultimate_seo_image_options');
    $enable_lazyload_styles = isset($options['enable_lazyload_styles']) ? $options['enable_lazyload_styles'] : 0; // Check if the key exists
    ?>
    <input type="checkbox" name="ultimate_seo_image_options[enable_lazyload_styles]" value="1" <?php checked($enable_lazyload_styles, 1); ?>>
    <?php
}

function usiultimate_seo_image_lazyload_threshold_render()
{
    $options = get_option('ultimate_seo_image_options');
    $lazyload_threshold = isset($options['lazyload_threshold']) ? $options['lazyload_threshold'] : 0; // Check if the key exists
    ?>
    <input type="number" name="ultimate_seo_image_options[lazyload_threshold]" value="<?php echo esc_attr($lazyload_threshold); ?>">
    <p class="description"><?php esc_html_e('By default, images are only loaded when the user scrolls to them and they become visible on the screen (default value for this field is 0). If you want your images to load earlier than that, let\'s say 200px then you need to type in 200.', 'ultimate-seo-image'); ?></p>
    <?php
}


// Callback function for shortcode example
function usiultimate_lazyload_callback_function()
{
    echo '<div class="usi-custom-message code">';
    echo '<p><code>&lt;img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="<strong>' . esc_html(__('REAL SRC HERE', 'ultimate-seo-image')) . '</strong>" class="ultimate-seo-lazy" /&gt;</code></p>';
    echo '</div>';
}


// Callback function for the alt & title scheme example
function usiultimate_alt_title_scheme_callback_function()
{
    ?>
    <div class="csf-notice csf-notice-warning">
        <strong>Possible Variables for alt &amp; title scheme:</strong><br><br>
        <strong>%title</strong> - Replaces post title<br>
        <strong>%desc</strong> - Replaces post excerpt<br>
        <strong>%name</strong> - Replaces image filename (without extension)<br>
        <strong>%category</strong> - Replaces post category<br>
        <strong>%tags</strong> - Replaces post tags<br>
        <strong>%media_title</strong> - Replaces attachment title (could be empty if not set)<br>
        <strong>%media_alt</strong> - Replaces attachment alt-text (could be empty if not set)<br>
        <strong>%media_caption</strong> - Replaces attachment caption (could be empty if not set)<br>
        <strong>%media_description</strong> - Replaces attachment description (could be empty if not set)
    </div>
    <?php
}


// Enqueue CSS/JavaScript for tab functionality
add_action('admin_enqueue_scripts', 'ultimate_seo_image_admin_scripts');
function ultimate_seo_image_admin_scripts($hook)
{
    if ($hook != 'toplevel_page_ultimate-seo-image') {
        return;
    }
    wp_enqueue_script('ultimate-seo-image-tabs-js', plugin_dir_url(__FILE__) . '../assets/js/tabs.js', array('jquery'), null, true);
	wp_enqueue_style('ultimate-seo-image-admin-css', plugin_dir_url(__FILE__) . '../assets/css/admin.css');
}

// Render the settings page with tabs and logo
function ultimate_seo_image_page()
{
    ?>
    <div class="wrap">
        <h1>
            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/img/frameworklogo.png'); ?>" alt="<?php esc_attr_e('Logo', 'ultimate-seo-image'); ?>" style="vertical-align: middle; margin-right: 10px;">
         
        </h1>
        <h2 class="nav-tab-wrapper">
            <a href="#settings" class="nav-tab nav-tab-active"><?php esc_html_e('Settings', 'ultimate-seo-image'); ?></a>
            <a href="#lazy-load-settings" class="nav-tab"><?php esc_html_e('Lazy Load Settings', 'ultimate-seo-image'); ?></a>
        </h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('ultimate_seo_image_options');
            ?>
            <div id="settings" class="tab-content">
                <?php
                do_settings_sections('usiultimate_ultimate-seo-image-settings');
                ?>
            </div>
            <div id="lazy-load-settings" class="tab-content" style="display: none;">
                <?php
                do_settings_sections('usiultimate_seo-image-lazyload');
                ?>
            </div>
            <?php
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
