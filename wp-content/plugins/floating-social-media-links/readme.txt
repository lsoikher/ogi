=== Floating Social Media Links ===
Contributors: celloexpressions
Tags: floating links, floating, social media, facebook, youtube, twitter, floating frame, links, icons, dynamic, clean, simple, easy, sticky, social, menu
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 1.5.2
License: GPL2

Add a clean and simple floating frame, with social media and/or custom links, to your website/blog.

== Description ==
Floating (Social Media) Links is designed to allow anyone to add a simple floating frame to their wordpress website/blog, which contains icons and links to their social media and/or partners' websites. And before you read too far you should know that I'm among those who strongly dislike "floating" animations, so this plugin does a clean simple "stay-in-place" (sticky) float with no animation for an incredibly clean look. 

The plugin is extremely flexible because of its custom links. In addition to a couple of built-in social media links, you can upload your own icons/logos that link to anything. You could create a small, persistent navigation bar, link to partner sites, link to sites for other parts of your organization, link to any of your social media, or any combination of the above.

On the social media side, the purposes of this plugin are highly customizable options and clean and simple linked icons rather than the cluitter of numerous "share" buttons (although popular "action buttons" can still be included). Instead of simply asking visitors to acknowledge their appreciation of your work (by liking or following), this encourages them to also check out your social media *content*, increasing real social interaction (more so than simply using page "likes" to indicate interaction). Also, as native OS/browser support for "share" features becomes the norm, it is redundant to clutter your site with buttons that share content.

This plugin features a refreshingly user-oriented interface for the admin that scales to users with varying desire for customization. If you want a quick set-up, you can focus on the most commonly used options and pick a frame theme that works with your site. If you like customizing stuff, you can configure the detailed components of everything from cookies to colors to z-indicies. The plugin followsa principles that prevent you from reading through irrelevant options (like frame-hide animation if you've chosen not to let users hide the frame), and hides less frequently used options by default. Easy to use for ANYONE!

For web designers and developers, you may customize which pages the frame appears on with your theme templates, and you can use custom, dynamic php-generated links (see the FAQ for details).

Please feel free to offer any feature suggestions you might have (through the support forums or by <a href="http://celloexpressions.com/dev/contact" target="_blank">contacting me</a>) and I WILL consider them for future releases. Any and all feedback/suggestions, positive or negative, is useful!


== Installation ==
1. Take the easy route and install through the wordpress plugin adder :) OR
1. Download the .zip file and upload the unzipped folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Ensure that the `<?php wp_head(); ?>` and `<?php wp_footer(); ?>` action hooks are in your theme's header.php and footer.php files templates
1. Enter your links, choose appearance settings, and set the first general option to "On Every Page" to activate the frame on your site (or place the custom code in your templates)

== Frequently Asked Questions ==
= I'm seeing a red JavaScript loading error on the settings page and can't access all of the settings! =
Make sure you're running WordPress 3.5 or later. This is now required. Next, make sure your browser has JavaScript enabled (it should). If it still isn't working, another plugin is probably outputing bad code onto my settings page. Try systematically disabling your plugins until the violator is found.

= The frame doesn't show up on my site! =
First, make sure that the first general option is set to show the frame on every page, or that you have placed the necessary code in the correct template files if you are showing it on customized pages. Then make sure that your browser window is larger than the value for minimum size to display the frame, and try changing that value (make it smaller). Then, check your theme's `header.php` and `footer.php` files for the necessary action hooks (more instructions on the general options settings page). Check your vertical position setting AND UNITS in the appearance settings tab (15px is much different from 15in) - it's possible that your frame is too far down the page to see. If the problems persist, try using a bigger number for the custom layering/z-index (in appearance). If all else fails, try the support forums and someone with html knowledge should be able to find the problem.

= Transparent Frame / Icons Only =
You can make the frame transparent by selecting custom theme, a custom colorscheme, and typing "transparent" in the color code field for background color.

= Pintrest, Linkedin, Google+, ... =
Native support for a wider array of social media sites is coming, but in the meantime, you can include links to them with fsml custom links. You can upload your own icons, or use those already packaged with the plugin for Pintrest, Linkedin, and Google+. The image urls are similar to those for the facebook, twitter, and youtube icons, in the plugin's img folder.

