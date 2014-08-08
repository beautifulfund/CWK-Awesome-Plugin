<?php
/*
* Plugin Name: CWK-Awesome-Plugin
* Plugin URI: http://www.creativeworksofknowledge.com/
* Description: An awesome plugin you can't do without brought to you by CWK.
* Version: 0.1
* Text Domain: cwk-ap-textdomain
* Domain Path: /languages/
* Author: Wankyu Choi
* Author URI: http://www.creativeworksofknowledge.com/
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0
* Slug: cwk-ap
*/

/* Copyright (C) 2014- Wankyu Choi (email: wankyuchoi@gmail.com)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */ 

// Action Example

function cwk_footer_message() {
    $html_content = '<a href="http://www.creativeworksofknowledge.com" title="Creative Works of Knowledge" target="_blank">CWK Awesome Plugin</a>';

    echo $html_content;
}

add_action( 'wp_footer', 'cwk_footer_message');

// Filter Example

function cwk_filter_profanity( $content ) {
    $profanities = array( '씨바', '졸라', '니미', '개쉐이');
    $content = str_replace( $profanities, '{고운말 써. 시방새야.}', $content);
    return $content;
}

add_filter( 'the_content', 'cwk_filter_profanity' );
 

// Shortcode Example
// [cwk-stock-info stockcode="005930"]

function cwk_get_stock_info( $atts ) {
    extract( shortcode_atts( array('stockcode' => 0, 'showchart' => 'false' ), $atts, 'cwktag' ) );

    if( !$stockcode ) {
        return "Invalid stockcode:" . $stockcode;
    }

    $url = "http://finance.naver.com/item/main.nhn?code=" . $stockcode;
    $http_args = array(
        'user-agent' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)'
    );

    $response = wp_remote_request($url, $http_args);
    
    if( !is_wp_error( $response ) ) {

        require_once('inc/phpQuery-onefile.php');

        $current_charset = get_bloginfo('charset');
        $raw_html = $response['body'];

        $price_selector = 'div.rate_info';
        $chart_selector = 'div.chart';

        $phpquery = phpQuery::newDocumentHTML($raw_html, $current_charset);
        phpQuery::selectDocument($phpquery);

        $titleElement = pq('title'); 
        $title = $titleElement->html();

        $html_content = htmlentities( $title )  .  ' <em>(Stock Info Provided by CWK)</em>';
        $html_content .= pq($price_selector)->html();

        if ( $showchart == 'true' ) {
            $html_content .= "<div>차트</div>";
            $html_content .= pq($chart_selector)->html();
        }

        return $html_content;
    } else {
        return 'CWK operation failed: ' . $response->get_error_message();
    }
}

add_shortcode( 'cwk-stock-info', 'cwk_get_stock_info');

// Shortcode Example 2
// [cwk-example]Here goes your content.[/cwk-example]


function cwk_example_style( $atts, $content = null ) {
    return '<div class="cwk-example">' . $content . '</div>';
}

add_shortcode( 'cwk-example', 'cwk_example_style');

?>