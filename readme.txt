=== Z Text Upfunker ===
Contributors: zodannl, martenmoolenaar
Donate link: https://www.buymeacoffee.com/zodan
Tags: Text, animation, theme design, theme development, development
Requires at least: 5.5
Tested up to: 6.8
Description: Funking up your texts by selecting html elements in your theme and assigning animation styles.
Version: 0.1.4
Stable tag: 0.1.4
Author: Zodan
Author URI: https://zodan.nl
Text Domain: z-text-upfunker
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Funking up your texts by selecting html elements in your theme and assigning animation styles.

== Description ==

To funk up some headings on a website we made, we created a CSS-animation plugin (using a bit of JavaScript (no jQuery, we’re keeping the funk pure)) with a bunch of funky effects.
Since we like them so much, we would like you to have it.
So here it is.


= What does it do? =

It lets you animate elements on your page by
* Selecting the elements using css selectors
* Selecting the desired animation style (or just randomly picked)
* Entering the maximum number of loops.

This plugin is under active development. Any feature requests are welcome at [plugins@zodan.nl](plugins@zodan.nl)!



== Installation ==

= Install the Text Upfunker from within WordPress =

1. Visit the plugins page within your dashboard and select ‘Add New’;
1. Search for ‘Z Text Upfunker’;
1. Activate the plugin from your Plugins page;
1. Go to ‘after activation’ below.

= Install manually =

1. Unzip the Text Upfunker zip file
2. Upload the unzipped folder to the /wp-content/plugins/ directory;
3. Activate the plugin through the ‘Plugins’ menu in WordPress;
4. Go to ‘after activation’ below.

= After activation =

1. On the Plugins page in WordPress you will see a 'settings' link below the plugin name;
2. On the Text Upfunker settings page:
**  Add a new item by clicking the "Add item" button
**  Select the element(s) of choice using css selectors
**  Select the animation type (or let the plugin decide)
3. Save your settings and you’re done!


== Frequently asked questions ==

= Can I apply the funky animation to multiple (different) elements? =

Yes, you can (apart from creating multiple items).
By entering multiple selectors, separated by a comma, you can have the animation applied to all those elements.

For example, enter `h1, h2, .someClassName` to apply the animation to all h1 and h2 elements and to all elements with the class "someClassName".


= Which animation types are available? =

Currently you can have the words and characters appear from scrambled code.
Or you can have them: Fade in, Flip in, Sink in, Pop up, Flicker or Circle in.


= Do you have plans to improve the plugin? =

We currently have on our roadmap:
* Add a meta box to the edit screen, so settings can be set per post/page/whatever
* Initialising the UpFunker on scroll (observer-triggered)
* Using data-* configuration for more details.
* Adding custom events (like onStart or onLoopComplete)
* Adding a custom capability to manage which users can change settings
* Adding more animation variations

If you have a feature suggestion, send us an email at [plugins@zodan.nl](plugins@zodan.nl).


== Changelog ==

= 0.1.4 =
* Renamed assets to satisfy WordPress standards

= 0.1.3 =
* Small code changes, adding admin functionality to the main class
* Cleaned up the admin interface, including examples of the animation styles

= 0.1.2 =
* Small security enhancements

= 0.1.1 =
* Loading assets async

= 0.1.0 =
* Very first dev version of this plugin