= How do I use the display on custom pages option? =
To use the option to display the frame on customized pages, instead of on every page on your site, you need to be comfortable with editing you're theme's template files (html/php). If you are, siply copy this code: `<?php floating_social_media_links() ?>` and paste it into each template file that is used for the pages that you want (for example, index.php for the main blog page, page.php for the default page template, search.php for the search results page, archives.php for archive pages, etc.). <a href="http://codex.wordpress.org/Theme_Development#Template_Files_List">See wordpress' documentation on theme files</a>. Once you find the template files that correspond to the pages you want the frame to appear on, paste the code into the file immediately before the line that says `<?php get_footer() ?>`.

To include dynamic php-generated links, call the `floating_social_media_links()` function with up to three parameters. Each parameter will override the custom link 1-3 url options, although you still need to set the icon image, link title, and click the show link checkbox in the settings page. If you only want one dynamic link but want it after two other custom links, you can call `<?php floating_social_media_links(null,null,$dynamic_variable); ?>`. The parameters will only replace the links if they are (identified by php as) strings. To debug, use the php function `is_string()`.

= Where's the settings page? =
It's under settings: Settings -> Floating Social Media Links. And on a side note, the tabs on the settings page are showing different parts of the same page, not refreshing the browser, so you can switch between tabs without needing to save.

