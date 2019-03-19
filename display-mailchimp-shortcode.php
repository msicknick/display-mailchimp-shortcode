<?php

/*
 *  Plugin Name:  Display Mailchimp
 *  Plugin URI:   
 *  Description:  Shortcode to display Mailchimp campaigns
 *  Version:      1.0.0
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
    
    const VERSION = '1.0.0'; 
    
    public function __construct() {
        
            // Register shortcode        
            add_shortcode( 'display-mailchimp', array($this, 'display_mailchimp_shrotcode') );

            // Add custom query variable
            add_filter( 'query_vars', array($this, 'add_query_vars') );
            
            // Add custom endpoint for single campaigns
            add_action( 'init', array($this, 'add_endpoint') );
    }

    /**
     * Add custom query vars
     * 
     * @since   1.0.0
     * 
     * @param   array   $vars The array of available query variables
     * @link    https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
     */
    function add_query_vars($vars) {
            $vars[] = 'campaign';
            return $vars;
    }

    /**
     * Add custom endpoint
     *
     * @since   1.0.0
     * 
     * @param   array   $vars The array of available query variables
     * @link    https://codex.wordpress.org/Rewrite_API/add_rewrite_endpoint
     */
    function add_endpoint() {
            add_rewrite_endpoint('campaign', EP_PAGES);
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
     * Helper function to add pagination
     * 
     * @since   1.0.0
     * 
     * @return  string  $output HTML output for pagination
     */
    public function archive_pagination() {
            global $post;

            $link = get_permalink($post->ID);        

            $output = '<nav class="navigation paging-navigation" role="navigation">';
            $output .= '<div class="page-links">';

             for($i = 1; $i <= $this->max_page; $i++):
                 if($this->page === $i):

                 endif;
                $output .= sprintf('<a class="page-numbers%s" href="%s">%s</a>',
                    ($this->page === $i ? ' current' : ''),
                    ($i === 1 ? $link : $link .$i.'/'),
                    $i
                );
            endfor;

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
               'count' => 8,
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

           if(count($api) != 2) {
               return;
           }

           // Set apikey to its actual value
           $wporg_atts['apikey'] = $api[0];
           // Set region
           $region = $api[1];

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

           if ($this->campaign_id) {

                   if ($campaign = $this->fetch_campaign()) {

                       $url = $campaign->long_archive_url;

                       $output = "<div style='text-align:center;'><a href='javascript:history.back();'>Go Back</a></div>";
                       $output .= '<object data="' . $url . '" type="text/html" style="height: 840px; width: 100%;">
                                       <embed src="' . $url . '" type="text/html">
                                       <p>It appears you don\'t have a the proper plugin to load this in your browser. You can <a href="' . $url . '">click here to access the resource.</a></p>
                                   </object>';
                   }      

           } else {
               if ($campaigns = $this->fetch_campaigns()) {
                   $pagination = ($this->paged ? $this->archive_pagination() : '');
                   foreach ($campaigns as $campaign) {
                       $list .= sprintf('<li><span class="campaign-date">%s</span> - <a href="%s" title="%s" ><strong>%s</strong></a></li>', 
                               date('F jS, Y', strtotime($campaign->send_time)),
                               self::get_newsletter_link($campaign->id), 
                               esc_attr($campaign->settings->subject_line), 
                               $campaign->settings->subject_line
                       );
                   }
                   $output = $this->get_wrapper($list) . $pagination;
               }
           }

           return $output;              
    }    
    
    /**
     * Fetch single campaign info using Mailchimp's API
     * 
     * @since:  1.0.0
     * @return  array/bool    An array of campaign info or false on failure
     */
    public function fetch_campaign() {
            try {
                if(!$this->apikey) {
                    return;
                }

                $curl = curl_init();

                if ($curl === false) {
                    throw new Exception('Failed to initialize');
                }

                $url = 'https://us14.api.mailchimp.com/3.0/campaigns/' . $this->campaign_id . "?apikey=" . $this->apikey;     

                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_URL => $url,
                ]);

                $resp = curl_exec($curl);

                if ($resp === false) {
                    throw new Exception(curl_error($curl), curl_errno($curl));
                }

                $return = json_decode($resp);

                if($return == false) {
                    throw new Exception(curl_error($curl), curl_errno($curl));
                }            

                curl_close($curl);

            } catch (Exception $ex) {
                $return = false;   
                trigger_error(sprintf(
                        'Curl failed with error #%d: %s', 
                        $ex->getCode(), $ex->getMessage()), 
                        E_USER_ERROR);
            }

            return $return;        
    }
    
    /**
     * Fetch a list of campaigns using Mailchimp's API
     * 
     * @since: 1.0.0
     * @return    array/bool    An array of campaigns or false on failure
     */
    public function fetch_campaigns() {
            try {
                if(!$this->apikey) {
                    return;
                }

                $curl = curl_init();

                if ($curl === false) {
                    throw new Exception('Failed to initialize');
                }

                $qry_str = $this->return_query_string();

                $url = 'https://us14.api.mailchimp.com/3.0/campaigns' . $qry_str;


                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_URL => $url,
                ]);

                $resp = curl_exec($curl);

                if ($resp === false) {
                    throw new Exception(curl_error($curl), curl_errno($curl));
                }

                $result = json_decode($resp);

                if($result == false) {
                    throw new Exception(curl_error($curl), curl_errno($curl));
                }

                $numpages = ceil($result->total_items / $this->count);

                $this->offset += $numpages;
                if (is_null($this->max_page)) {
                    $this->max_page = ceil($result->total_items / $this->count);
                }

                $return = $result->campaigns;

                curl_close($curl);

            } catch (Exception $ex) {
                $return = false;   
                trigger_error(sprintf(
                        'Curl failed with error #%d: %s', 
                        $ex->getCode(), $ex->getMessage()), 
                        E_USER_ERROR);
            }

            return $return;        
    }
     
    
} 