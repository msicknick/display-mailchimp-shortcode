<?php

/*
 *  Plugin Name:  Display Mailchimp
 *  Plugin URI:   
 *  Description:  Shortcode to display Mailchimp campaigns
 *  Version:      1.1.0
 *  Author:       Magda Sicknick
 *  Author URI:   https://www.magdaicknick.com/
 *  License:      GPLv3
 *  License URI:  https://www.gnu.org/licenses/gpl-3.0.html
 *  Text Domain:  display-mailchimp-shortcode
 */

/**
 * Exit if accessed directly
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * DEFINE CONSTANTS
 */
define('DMCS_PATH', plugin_dir_path(__FILE__));
define('DMCS_URL', plugin_dir_url(__FILE__));
define('DMCS_GITHUB_URL', 'https://github.com/msicknick/');

/**
 * Initialize class
 */
new Display_Mailchimp_Shortcode();


class Display_Mailchimp_Shortcode {
    private $_args, $max_page;
    
    const VERSION = '1.1.0'; 
    
    public function __construct() {
        
            // Register shortcode        
            add_shortcode( 'display-mailchimp', array($this, 'display_mailchimp_shrotcode') );

            // Add custom query variable
            add_filter( 'query_vars', array($this, 'add_query_vars') );
            
            // Add custom endpoint for single campaigns
            add_action('init', array($this, 'add_endpoint'));
    }

    /**
     * Add custom query vars
     * 
     * @since   1.0.0
     * 
     * @param   array   $vars The array of available query variables
     * @link    https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
     * @return  array   $vars
     */
    function add_query_vars($vars) {
            $vars[] = 'campaign';
            return $vars;
    }

    /**
     * Add custom endpoint
     *
     * @since   1.0.0
     * @since   1.1.0   Added EP_PERMALINK 
     * 
     * @param   array   $vars The array of available query variables
     * @link    https://codex.wordpress.org/Rewrite_API/add_rewrite_endpoint
     */
    function add_endpoint() {
            add_rewrite_endpoint('campaign', EP_PAGES | EP_PERMALINK );
    }

    /**
     * Helper function to get wrapper for data
     * 
     * @since   1.0.0
     * 
     * @param   string  $list HTML output to be wrapped
     * @return  string
     */
    public function get_wrapper($list) {
            $output = '<ul class="display-mailchimp">';
            $output .= $list;
            $output .= '</ul>';

            return $output;
    }
    
    /**
     * Helper function to get properly formatted query string
     * 
     * @since   1.0.0
     * 
     * @return  string 
     */
    public function return_query_string() {
            $query_string = http_build_query($this->_args) . "\n";
            $query_string = http_build_query($this->_args, '', '&');

            return "?" . $query_string;
    }

