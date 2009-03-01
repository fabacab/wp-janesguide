=== Plugin Name ===
Contributors: meitar
#Donate link: http://example.com/
#Tags: comments, spam
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: 0.1

Easily enables the display of JanesGuide.com awards on your WordPress-generated pages.

== Description ==

Provides two template tags (theme functions), `wp_janesguide_award()` and `wp_janesguide_icon()`, which will display an image of one of two awards in the case of the former or a generic JanesGuide.com linkback icon in the case of the latter.

Use `wp_janesguide_award()` if your site has received a "quality" (default) or a "quality and original" review from JanesGuide.com. For instance, to display a "quality and original" award icon, use code like this:

    <?php wp_janesguide_award('originalquality');?>

To display the 'quality' icon, use:

    <?php wp_janesguide_award();?>

These template tags will produce a linked image. You can customize the destination of the link by going to the JanesGuide Settings page in your WordPress Admin screens.

If you have not received a JanesGuide review yet, use:

    <?php wp_janesguide_icon();?>

== Installation ==

1. Upload `wp-janesguide.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php wp_janesguides_award();?>` in your template whereever you would like to display your JanesGuide.com award

== Frequently Asked Questions ==

= How do I find my JanesGuide.com review permalink? =

I need to find an answer to that question. ;)

= Where can I learn more about JanesGuide.com's linking guidelines, contact information, and so on? =

You should read the very succinct [JanesGuide.com webmaster info](http://janesguide.com/wm/).

== Screenshots ==

1. JanesGuide widget options window allows you to easily choose between four JanesGuide images to display that link to Jane's site. The "Set plugin options…" link allows you to configure additional, plugin-wide settings.

== Change log ==

= Version 0.1 =

* Initial release.
