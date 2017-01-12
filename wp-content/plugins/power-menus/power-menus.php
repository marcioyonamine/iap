<?php
/*
  Plugin Name: Power Menus
  Plugin URI: http://www.wpidiots.com/plugins/power-menus/
  Description: Get full control over WordPress menus with ease - control visibility of menu items for logged-in or non-logged-in users, show or hide items per user role, limit access to posts and pages, redirect users without required access permissions to a chosen page!
  Author: WP Idiots
  Author URI: http://www.wpidiots.com/
  Version: 1.1.2
  License: GNU General Public License (Version 2 - GPLv2)

  Copyright 2014 WP Idiots (http://www.wpidiots.com/)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
  the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

//

/* Require custom walker class */

require_once("includes/power_menus_walker.php");

/* Actions */

add_action('wp_update_nav_menu_item', 'power_menus_custom_nav_update', 10, 3);
add_action('admin_init', 'power_menus_secure_page_meta_box');
add_action('init', 'power_menus_output_buffer', 0);
//add_action('admin_enqueue_scripts', 'power_menus_admin_enqueue_scripts');

/* Filters */

add_filter('wp_edit_nav_menu_walker', 'power_menus_nav_walker', 10, 2);
add_filter('wp_setup_nav_menu_item', 'power_menus_custom_nav_item');
add_filter('wp_nav_menu_objects', 'power_menus_main_navigation_links', 10, 2);

/* Start buffering content (headers already sent resolved with this one) */

function power_menus_output_buffer() {
    ob_start();
}

/* Enqueue admin scripts and style */

function power_menus_admin_enqueue_scripts() {
    wp_enqueue_style('power_menus_admin', plugins_url('css/admin-style.css', __FILE__));
}

/* Modify menu item list depend on options set (user logged in or not / user role) */

function power_menus_main_navigation_links($menu_items, $args) {

    if (!is_admin()) {
        $post_id = get_the_ID();

        $current_menu_item = 1;
        $user_role_can_access = 0;
        $protected_page_id = get_option('power_menus_protected_page_to_redirect_to', '');

        if ($protected_page_id !== '') {
            $protected_page_url = get_permalink($protected_page_id);
        } else {
            $protected_page_url = '';
        }

        foreach ($menu_items as $menu_item) {

            $user_role_can_access = 0;
            $item_url = $menu_item->url;

            if ($menu_item->power_menus_logged_in_only == 'yes') {
                if (!is_user_logged_in()) {

                    if ($item_url == power_menus_current_url()) {

                        if ($protected_page_url !== '') {
                            wp_redirect($protected_page_url);
                            exit;
                        }
                    }

                    unset($menu_items[$current_menu_item]);
                }
            }

            //power_menus_logged_in_only == 'no'

            if (!isset($menu_item->power_menus_user_roles)) {//it's not set only once, at start
                $user_role_can_access++;
            } else {//roles are set (could be empty array as well)
                if ( is_array($menu_item->power_menus_user_roles) && !empty($menu_item->power_menus_user_roles) && count($menu_item->power_menus_user_roles[0]) > 0 ) {
                    foreach ($menu_item->power_menus_user_roles as $user_roles) {
                        if (count($user_roles) > 0) {
                            foreach ($user_roles as $user_role) {
                                if (current_user_can(strtolower($user_role))) {
                                    $user_role_can_access++;
                                }
                            }
                        }
                    }
                } else {//user roles are empty / user is logged in but doesn't have required role to view the page
                    if ($menu_item->power_menus_logged_in_only == 'no' && is_user_logged_in()) {
                        $user_role_can_access = 0;
                        unset($menu_items[$current_menu_item]);
                    }
                }
            }

            if ($user_role_can_access == 0 /* && $menu_item->power_menus_logged_in_only == 'yes' */) {

                if ($item_url == power_menus_current_url()) {

                    if ($protected_page_url !== '') {

                        wp_redirect($protected_page_url);
                        exit;
                    }

                    unset($menu_items[$current_menu_item]);
                }
                if ($menu_item->power_menus_logged_in_only == 'yes' && is_user_logged_in()) {
                    unset($menu_items[$current_menu_item]);
                }
            }

            $current_menu_item++;
        }
    }
    return $menu_items;
}

function power_menus_secure_page_meta_box() {
    power_menus_secure_page_save_data();
    add_meta_box(
            'power-menus-secure-page-meta-box', __('Secure Page - Power Menus'), 'power_menus_secure_page_meta_box_content', 'nav-menus', 'side', 'default'
    );
}

function power_menus_secure_page_meta_box_content() {
    wp_nonce_field('power_menus_secure_page_meta_box', 'power_menus_secure_page_meta_box_nonce');
    ?>
    <p>
        <label class="howto" for="power_menus_secure_page">
            <span><?php _e('Secure Page'); ?></span>
            <?php
            $power_menus_protected_page_to_redirect_to_args = array(
                'selected' => get_option('power_menus_protected_page_to_redirect_to', ''),
                'echo' => 1,
                'show_option_none' => 'None',
                'name' => 'power_menus_protected_page_to_redirect_to');

            wp_dropdown_pages($power_menus_protected_page_to_redirect_to_args); //code menu-item-textbox

            $additional_attributes = array('class' => 'right');

            submit_button('Save', 'button right', 'power-menus-protected-page-save', true, $additional_attributes);
            ?>
        </label>
    </p>
    <?php
}

function power_menus_secure_page_save_data() {
    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['power_menus_secure_page_meta_box_nonce']))
        return;

    $nonce = $_POST['power_menus_secure_page_meta_box_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'power_menus_secure_page_meta_box'))
        return;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    /* OK, its safe for us to save the data now. */

    // Sanitize user input.
    $power_menus_protected_page_to_redirect_to = sanitize_text_field($_POST['power_menus_protected_page_to_redirect_to']);

    // Update the option in the database.
    update_option('power_menus_protected_page_to_redirect_to', $power_menus_protected_page_to_redirect_to);
}

/* Update custom power menus values */

function power_menus_custom_nav_update($menu_id, $menu_item_db_id, $args) {
    if (isset($_POST['power_menus_logged_in_only'][$menu_item_db_id]) && check_admin_referer('saving_custom_power_menus_options', 'power_menus_save')) {
        update_post_meta($menu_item_db_id, '_power_menus_logged_in_only', $_POST['power_menus_logged_in_only'][$menu_item_db_id]);
        update_post_meta($menu_item_db_id, '_power_menus_user_roles', (isset($_POST['power_menus_user_roles'][$menu_item_db_id]) ? $_POST['power_menus_user_roles'][$menu_item_db_id] : array()));
    }
}

/* Add custom properties to the menu object */

function power_menus_custom_nav_item($menu_item) {
    $menu_item->power_menus_logged_in_only = get_post_meta($menu_item->ID, '_power_menus_logged_in_only', true);
    $menu_item->power_menus_user_roles = get_post_meta($menu_item->ID, '_power_menus_user_roles', false);
    return $menu_item;
}

/* Handy function for easy searching through an array */

function power_menus_in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && power_menus_in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

/* Get current page url */

function power_menus_current_url() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function power_menus_nav_walker($walker, $menu_id) {
    return 'Power_Menus_Walker';
}
?>