    /**
     * Format pagination links with an elipsis and and 'Previous' and 'Next' buttons
     * 
     * @since 1.1.0
     *
     * @param    int     $current_page - current page
     * @param    int     $item_count - number of items to paginate, used to calculate total number of pages
     * @param    int     $per_page_count - number of items per page, used to calculate total number of pages
     * @param    int     $adjacent_count - half the number of page links displayed adjacent to the current page
     * @param    string  $page_link - pagination URL string containing %d placeholder or a callable function that accepts page number and returns page URL
     * @param    boolean $show_prev_next - whether to show previous and next page links
     * @return   string  $output HTML output
     */
    public function get_pagination($current_page, $item_count, $per_page_count, $adjacent_count = 3, $show_prev_next = true) {
            global $post;
            $page_link = get_permalink($post->ID);
              
            $first_page = 1;
            $last_page  = ceil($item_count / $per_page_count);

            if ($last_page == 1) {
                return;
            }

            if ($current_page <= $adjacent_count + $adjacent_count) {
                $first_adjacent = $first_page;
                $last_adjacent  = min($first_page + $adjacent_count + $adjacent_count, $last_page);
            } elseif ($current_page > $last_page - $adjacent_count - $adjacent_count) {
                $last_adjacent  = $last_page;
                $first_adjacent = $last_page - $adjacent_count - $adjacent_count;
            } else {
                $first_adjacent = $current_page - $adjacent_count;
                $last_adjacent  = $current_page + $adjacent_count;
            }       

            $output = '<nav class="navigation paging-navigation" role="navigation">';
            $output .= '<div class="page-links">';
            
            if ($show_prev_next):
                if ($current_page != $first_page):        
                    $output .= '<a class="page-numbers" href="' . ($page_link . $current_page - 1) . '">&#171;</a>';
                endif;
            endif;
            
            if ($first_adjacent > $first_page):
                $output .= '<a class="page-numbers" href="' . ($page_link . $first_page) . '">' . $first_page . '</a>';
                if ($first_adjacent > $first_page + 1) {
                    $output .= '<span class="page-numbers">...</span>';
                }
            endif;
            
            for ($i = $first_adjacent; $i <= $last_adjacent; $i++):
                if ($current_page == $i) {
                    $output .= '<span class="page-numbers current">' . $i . '</span>';
                } else {
                    $output .= '<a class="page-numbers" href="' . ($page_link . $i) . '">' . $i . '</a>';
                }
            endfor;
            
            
            if ($last_adjacent < $last_page):
                if ($last_adjacent < $last_page - 1):
                    $output .= '<span class="page-numbers">...</span>';
                endif;
                $output .= '<a class="page-numbers" href="' . ($page_link . $last_page) . '">' . $last_page . '</a>';
            endif;
            
            if ($show_prev_next):
                if ($current_page != $last_page):           
                    $output .= '<a class="page-numbers" href="' . ($page_link . $current_page + 1) . '">&#187;</a>';
                endif;
            endif;

            $output .= '</div>';
            $output .= '</nav>';

            return $output;
   }


    /**
     * Helper function to get direct newsletter link
     * 
     * @since   1.0.0
     * 
     * @param   string  $campaign_id
     * @return  string  $output url
     */
    public function get_newsletter_link($campaign_id) {
            global $post;
            $link = get_permalink($post->ID);

            $output = ($link . 'campaign/' . $campaign_id . '/');

            return $output;
    }
    
     /**
     * Helper function to get URL for API to call
     * 
     * @since   1.1.0
     * 
     * @param   string  $type   `single` or `all`
     * @return  string  $url
     */
    public function get_fetch_url($type) {
            $url = '';
            if ($type == 'single') {
                $url = 'https://us14.api.mailchimp.com/3.0/campaigns/' . $this->campaign_id . "?apikey=" . $this->apikey;
            } else if ($type == 'all') {
                $qry_str = $this->return_query_string();
                $url = 'https://us14.api.mailchimp.com/3.0/campaigns' . $qry_str;
            }

            return $url;
    }
    
    

