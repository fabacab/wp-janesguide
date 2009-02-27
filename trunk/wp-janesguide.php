<?php
/*
 * Plugin Name: WP-JanesGuide
 * Version: 0.1
 * Plugin URI: http://maybemaimed.com/playground/wp-janesguide-wordpress-plugin/
 * Description: Easily enables the display of <a href="http://JanesGuide.com">JanesGuide.com</a> awards on your WordPress-generated pages.
 * Author: Meitar "maymay" Moscovitz
 * Author URI: http://maybemaimed.com/
 *
 * @file wp-janesguide.php
 * @license GPL3 
 *
 *  Copyright 2008  Meitar Moscovitz  (email : bitetheappleback@gmail.com)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

/*********************
 * Metadata and stuff.
 ********************/

define('WP_DEBUG', true);

if (!defined(WP_JANESGUIDE_URL)) {
    define('WP_JANESGUIDE_URL', get_bloginfo('wpurl') . '/' . PLUGINDIR . '/wp-janesguide');
} else {
    // This shouldn't ever really happen, right?
    die('WP JanesGuide has already been defined. Duplicate instance running?');
}

class WP_JanesGuide {
    var $janes_prefix = 'http://www.janesguide.com/'; /**< Hardcode the domain of JanesGuide.com to frustrate phishing opportunities. */

    /**
     * Constructor.
     */
    function WP_JanesGuide () {
        add_option('janesguide_review_uri', get_option('janesguide_review_uri'));
    }
}
$wp_janesguide = new WP_JanesGuide();

/****************************
 * Administration page stuff.
 ***************************/

add_action('admin_menu', 'wp_janesguide_menu');
function wp_janesguide_menu () {
    add_options_page('JanesGuide Awards', 'JanesGuide', 8, __FILE__, 'wp_janesguide_options');
}

function wp_janesguide_options () {
    global $wp_janesguide;
?>
    <div class="wrap">
        <h2>JanesGuide Settings</h2>
        <form method="post" action="options.php">
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="janesguide_review_uri" />
            <?php wp_nonce_field('update-options'); // useful WordPress magic ?>
            <fieldset>
                <legend>Review Details</legend>
                <table summary="" class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="janesguide_review_uri">Review URI</label></th>
                            <td>
                                <?php print $wp_janesguide->janes_prefix;?><input id="janesguide_review_uri" name="janesguide_review_uri" class="regular-text" value="<?php print get_option('janesguide_review_uri');?>" onchange="if (this.value.match(/^http:\/\/www\.janesguide\.com\//)) { this.value = this.value.substr(26, this.value.length - 26) }" />
                                <span class="setting-description">Enter the web address of your review on <a href="http://www.janesguide.com/">JanesGuide.com</a>.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <p class="submit"><input name="submit" class="button-primary" type="submit" value="<?php _e('Save Changes');?>" /></p>
        </form>
    </div>
<?
}

/******************
 * Theme functions.
 *****************/

/**
 * Displays this site's JanesGuide award.
 * 
 * @param string $award Which award image to display. Options are "quality" (the default) or "originalquality".
 * @param bool $out Returns the HTML output instead of printing it if false. Defaults to true.
 * @return mixed Void if $out is true, otherwise a string of the constructed HTML.
 */

function wp_janesguide_award ($award = 'quality', $out = true) {
    global $wp_janesguide;
    $html = '<a href="' . $wp_janesguide->janes_prefix . get_option('janesguide_review_uri') . '"><img src="' . WP_JANESGUIDE_URL;
    switch ($award) {
        case 'originalquality':
            $html .= '/linkbackqo.gif" alt="Jane says we\'re quality and original!"';
        break;
        case 'quality':
        default:
            $html .= '/linkbackq.gif" alt="Jane says we\'re quality!"';
        break;
    }
    $html .= ' /></a>'; // close elements

    if ($out) {
        print $html;
    } else {
        return $html;
    }
}

/**
 * Displays generic icon to JanesGuide homepage; useful for sites that have not been reviewed yet.
 * 
 * @return void
 */
function wp_janesguide_icon () {
    global $wp_janesguide;
    $src   = WP_JANESGUIDE_URL . '/janesays.gif';
    $html  = '<a href="' . $wp_janesguide->janes_prefix . '">';
    $html .= "<img src=\"$src\" alt=\"Fine adult website reviews, galleries, sex toys and more at JanesGuide.com!\" />";
    $html .= '</a>';
    print $html;
}
?>
