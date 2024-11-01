<?php
/* Security-Check */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ultimateimage_cache
{
    protected $status;
    protected $expire;

    protected $prefix = 'ultimateimage_cache_';

    public function __construct($status=false, $expire=3600)
    {
        $this->status = $status;
        $this->expire = $expire;
    }

    /**
     * Get content from transient cache
     *
     * @param $key
     * @return bool|mixed
     */
    public function get_cache( $key )
    {
        if( $this->status !== true )
            return false;

        $cache = get_transient($this->prefix.$key);

        if( $cache ) {
            return $cache;
        }

        return false;
    }

    /**
     * Set content for transient cache
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function set_cache($key, $value)
    {
        if( $this->status !== true )
            return false;

        return set_transient($this->prefix.$key, $value, $this->expire);
    }

    /**
     * Delete a transient
     *
     * @param $key
     * @return bool
     */
    public function delete_cache($key)
    {
        return delete_transient($this->prefix.$key);
    }

    /**
     * Clear transients for a specific post
     *
     * @param $post_ID
     * @return int
     */
   public function clear_post_cache($post_ID)
{
    if (!is_numeric($post_ID)) {
        return false;
    }

    global $wpdb;

    // Prepare the LIKE pattern
    $like_pattern = $wpdb->esc_like('_transient_' . $this->prefix . 'post_' . $post_ID) . '%';
    $query = $wpdb->prepare(
        "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE %s",
        $like_pattern
    );

    // Execute the prepared query
    $results = $wpdb->get_results($query, OBJECT);

    $deleted = 0;

    if ($results) {
        foreach ($results as $result) {
            $key = str_replace('_transient_' . $this->prefix, '', $result->option_name);
            $this->delete_cache($key);

            $deleted++;
        }
    }

    return $deleted;
}


    /**
     * Clear site cache
     *
     * @return int
     */
    public function clear_cache()
    {
        global $wpdb;

					$like_pattern = $wpdb->esc_like( '_transient_' . $this->prefix . 'post_' ) . '%';
					$query = $wpdb->prepare(
						"SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE %s",
						$like_pattern
					);
					$results = $wpdb->get_results( $query, OBJECT );


        $deleted = 0;

        if( $results ) {
            foreach($results as $result) {
                $key = str_replace('_transient_'.$this->prefix, '', $result->option_name);
                $this->delete_cache( $key );
                $deleted++;
            }
        }

        return $deleted;
    }
}