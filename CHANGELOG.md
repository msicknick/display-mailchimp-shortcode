# Changelog
All notable changes to this project will be documented in this file.


## [1.2.0] - 2019-03-19
### Added
- get_fetch_url($type) function that takes either 'single' or 'all' to retrieve the appropriate URL to call

### Changed
- default 'count' value from 8 to 10
- fetch_campaigns($url) to be more generic by accepting a URL from a different function
- curl calls to WordPress HTTP API calls as per plugin standards

### Removed
- sprintf() in get_pagination() function
- fetch_campaign() function for fetching single campaign


## [1.1.0] - 2019-03-18
### Added
- EP_PERMALINK to add_rewrite_endpoint
- $this->total_items to get set in fetch_campaigns()
- comments to explain certain pieces of code

### Changed
- use of brackets to endif; and endwhile; for clearer code
- archive_pagination() to get_pagination()
- Pagnation function to use elipsis, and 'Previous' and 'Next' for better display of large amounts of pages


## [1.0.0] - 2019-03-17
### Initial release

[1.2.0]: https://github.com/msicknick/display-mailchimp-shortcode/compare/v1.2.0...HEAD
[1.1.0]: https://github.com/msicknick/display-mailchimp-shortcode/compare/v1.1.0...v1.2.0
[1.0.0]: https://github.com/msicknick/display-mailchimp-shortcode/compare/v1.0.0...v1.1.0