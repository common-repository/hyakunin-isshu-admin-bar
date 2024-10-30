=== Hyakunin Isshu Admin Bar ===
Contributors: Katsushi Kawamori
Donate link: https://shop.riverforest-wp.info/donate/
Tags: admin bar, poem, notice
Requires at least: 5.0
Requires PHP: 8.0
Tested up to: 6.6
Stable tag: 1.13
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin that randomly displays Hyakunin Isshu in the admin bar.

== Description ==

= Items =
* Tanka : Hyakunin Isshu
* Tanka Author
* Source of Tanka
* Subject of Tanka

= Display =
* Displayed at 60-second intervals.

= Data =
* The following data for the "Hyakunin Isshu" are used.
* [Hyakunin Isshu data](http://www2e.biglobe.ne.jp/shinzo/shiryou/misc/benri.html)

= Filter hooks =
~~~
/** ==================================================
 * Filter for capability.
 *
 */
add_filter( 'hyakunin_isshu_bar_user_can', function(){ return 'read'; }, 10, 1 );
~~~
~~~
/** ==================================================
 * Filter for interval.
 *
 */
add_filter( 'hyakunin_isshu_bar_interval_sec', function(){ return 5; }, 10, 1 );
~~~
~~~
/** ==================================================
 * Filter for Tanka array.
 *
 */
add_filter(
	'hyakunin_isshu_bar_array',
	function() {
		$tanka_arr = array(
			array(
				'tanka' => 'Notice of Drinking Party 1 !',
				'author' => 'Katsushi',
				'source' => 'February 22, 2023, 7:00 p.m. - 9:00 p.m.',
				'subject' => 'Restaurant WP',
			),
			array(
				'tanka' => 'Notice of Drinking Party 2 !',
				'author' => 'kawamori',
				'source' => 'March 22, 2023, 7:00 p.m. - 9:00 p.m.',
				'subject' => 'Sushi bar WP',
			),
		);

		return $tanka_arr;
	},
	10,
	1
);
~~~
~~~
/** ==================================================
 * Filter for Tanka Author title.
 *
 */
add_filter( 'hyakunin_isshu_bar_author_title', function(){ return 'Banquet Director'; }, 10, 1 );
~~~
~~~
/** ==================================================
 * Filter for Source title.
 *
 */
add_filter( 'hyakunin_isshu_bar_source_title', function(){ return 'Date and Time'; }, 10, 1 );
~~~
~~~
/** ==================================================
 * Filter for Subject title.
 *
 */
add_filter( 'hyakunin_isshu_bar_subject_title', function(){ return 'Place'; }, 10, 1 );
~~~

== Installation ==

1. Upload `hyakunin-isshu-admin-bar` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

none

== Screenshots ==

1. Admin bar view
2. Admin bar view( Filter hook applied )

== Changelog ==

= 1.13 =
Supported WordPress 6.4.
PHP 8.0 is now required.

= 1.12 =
Fixed css.

= 1.11 =
Style modified.

= 1.10 =
The information is now displayed at 60-second intervals.
Filter hooks have been changed and added.

= 1.02 =
Fixed translation.

= 1.01 =
Fixed translation.

= 1.00 =
Initial release.

== Upgrade Notice ==

= 1.00 =

