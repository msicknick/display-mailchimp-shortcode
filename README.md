# Display Mailchimp

This WordPress plugin can be used by placing the `[display-mailchimp]` shortcode in a post or page to show a list of Mailchimp campaigns.
A required parameter is an API key from Mailchimp. Instructions how to create your api key can be found [here](https://mailchimp.com/help/about-api-keys/)

## How to Install

### From this repository
Go to the [releases](https://github.com/msicknick/display-mailchimp-shortcode/releases) section of the repository and download the most recent release.

Then, from your WordPress administration panel, go to `Plugins > Add New` and click the `Upload Plugin` button on the top of the page.

## How to Use
Begin by adding the following shortcode on a page/post:
```
[display-posts apikey='{YOUR_API_KEY}']
```
To filter your results, you can add other parameters such as
```
[display-posts apikey='{YOUR_API_KEY}' count='20' list_id='{YOUR_LIST_ID}']
```

### Available parameters
* **apikey**: **REQUIRED** [Mailchimp API Documentation](https://mailchimp.com/help/about-api-keys/)
* **campaign_id**: if set, pulls details about a campaign
* **offset**: number of campaigns to offset the search *(default: 0)*
* **count**: number of campaigns to show *(default: 10)*
* **sort_field**: soft files by specific field *(default: send_time; possible: create_time, send_time)*
* **list_id**: the unique id for the list
* **status**: status of the campaign *(default: sent; possible: save, paused, schedule, sending, sent)*
* **paged**: whether or not to show pagnation *(default: true)*

## License
Display Mailchimp WordPress Plugin is licensed under the [GNU General Public License](https://www.gnu.org/licenses/gpl-2.0.html) and is available for free.