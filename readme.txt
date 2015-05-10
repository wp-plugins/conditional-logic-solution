=== Conditional Logic Solutions (CLS) ===
Contributors: irenem
Tags: users, roles, capabilities, posts, pages, widgets, sidebars
Requires at least: 3.4
Tested up to: 4.2
Stable tag: 1.2
License: GPLv2 or later

The complete control solution for wordpress powered site.

== Description ==

= What is CLS? =
CLS stands by it's name. It is a conditional logic design to empower site owners to have absolute control in most areas, if not all, of their site. It provides control to modify what users can and cannot do. And control contents visibility according to user, user group, and currently use page template.

= What it does? =
1. Set or reset users capabilities.
1. Add new user group.
1. Controls dashboard widgets visibilities for selected user or user group.
1. Control the visibility of your posts and pages.
1. Controls the visibility of your sidebars, and sidebar widgets per user, user group, and current page templates.
1. Control product's (Woocommerce) visibility and capabilities.


== Installation ==
1. Upload `conditional-logic-solution` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Once activated, go to -> Settings -> Conditional Logic menu to configure the main settings.

== Frequently Asked Questions ==

= How will I know if the changes are applied? =
You may want to download my other plugin called "userSwitcher". It allows you to see all changes from different type of user or user group.
And when you do so, do choose an specific user belonging to a group rather than a group. Selecting a group instead of user makes you the current user. So you might not see the difference.

= Will it change the core wordpress capabilities? =
Technically yes. It's what it is made for. But it will not change anything directly. It only changes the interface. If you decide to turn this plugin off, all settings will disappear as well. Your site will resume to it's original glory.

= Will it changes administrators capabilities? =
Oh yes provided that the administrator whom you want to have a different capabilities is/are not selected as controller. All selected controllers becomes the super-admin of your site. Any changes you made will not have any effect to you or your selected controllers.

= Will my selected controller can remove me as controller? =
Unfortunately yes. As controller he has the power to change the settings at the back. He can either remove or change your capabilities or make you a subscriber. Anything is possible.

== Screenshots ==
1. Main CLS page
2. CLS @ Dashboard
3. CLS @ Posts / Pages
4. Per posts/page CLS
5. CLS @ Widget page

== Changelog ==
= 1.1 = 
* Fixed sidebar ui, widget visibility not showing in customizer page.

= 1.2 =
* Added Woocommerce CLS controls.
