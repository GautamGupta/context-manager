=== Context Manager ===
Contributors: phill_brown
Donate link: http://pbweb.co.uk/donate
Tags:  context, rules, widget logic, menu rules, body class, widgets, parent menu, active menu
Requires at least: 3.2
Tested up to: 3.5
Stable tag: 1.0.1

Make your site react to contextual conditions using a point and click interface

== Description ==

Context Manager lets you apply your own context rules and reactions to the menu system, your theme's sidebars and the HTML body tag.

The plugin supersedes [Menu Rules](http://wordpress.org/extend/plugins/menu-rules/)

= Example usage =

A website has e-commerce shopping functionality driven by a custom post type called 'products'. There's an archive page called 'shop' that lists products and is linked to in the main navigation menu.

A user visits 'shop' and the menu item becomes 'active', but when they click through to an individual product, the menu item loses its state. The user becomes lost.

On the product page, there are irrelevant widgets that distract the user from making a purchase.

The whole shop section requires its own colour scheme, but there's no common class that ties all the pages together.

Context Manager can fix all of these things.

1. [Install](http://wordpress.org/extend/plugins/context-manager/installation/) the Context Manager plugin
1. Add a new context rule
1. Give it a meaningful name in the title field. This is just for administration purposes
1. In the *conditions* field enter `is_singular( 'product' )`
1. Choose *Emulate current page as a child but do not create a menu item.* as the menu rule
1. Find your products page in the menu dropdown
1. Hide irrelevant widgets under the *widgets* reaction
1. Enter a meaningful class name in the *body class* reaction
1. Hit publish

See a [screenshot](http://wordpress.org/extend/plugins/context-manager/screenshots/) of the above setup.

= Support =

If you're stuck, ask me for help on [Twitter](http://twitter.com/phill_brown).

== Installation ==

1. Download and unzip the folder from [the WordPress plugins repository](http://wordpress.org/extend/plugins/context-manager/)
1. Upload the context-manager folder into to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Open the 'Appearance' menu item and click the 'Context Rules' link

== Screenshots ==

1. An example setup for a products section in a online shop

== Changelog ==

= 1.0.1 =
* [Bugfix]: Invalid foreach warning when no rules were added in get_rules()