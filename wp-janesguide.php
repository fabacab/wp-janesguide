<?php
/*
 * Plugin Name: WP-JanesGuide
 * Version: 0.3
 * Plugin URI: http://maybemaimed.com/playground/wp-janesguide-wordpress-plugin/
 * Description: Easily enables the display of <a href="http://JanesGuide.com">JanesGuide.com</a> awards on your WordPress-generated pages via a widget or template tags.
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

define('WP_DEBUG', true);

if (!defined(WP_JANESGUIDE_URL)) {
    define('WP_JANESGUIDE_URL', get_bloginfo('wpurl') . '/' . PLUGINDIR . '/wp-janesguide');
} else {
    // This shouldn't ever really happen, right?
    die('WP JanesGuide has already been defined. Duplicate instance running?');
}

class WP_JanesGuide {
    var $janes_scheme = 'http://'; /**< Hardcode the protocol of JanesGuide.com to frustrate phishing opportunities. */
    var $janes_domain = 'janesguide.com'; /**< Hardcode the domain of JanesGuide.com to frustrate phishing opportunities. */

    /**
     * Constructor.
     */
    function WP_JanesGuide () {
        add_option('janesguide_review_uri', get_option('janesguide_review_uri'));
    }
}
$wp_janesguide = new WP_JanesGuide();

class WP_JanesGuide_Widget extends WP_JanesGuide {
    /**
     * Registers to the WordPress Widget API.
     */
    function init () {
        if (!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) { return; }
        register_sidebar_widget('JanesGuide Widget', array('WP_JanesGuide_Widget', 'display'));
        register_widget_control('JanesGuide Widget', array('WP_JanesGuide_Widget', 'control'));
    }

    /**
     * Validates review URI.
     *
     * @param $uri string A review URI string to check.
     * @return bool True if a valid review URI, false otherwise.
     * @TODO: This is currently unused. Need to figure out how to make sure we
     *        trap the options page save action and verify the URI. But, later.
     */
    function validateReviewURI ($uri) {
        // Make sure we're actually pointing at JanesGuide.com, please.
        // Or, of course, a subdomain, for flexible review URIs.
        $pattern = "(?:[A-Za-z0-9-]+\.)?$this->janes_domain";
        return (preg_match("#^$pattern#", $uri)) ? true : false;
    }

    /**
     * Displays the widget.
     */
    function display ($args) {
        extract($args); // WordPress magic

        $jgw_options = get_option('wp_janesguide_widget');
        if (!is_array($jgw_options)) {
            $jgw_options = array(
                    'title' => 'JanesGuide Award',
                    'rating' => 'icon'
                );
        }

        print $before_widget;
        print $before_title;
        print $jgw_options['title'];
        print $after_title;

        if ($jgw_options['rating'] === 'icon') {
            wp_janesguide_icon();
        } else {
            wp_janesguide_award($jgw_options['rating']);
        }

        print $after_widget;
    }

    /**
     * Outputs widget settings screen.
     */
    function control () {
        $jgw_options = get_option('wp_janesguide_widget');

        if ($_POST['wp_janesguide_widget_submit']) {

            switch ($_POST['wp_janesguide_rating']) {
                case 'quality':
                    $jgw_options['rating'] = 'quality';
                break;
                case 'originalquality':
                    $jgw_options['rating'] = 'originalquality';
                break;
                case 'originalquality2':
                    $jgw_options['rating'] = 'originalquality2';
                break;
                case 'icon':
                default:
                    $jgw_options['rating'] = 'icon';
                break;
            }

            if (!empty($_POST['wp_janesguide_widget_title'])) {
                $jgw_options['title'] = htmlentities($_POST['wp_janesguide_widget_title'], ENT_QUOTES, get_bloginfo('charset'));
            } else {
                $jgw_options['title'] = ''; // we don't want a title
            }

            update_option('wp_janesguide_widget', $jgw_options);
        }
?>
<!-- WP JanesGuide Widget options inline window -->
<input type="hidden" id="wp_janesguide_widget_submit" name="wp_janesguide_widget_submit" value="1" />
<fieldset>
    <legend style="display: none;">Title Options:</legend>
    <label for="wp_janesguide_widget_title">Title:</label>
    <input id="wp_janesguide_widget_title" name="wp_janesguide_widget_title" class="widefat" value="<?php print $jgw_options['title'];?>" />
</fieldset>
<fieldset>
    <legend>Image Options:</legend>
    <p>
        <input type="radio" name="wp_janesguide_rating" id="wp_janesguide_rating_icon" value="icon" <?php if ($jgw_options['rating'] === 'icon') : ?>checked="checked"<?php endif; ?> />
        <label for="wp_janesguide_rating_icon">Generic Icon</label>
    </p>
    <p>
        <input type="radio" name="wp_janesguide_rating" id="wp_janesguide_rating_quality" value="quality" <?php if ($jgw_options['rating'] === 'quality') : ?>checked="checked"<?php endif; ?> />
        <label for="wp_janesguide_rating_quality">Quality Award</label>
    </p>
    <p>
        <input type="radio" name="wp_janesguide_rating" id="wp_janesguide_rating_originalquality" value="originalquality" <?php if ($jgw_options['rating'] === 'originalquality') : ?>checked="checked"<?php endif; ?> />
        <label for="wp_janesguide_rating_originalquality">Quality and Original Award</label>
    </p>
    <p>
        <input type="radio" name="wp_janesguide_rating" id="wp_janesguide_rating_originalquality2" value="originalquality2" <?php if ($jgw_options['rating'] === 'originalquality2') : ?>checked="checked"<?php endif; ?> />
        <label for="wp_janesguide_rating_originalquality2">Quality and Original Award (simplified white background)</label>
    </p>
</fieldset>
<p><a href="options-general.php?page=<?php print 'wp-janesguide/wp-janesguide.php';?>" title="Configure additional WP JanesGuide plugin options.">Set plugin options&hellip;</a></p>
<?php
    }
}

add_action('plugins_loaded', array('WP_JanesGuide_Widget', 'init'));

/****************************
 * Administration page stuff.
 ***************************/

add_action('admin_menu', 'wp_janesguide_menu');
function wp_janesguide_menu () {
    add_options_page('JanesGuide Awards', 'JanesGuide', 8, 'wp-janesguide/wp-janesguide.php', 'wp_janesguide_options');
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
                                <?php print $wp_janesguide->janes_scheme;?><input id="janesguide_review_uri" name="janesguide_review_uri" class="regular-text" value="<?php print get_option('janesguide_review_uri');?>" />
                                <span class="setting-description">Enter the web address of your review on <a href="http://www.janesguide.com/">JanesGuide.com</a>. (Don't include the <code>http://</code> part.)</span>
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
    $html = '<a href="' . $wp_janesguide->janes_scheme . get_option('janesguide_review_uri') . '"><img src="' . WP_JANESGUIDE_URL;
    switch ($award) {
        case 'originalquality':
            $html .= '/linkbackqo.gif" alt="Jane says we\'re quality and original!"';
        break;
        case 'originalquality2':
            $html .= '/janes_quality_sex02.gif" alt="Jane says we\'re quality and original!"';
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
    $html  = "<a href=\"{$wp_janesguide->janes_scheme}{$wp_janesguide->janes_domain}\">";
    $html .= "<img src=\"$src\" alt=\"Fine adult website reviews, galleries, sex toys and more at JanesGuide.com!\" />";
    $html .= '</a>';
    print $html;
}
?>
