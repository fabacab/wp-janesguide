<?php
/*
 * Plugin Name: WP-JanesGuide
 * Version: 0.1
 * Plugin URI: http://maybemaimed.com/playground/wp-janesguide-wordpress-plugin/
 * Description: Enables the display of JanesGuide awards on your WordPress generated pages.
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
 */

if (!defined(WP_JANESGUIDE_URL)) {
    define('WP_JANESGUIDE_URL', get_bloginfo('wpurl') . '/' . PLUGINDIR . '/wp-janesguide');
} else {
    die('WP JanesGuide has already been defined. Duplicate instance running?');
}

class WP_JanesGuide {
    var $review_uri;

    /**
     * Constructor.
     */
    function WP_JanesGuide () {
        // get the user's award URI from the database
        // or use the JanesGuide homepage if no permalink.
        $this->review_uri = ($this->getReviewURI()) ? $this->getReviewURI() : 'http://www.janesguide.com/' ;
    }

    /**
     * Retrieves the user's saved review permalink.
     */
    function getReviewURI () {
        return $this->review_uri;
    }
}
$wp_janesguide = new WP_JanesGuide();

/******************
 * Theme functions.
 */

/**
 * Displays this site's JanesGuide award.
 * 
 * @param string $award Which award image to display. Options are "quality" or "originalquality".
 * @param bool $out Returns the HTML output instead of printing it if false. Defaults to true.
 * @return mixed Void if $out is true, otherwise a string of the constructed HTML.
 */

function wp_janesguide_award ($award = 'quality', $out = true) {
    global $wp_janesguide;

    $html = '<a href="' . $wp_janesguide->getReviewURI() . '"><img src="' . WP_JANESGUIDE_URL;
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
    $src   = WP_JANESGUIDE_URL . '/janesays.gif';
    $html  = '<a href="http://www.janesguide.com/">';
    $html .= "<img src=\"$src\" alt=\"Fine adult website reviews, galleries, sex toys and more at JanesGuide.com!\" />";
    $html .= '</a>';
    print $html;
}
?>
