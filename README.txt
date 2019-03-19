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
  - offset: number of campaigns to offset the search [default: 0]
  - count: number of campaigns to show [default: 8]
  - sort_field: soft files by specific field [default: send_time; possible: create_time, send_time]
  - list_id: the unique id for the list
  - status: status of the campaign [default: sent; possible: save, paused, schedule, sending, sent]

== Installation ==
 Upload the Display MailChimp to your plugin directory and activate it.

== Changelog ==
 = Version 1.0.0 (03/17/2019) =
 * Initial release