= What about other social media sites? =
I realize that there are countless other social media sites which aren't included in this plugin. In order to avoid an extensively bloated plugin and settings page, I have only included the three most popular sites (Facebook, Twitter, and Youtube - in the US). But... one of the reasons I've included space for up to 7 custom links is so that you may include links to any additional social media sites that you wish to. Additional social media icons are now bundled with the plugin (these all have a unified, flattened design and are both Hi-DPI and highly compressed), and you can link to them in the settings page, or you can find and upload your own icons. Then, enter the entire url (including, for example, http://facebook.com/), and it functions just like any of the built-in links. You can also include action buttons for other sites, as long as they have url-based options (enter the share url as a custom link, then create a share icon and use them in tandem). 

= How can I add more custom links? =
Currently, up to seven custom links may be used at a time. I am preparing a massive internal restructuring of this plugin which will remove a variety of current limitations, including allowing infinite links of any time, including built-in support for many more social media sites and additional action buttons.

= Why default to customized pages showing the links? =
This allows users to enter their links before the frame is actually show on your site. This option replaces an old option to activate the frame output; which would be redundant as this option does essentially the same thing. I try to minimize the number of useless options so that I can maximize the useful ones without bloating the plugin and making it take more than a couple minutes to set up.

= My settings for a custom link disappeared when I said to hide it. Can I get them back or do I need to start over? =
Any custom links that are hidden from your site will also be hidden from your settings page, by default. All of your settings, however, are saved. Simply click "Add Another Custom Link" and your previous settings will be pre-populated in the form. 

Note: If you have custom url 2 set, but not custom url 1 (for example), the options for custom url 2 will be hidden and url 1 shown on the options page (not on your site). Please use the custom urls sequentially to avoid this scenario. Eventually, I will introduce the ability to reorder the links, but this is at least 6-10 months out.

= How can I include a facebook like / twitter follow / youtube subscribe button in the frame? =
Simply go to the "action buttons" tab on the settings page and check the boxes for the buttons that you would like to show. Facebook like buttons do have the potential to increase your page load time though. This is an issue with the facebook SDK, not this plugin, and is present regardless of whether you include the facebook like button with this plugin or elsewhere.

= Where are the custom color scheme settings? =
Once you click on the "custom" color scheme option, all of the associated settings will appear. In general, options that are dependent on other options are hidden when they aren't available so that you don't need to read and scroll past them (creating a very convinient, user-friendly interface!).

== Screenshots ==
1. A collage of uses of the plugin on various sites with various settings.
2. The Admin Settings Page, on the new Design tab, showing the theme-choosing interface with the various default themes.

== Changelog ==
= 1.5.2 =
* Fix image uploaders for WordPress 3.6
* Remove deprecated jQuery function call in media modal js (displays with jQuery migrate when WP_DEBUG is true in WordPress 3.6)
* Update facebook icon. New email and default icons.
* Add suggestions to use the new QuickShare plugin for content-sharing (which is more appropriate than FSML for sharing as opposed to liking/following/subscribing and linking).
* A major plugin overhaul is in the works. **Many** unnecessary/bloated features will be removed. This version disables some of those features by default for new installs. 
* The FSML contents with widgets temporary beta plugin experiment is over; remove referrences. It will continue working until the major plugin overhaul is released, and registered users will be notified of EOL at that time.

= 1.5.1 =
* Add custom css input field (in design customizer)
* Improve compatibility with TwentyThirteen and other themes by explicitly specifying box-sizing for plugin elements
* Plugin's html output is now valid per W3C guidelines (there were a few minor issues previously)
* Moved admin settings page css to separate file 
* Generate options css output in a separate function, and store upon options save for improved performance
* Confirm compatibility with WordPress 3.6 (barring major last-minute changes)

= 1.5.0 =
* **WordPress 3.5 is now the REQUIRED minimum version**. It won't whitescreen, but there will be limited access to settings in older versions. The front end of your site will still function properly.
* Added **themes** (go to the design tab of the settings page), a quick/easy way to automatically style the floating frame to fit your site. Detailed, individual settings can be adjusted by selecting the "customize" theme
* Added frame opacity (semi-transparency) option
* Reworked admin interface with more dynamic options
* Created **new Facebook like button** implementation that fits with the plugin's style, is dynamic width, and is relatively consistent with the UI of the other action buttons. And, it has virtually no impact on page load time, unlike the previous version, with the actual buttons only loading after the user clicks the like icon (like with the follow and subscribe buttons)
* Packaged more complete icon set for other social media sites with plugin, files are located in `/[fsml directory]/img/`
* Reworked all of the plugin's JavaScript, in the admin and the front end, should have **much better performance** on both fronts
* Switched to the WordPress 3.5 media library and color pickers. Not maintaining back-compatibility to avoid bloat, so be sure to update WordPress when you update the plugin (front-end of site is uneffected)
* Fixed lots of other bugs
* Cleaned up documentation in settings page, code comments, and readme, some new screenshots
* Development of major plugin rewrite, version 2.0.0 is ongoing (expected around WordPress 3.7); the temporary beta sub-plugin is still available to enable the upcomming features with a workaround in the widgets interface, although it will not be actively maintained as I'm taking a different approach to integrating the features into the main plugin.

= 1.4.3 =
* Confirmed compatibility with Wordpress 3.5. Integration of new 3.5 media and color picker features will be explored in the upcomming major release.
* Fixed two security vulnerabilities. Thanks to Charlie Eriksen via Secunia SVCRP for disclosure of the issues. Performance should also be much improved as a result of the changes.
* Fixed image alt attributes; now reflecting the title values
* Added new option to better enforce minimum width screens to display the frame, see the advanced general options

= Special Notice for Version 1.4.2 =
* Minor modifications have been made to this plugin which allow a secondary plugin I've created to implement coming features in a slightly roundabout way using widgets. If you're interested in frame contents reordering, unlimited custom links, or native links for more sites and more action buttons, <a href="http://celloexpressions.com/dev/floating-social-media-links/fsml-contents-widgets-temporary-plugin-information/">please learn more here</a>.

= 1.4.2 =
* In honor of the release of Windows 8, and the new style of graphic design it emphasizes, the ability to switch between "classic" (rounded corners, shadows, etc.) and "modern" (square, bold, clean lines) styles with a single click has been added. Individual elements can still be edited independently, of course.
* A new option to show a border around each icon as it's hovered over, like in the new Windows 8 start screen. Option is in extended appearance, and merges with the opacity effect, which has been toned down.
* <b>More-Contextual Editing</b>: When an administrator is logged in to the front end of the site, an edit link is now displayed in the floating frame. This makes it super easy to get to the settnigs page from the site, providing a more contextual editing experience like that found in other content management systems. Think of it like the logged in toolbar, and edit post links that display for admins. The link is designed to allow you to see what the frame looks like without the edit link, but with the link still being noticeable.
* <b>Device Compatibility</b>: The frame can now be optionally hidden on devices/windows under a specified width. This addresses issues with the iPhone handling webpages unexpectedly, although Android and Windows Phone always positioned the frame accordingly.
* <b>Customization</b>: A custom width can now be specified (decimals are allowed in dynamic width mode). Please note that max/min widths are still in place to avoid ridiculousness
* <b>Efficiency</b>: Plugin size has been reduced by about 50%, thanks to the improved compression of necessary images, and the removal of most screenshots (which are now visible on my website). This will allow the reworked version of the plugin that I'm working on to be about the same total size as version 1.4.1, despite offering unlimited links, native support for many more sites, and a drag-and-drop interface that allows link reordering (this update is coming in a few months because I need to completely rewrite the internal structure of the plugin's codebase).

