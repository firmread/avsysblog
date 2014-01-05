== License ==
Unless otherwise specified, all the theme files, scripts, and images are licensed under the GNU General Public License v2.
The exceptions to this license are as follows:
* WordPress Audio Player [http://www.opensource.org/licenses/mit-license.php MIT license]

== Changelog ==

= 1.1 Jan 2 2013 =
* Made wp_title truly pluggable
* Moved custom header and wpcom buisiness out of functions.php
* Added post format links to each formatted post.
* Appended post title to Aside and Quote posts' content.
* Set a max-width on the comment meta and comment author areas so they don't overlap when threaded comments make for a narrow comment header area.
* CSS fixes to allow background gradients to stay put on pages with lots of content.
* Let users change desired date format: However, if a user chooses F j, Y format, the theme will print it as M d Y to prevent the date spans two lines. It is counter intutive but this approach minimizes the breaking on Chunk activated blogs and at least now user can change to other date formats except F j, Y.
* Updated Google Fonts link to include the Extended Latin character subset, which was excluded from the original API.
* Added a check is_ssl() to define a protocol for Google fonts in order to ensure it's available for both protocols.
* Removed loading of $locale.php.
* Made sure attribute escaping occurs after printing.
* Fixed overly general .attachment img selectors.
* Updated the "audio" post format.
* Removed outdated swfobject code from js/audio-player.js.
* Used core version of swfobject and listed as a dependency of js/audio-player.js.
* Removed unneeded jQuery dependency.
* Cleaned out unused functions.
* Slightly increased padding around top-level links to account for smaller custom fonts in menus with sub-menus.
* Moved functions for grabbing bits of content into the theme.
* Added trailing slash to URL in comment header.
* Added a little more margin on .wp-caption.alignleft and .wp-caption.alignright so that there is more breathing space around images with captions when wrapped by text.
* Removed self-link from titles in single.php
* Added a byline for multi-author blogs hidden with CSS on single author blogs
* Added body classes for single and multi-author blogs
* Corrected the textdomain
* Added and improved reblog styles for comment stream

= 1.0 Sep 13 2011 =
* Initial release