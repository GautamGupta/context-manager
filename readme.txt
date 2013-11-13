=== Context Manager ===
Contributors: phill_brown
Donate link: http://pbweb.co.uk/donate
Tags:  context, wp_enqueue_styles, wp_enqueue_scripts, rules, widget logic, menu rules, body class, widgets, parent menu, active menu
Requires at least: 3.2
Tested up to: 3.7
Stable tag: 1.2.0

Make your site react to users' context by changing your theme's CSS and JavaScript files, navigation menus, sidebars and the HTML body tag.

== Description ==

Context Manager makes your site behave differently depending on the current user's context. Using the simple point-and-click admin pages, there are four different ways your site can react:

1. Include and exclude CSS and JavaScript files
1. Changing the behaviour of menu items
1. Hiding widgets in sidebars
1. Adding extra classes to the `<body>` tag.

The plugin supersedes [Menu Rules](http://wordpress.org/extend/plugins/menu-rules/)

= Example usage =

A website has e-commerce shopping functionality driven by a custom post type called 'products'. There's an archive page called 'shop' that lists products and is linked to in the main navigation menu.

A user visits 'shop' and the menu item becomes 'active', but when they click through to an individual product, the menu item loses its state. The user becomes lost.

1. [Install](http://wordpress.org/extend/plugins/context-manager/installation/) the Context Manager plugin
1. Add a new context rule
1. Give it a meaningful name in the title field. This is just for administration purposes
1. In the *conditions* field enter `is_singular( 'product' )`
1. Choose *Emulate current page as a child but do not create a menu item.* as the menu rule
1. Find your products page in the menu dropdown

On the product page, there are irrelevant widgets that distract the user from making a purchase.

* Hide irrelevant widgets under the *widgets* reaction

The whole shop section requires its own colour scheme, but there's no common class that ties all the pages together.

* Enter `shop-section` class name in the *body class* reaction. Or alternatively, register another stylesheet using [`wp_register_style()`](http://codex.wordpress.org/Function_Reference/wp_register_style) in you theme's `functions.php`.
* Create 

Remember to click publish when you're ready to save.

Have a look at [screenshots](http://wordpress.org/extend/plugins/context-manager/screenshots/) to see the above setup in action.

= Support =

If you're stuck, ask me for help on [Twitter](http://twitter.com/phill_brown).

== Installation ==

1. Download and unzip the folder from [the WordPress plugins repository](http://wordpress.org/extend/plugins/context-manager/)
1. Upload the context-manager folder into to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Open the 'Appearance' menu item and click the 'Context Rules' link

== Screenshots ==

1. An example setup for a products section in a online shop
2. CSS and JavaScript reactions

== Changelog ==

= 1.2.0 =
* [Bugfix]: disabled assets reaction which triggered errors in 3.6. No feasible workaround found yet
* [Bugfix]: added conditional checks into widgets reaction

= 1.1.5 =
* [Bugfix]: menu_reaction property not being set in child page and inactive parent menu reactions

= 1.1.4 =
* [Bugfix]: Some environments were triggering a fatal error related to html() due to an action being added to a hook too late

= 1.1.3 =
* [Bugfix]: Asset reaction generating a global site error if not logged in

= 1.1.2 =
* [Bugfix]: Major issue that generated an error when adding or editing a context rule

= 1.1.1 =
* [Bugfix]: Errors in widget reaction when site has orphaned widgets 

= 1.1 =
* [Added]: Assets reaction
* [Bugfix]: get_rules() using incorrect meta_query parameter
* [Bugfix]: PHP 5.4 fatal errors

= 1.0.2 =
* [Bugfix]: Body class and widget reactions caused an error when no rules were set up

= 1.0.1 =
* [Bugfix]: Invalid foreach warning when no rules were added in get_rules()