= 1.4.1 =
* Fixed a bug in setting the defualts, which caused cookies to not be enabled by default or set top a numeric length
* Caught a couple of minor bugs and spelling errors in the settings
* Cleaned up the readme info
* Fixed a major bug that affected completely clean installs only, since version 1.3.0. It said that the plugin generated 300 or so characters of unexpected output - wordpress was doing something weird but I worked around it. If anyone notices something like this in the future, please let me know so I can fix it!!! I haven't done completely clean installs for a while, only reseting the defaults, as that part of the code hasn't changed since version 1.0.0; the bug was somewhere else entirely and thus very difficult for me to detect and locate.

= 1.4.0 =
* Major Update, lots of bugfixes, new features, and enhancements, including a new built-in icon set!
* NEW FEATURES:
* When a visitor hides the frame on one page, it will remain hidden on every other page they visit on the site or if they refresh the page (if it is reopened, it will remain reopened, etc.). The preference for keeping the frame hidden is kept for one week as a cookie. For sites that start with the frame hidden, note that the frame will never be saved as being in the open position.
* The length of the cookie for remembering a hidden frame can be customized.
* Ability to prevent facebook like button from overflowing the frame contents (unfortunately, facebook is stubborn with their api and the only way to accomplish this is to make their width the minimmum possible for the frame, but the frame will still resize larger when using this option)
* Advanced/detailed options and options that are unavailable are now always hidden as necessary, making the overall user experience less cluttered and faster to use. Want more options? Click the appropriate links and the will be shown. Not using a custom color scheme? Those options won't be shown (a preliminary framework for this system has been in place for a while, but it is now fully implemented accross every options tab). The plugin will also remember your preferrences next time you visit the settings page, even if it's on a different computer.
* ENHANCEMENTS:
* Upgraded icon set allows full support for square corners on icons and the frame, a much more modern look which will fit better with most websites and is the new default.
* New, larger & dynamic width "X" icon for hiding the frame (much easier to hit on touch devices!)
* Added opacity/hover effect to the x and + icons to make them more blendable and less distracting (not available on non-html5/css3 browsers, as with rounded corners and shadows)
* Restructured html&css output for more streamlined spacing and padding around frame (again)
* New update mechanism that makes saving options faster (you still need to "save changes" after most updates though)
* Updated screenshots, which now include several examples of the plugin in use. If you want me to add yours with the next version, <a href="http://celloexpressions.com/dev/contact/" target="_blank">tell me</a>!
* Confirmed compatibility with Wordpress 3.4.2
* BUGFIXES:
* Fixed bug with hiding advanced appearance options
* The frame is no longer hidden behind the wordpress "logged in toolbar", it's moved down the page accordingly instead; so you don't need to log out or use a different browser to set up vertical positioning! Please note that the animations for closing the frame will display differently without the admin toolbar.
* Fixed inconsistencies where certain themes could mess up the appearance of the frame.
* The most thuroughly tested and stable version yet!

= 1.3.5 =
* Fixed a MAJOR bug affecting versions 1.3.1 and higher, which prevented the 1st custom link from working properly. The bug was only present on clean/new installs, not updates, which is why it took so long to discover.
* Fixed a secondary bug that was preventing the dynamic php link functionality from working for more than one link, but that was also preventing the major bug fixed in this update from being catastrophic :)

= 1.3.4 =
* Custom links can be opened in the same or a new tab (set on a per-link basis). Please click "save changes" on the settings page after updating to set all of the custom links to the default (new tab) setting BEFORE making your own settings for this option.
* Reordered the options in the appearance tab for more efficient workflow
* New option to set a custom z-index (frame layer), in case you need to customize positioning around other elements, such as full-page overlays or other floating frames.
* Laying some groundwork for a system which will eventually hide more advanced appearance options by default. For now all of the more highly customizing options in the appearance page are grouped into a section that can be shown/hidden
* The plugin now remembers the most recent settings tab visited, and you automatically return to that tab once you save changes, refresh the page, or come back to the settings page later. You can (and should) still move freely between the different tabs without needing to save changes!

= 1.3.3 =
* Fixed major bug with the "include facebook root" script option that caused it to be included on every site regardless of the option. This should greatly increase performance for users who are including the facebook SDK root elsewhere.