    /**
     * Function that establishes the shortcode and sets parameters
     * 
     * @since   1.0.0
     * 
     * @param   array   $atts
     * @param   array   $content
     * @param   string  $tag
     * @return  string  $output url
     */
    public function display_mailchimp_shrotcode($atts = [], $content = null, $tag = '') {        

            // Normalize attribute keys, lowercase
           $atts = array_change_key_case((array) $atts, CASE_LOWER);

           // Override default attributes with user attributes
           $wporg_atts = shortcode_atts([
               'apikey' => false,
               'offset' => 0,
               'count' => 10,
               'sort_field' => 'send_time',
               'sort_dir' => 'DESC',
               'list_id' => false,
               'status' => 'sent',
               'campaign_id' => false,
               'layout' => 'list',
               'paged' => true,
                   ], $atts, $tag);

           // Split api key to get region ID
           $api = explode("-", $wporg_atts['apikey']);

           // Check to make sure the api value has two parts
           if(count($api) != 2) {
               return;
           }

           // Set apikey to its actual value
           $wporg_atts['apikey'] = $api[0];
           // Set region
           $region = $api[1];
           // Set page number
           $this->page = get_query_var('page') ? get_query_var('page') : 1;

           $wporg_atts['offset'] = (($this->page - 1) * $wporg_atts['count']);      

           $this->_args = $wporg_atts;

           // Set values to be used throughout the class
           $this->apikey                = sanitize_text_field($wporg_atts['apikey']);
           $this->region                = sanitize_text_field($region);
           $this->offset                = intval($wporg_atts['offset']);
           $this->count                 = intval($wporg_atts['count']);
           $this->sort_field            = sanitize_text_field($wporg_atts['sort_field']);
           $this->list_id               = sanitize_text_field($wporg_atts['list_id']);
           $this->status                = sanitize_text_field($wporg_atts['status']);
           $this->campaign_id           = sanitize_text_field($wporg_atts['campaign_id']);
           $this->layout                = sanitize_text_field($wporg_atts['layout']);
           $this->paged                 = sanitize_text_field($wporg_atts['paged']);

           $output = '';
           $list = '';

           $this->campaign_id = get_query_var('campaign') ? get_query_var('campaign') : $this->campaign_id;

           if ($this->campaign_id):                
                if ($campaign = $this->fetch_campaigns(self::get_fetch_url('single'))):
                    // Get single campaign's URL
                    $url = $campaign->long_archive_url;
                    // Generate output object
                    $output = "<div style='text-align:center;'><a href='javascript:history.back();'>Go Back</a></div>";
                    $output .= '<object data="' . $url . '" type="text/html" style="height: 840px; width: 100%;">
                                    <embed src="' . $url . '" type="text/html">
                                    <p>It appears you don\'t have a the proper plugin to load this in your browser. You can <a href="' . $url . '">click here to access the resource.</a></p>
                                </object>';
                endif;    

           else:
               if ($campaigns_res = $this->fetch_campaigns(self::get_fetch_url('all'))):
                   
                    // Set total items
                    $this->total_items = $campaigns_res->total_items;

                    $numpages = ceil($this->total_items / $this->count);

                    $this->offset += $numpages;
                    if (is_null($this->max_page)) {
                        $this->max_page = ceil($this->total_items / $this->count);
                    }

                    $campaigns = $campaigns_res->campaigns;

                    $pagination = ($this->paged ? $this->get_pagination($this->page, $this->total_items, $this->count) : '');
                    foreach ($campaigns as $campaign):
                       $list .= sprintf('<li><span class="campaign-date">%s</span> - <a href="%s" title="%s" ><strong>%s</strong></a></li>', 
                               date('F jS, Y', strtotime($campaign->send_time)),
                               self::get_newsletter_link($campaign->id), 
                               esc_attr($campaign->settings->subject_line), 
                               $campaign->settings->subject_line
                       );
                   endforeach;
                   $output = $this->get_wrapper($list) . $pagination;
              endif;
           endif;

           return $output;              
    }    
    
    /**
     * Fetch campaigns or a single campaign using Mailchimp's API
     * 
     * @since:  1.0.0
     * @return  array/bool    An array of campaign info or false on failure
     */
    public function fetch_campaigns($url) {
        
            try {
                $response = wp_remote_get( $url );
                
                // Check the response code
                $response_code = wp_remote_retrieve_response_code($response);
                $response_message = wp_remote_retrieve_response_message($response);

                if (200 != $response_code && !empty($response_message)) {
                    throw new Exception('Failed to fetch URL', $response_code . ": " .$response_message);
                } elseif (200 != $response_code) {
                    throw new Exception('Failed to fetch URL - Unknown error occurred', $response_code . ": " . $response_message);
                }

                $body = wp_remote_retrieve_body($response);

                // Translate into an object
                $return   = json_decode($body);

            } catch (Exception $ex) {
                $return = false;   
                trigger_error($ex->getMessage() . $ex->getCode(), E_USER_ERROR);
            }         

            return $return;        
    }
    
} 