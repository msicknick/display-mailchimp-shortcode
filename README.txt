=== Display MailChimp ===
Contributors: MSicknick
Tags: mailchimp, shortcode
Requires at least: 4.0
Tested up to: 5.1.1
Requires PHP: 7.3
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Shortcode to display Mailchimp campaigns.

== Description ==
 This plugin can be used by placing the [display-mailchimp] shortcode in a post or page.
 A required parameter is an api key from MailChimp. Instructions how to create your api key are [here](https://mailchimp.com/help/about-api-keys/)
 Other parameters than can be used are: 
  - campaign_id: if set, pulls details about a campaign
  - offset: number of campaigns to offset the search (default: 0)
  - count: number of campaigns to show (default: 10)
  - sort_field: soft files by specific field (default: send_time; possible: create_time, send_time)
  - list_id: the unique id for the list
  - status: status of the campaign (default: sent; possible: save, paused, schedule, sending, sent)
  - layout: a way to integrate the shortcode to display items matched with the current wordpress theme (default: list)
  - paged: whether or not to show pagnation (default: true)

== Installation ==
 Upload the Display MailChimp to your plugin directory and activate it.

== Changelog ==
 = Version 1.2.0 (03/19/2019) =
 * Added get_fetch_url($type) function that takes either 'single' or 'all' to retrieve the appropriate URL to call
 * Changed default 'count' value from 8 to 10
 * Changed fetch_campaigns($url) to be more generic by accepting a URL from a different function
 * Changed curl calls to WordPress HTTP API calls as per plugin standards
 * Removed sprintf() in get_pagination() function
 * Removed fetch_campaign() function for fetching single campaign

 = Version 1.1.0 (03/18/2019) =
 * Added EP_PERMALINK to add_rewrite_endpoint
 * Added $this->total_items to get set in fetch_campaigns()
 * Added comments to explain certain pieces of code
 * Changed use of brackets to endif; and endwhile; for clearer code
 * Changed archive_pagination() to get_pagination()
 * Changed pagnation function to use elipsis, and 'Previous' and 'Next' for better display of large amounts of pages
 
 = Version 1.0.0 (03/17/2019) =
 * Initial release