= 1.3.2 =
* Fixed minor bug with new IE feature (missing space in conditional comment)

= 1.3.1 =
* Custom link default/placeholder icons replaced with one universal default icon to reduce plugin filesize and remove unnecessary unique placeholders.
* Support for compatibility with Internet Explorer 7 and below can be enabled with a checkbox in the general options admin tab.
* Improved process of updating from older versions (1.2.0 and below)
* Up to three (3) custom links can be dynamically generated with php. Simply set the display option to custom pages, and call the floating_social_media_links() function, which accepts up to three parameters. Each parameter will override the custom link 1-3 url options, although you still need to set the icon image, link title, and click the enable checkbox in the settings page. If you only want one dynamic link but want it after two other custom links, you can call floating_social_media_links(null,null,$dynamically-generated-variable);.

= 1.3.0 =
* MAJOR Update, with new features, bugfixes, enhancements, greater customization, and a streamlined options page (that didn't get much longer!)
* NEW FEATURES: 
* Frame can start hidden (in show/hide options)
* Choice of show/hide animations
* Changed plugin output activation to allow option for automatic sitewide display (with the wp_footer hook) or template-specific display with the new floating_social_media_links() hook
* To use the custom hook, paste this code into the bottom of each page template you want the links to show up on, right before `<?php get_footer() ?>`: `<?php floating_social_media_links() ?>`.
* A title can be set for the show frame icon (when in the hidden position)
* BUG FIXES:
* Border displaying in dynamic width when set during fixed width mode
* Max-width set on dynamic widths, so dynamic width frames are never too big (more than 100px wide) on large screens
* When website is printed, frame is not displayed at all now (previously printed covering up the middle of the website)
* Enqueued admin script instead of embeding (should fix isssues where the page tabs, color pickers, and dynamically showing/hiding options didn't work)
* ENHANCEMENTS:
* Auto-dection/inclusion of leading /, http://, https://, mailto:, or sms: on custom links
* Twitter follow button dynamically resizes (never overflows), and uses NO javascript for instant loading
* Removed option to hide from the homepage, because of new, more customizable template tag for customizing pages to display frame on

= 1.2.1 =
* Minor update addressing a couple of minor bugfixes
* Ability to hide the floating from from the homepage/blog page (whichever is set as the front page)

= 1.2.0 =
* Several feature upgrades and additions including:
* Ability to disable frame shadow independently of a custom colorscheme
* Wide, medium, slight, no, or custom rounded corners on the frame (css border radius)
* More extensive options for vertical positioning including support for different units and auto-detection if a custom value has been set
* Custom Link icons are no longer restricted to squares, only the width is resized and the height is adjusted accordingly
* Support for dynamic width websites has been added, with separate size options for fixed width and dynamic width sites (reduces frequency of frame covering site content)
* Completely restructured CSS for greater efficiency, and way fewer external files
* /user/ and /!#/ have been removed from youtube and twitter URLs by default, as they are now optional for youtube and twitter (you may add them in with your url extension if desired)
* Other minor adjustments, bugfixes, better protection from bad options inputs, and clearer, more intuitive instructions and settings labels

= 1.1.0 =
* Major upgrades to the user interface, some new options, clearer instructions, a new YouTube subscribe button, and minor bugfixes.
* VERY IMPORTANT: ONCE THE UPDATE IS COMPLETE, YOU MUST GO TO THE "LINKS/ICONS" TAB AND CHECK THE "ENABLE LINK" CHECKBOX NEXT TO EACH LINK YOU WANT TO ENABLE. THIS IS A NEW OPTION REPLACING THE DISABLE THIS LINK OPTION (MORE INTUITIVE THIS WAY), AND YOUR DISABLE SETTINGS CANNOT BE TRANSFERED. ALL OTHER SETTINGS ARE PRESERVED. 

= 1.0.1 =
* Fixed margin/spacing errors between links and especially social sharing buttons
* Confirmed compatibility with Wordpress 3.4.1

= 1.0.0 =
* First publically available version of the plugin.
* Compatible with Wordpress 3.3.0 through 3.4.0

== Upgrade Notice ==
= 1.5.2 =
* Bugfixes for WordPress 3.6, icon updates minor enhancements and tweaks.

= 1.5.1 =
Minor housekeeping update addressing code cleanup and html validation; adding custom css field. WordPress 3.5 is required as of version 1.5.0. 