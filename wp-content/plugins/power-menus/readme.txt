=== Power Menus ===
Contributors: wpidiots
Tags: menus, admin menus, administration menus, access, members, restricted, wp_nav_menu
Stable tag: trunk
Requires at least: 3.8
Tested up to: 3.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get full control over WordPress menus! Set visibility of menu items, control access per user role and logged or non-logged in users!

== Description ==

Get full control over WordPress menus with ease

*   Control visibility of menu items for logged-in or non-logged-in users
*   Show or hide items per user role
*   Limit access to posts and pages
*   Redirect users without required access permissions to a chosen page

More coming soon!

Don't forget to check our other cool [plugins](http://profiles.wordpress.org/wpidiots/)

== Frequently Asked Questions ==

= How to show a menu item for non-logged in users and hide it from logged in one? =

1. Select "No" for Visible to logged in users only option
2. Un-check all roles from the Visible to selected user roles list

= How to hide a menu item from non-logged in users  =

1. Select "Yes" for Visible to logged in users only option
2. Check all roles you want to see the menu item from the Visible to selected user roles list

= I can see "Secure Page - Power Menus" section on the left hand side of menu options, what is it for?  =

You can select a page there where the users with insufficient permissions will be redirected to if they try to access to the post / page you hided.
If the page is not selected, menu items will be hidden but direct URL access will still be allowed.

== Installation ==

Quick Setup Steps

1. Upload `power-menus` directory or `power-menus.zip` file to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `/wp-admin/nav-menus.php` and set the secure page under "Secure Page - Power Menus" box (this will be the page where user with insufficient access permissions will be redirected to). If the page is not set or it's set to "None", chosen menu items will be hidden but page(s) will be still accessible via URL.
4. Choose menu items and set their visibility (you should see "Visible to logged in users only" and "Visible to selected user roles" section)

== Screenshots ==

1. View of the menus additional options

== Changelog ==

= 1.1.2 =

* Removed unnecessary enqueue for admin-style.css

= 1.1.1 =
----------------------------------------------------------------------
* Removed front-end notices (http://wordpress.org/support/topic/a-bug-and-an-enhancement?replies=2)

= 1.1 =
* Added additional rules in order to show menu items for non-logged in users and hide it from logged in one

= 1.0.1 =
* Applied a small CSS fix for Secure Page dropdown in admin

= 1.0 =
* First Release