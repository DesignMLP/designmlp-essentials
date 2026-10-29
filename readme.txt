=== DesignMLP Essentials ===
Tags: divi, accessibility, viewport, zoom
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 0.1.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Recurring fixes for DesignMLP websites, split into modules that can be enabled one by one.

== Description ==

DesignMLP Essentials collects the fixes that DesignMLP applies to the websites it builds, so they do not have to be repeated on every site. Each fix is a module that can be enabled or disabled in Settings > DesignMLP Essentials.

= Modules =

* **Viewport zoom**: some themes, such as Divi 4 and Divi 5, add a viewport meta tag with `maximum-scale` and `user-scalable`, which prevents visitors from zooming the page with two fingers on many phones (WCAG 2.2, success criterion 1.4.4). The module replaces it with `width=device-width, initial-scale=1`. It runs only when the active theme is one it recognises, so a site never ends up with two viewport meta tags.

A module that finds nothing to fix on a site stays inactive and starts again by itself if the site goes back to a supported theme.

= For developers =

The content of the viewport meta tag can be changed with the `designmlp_viewport_content` filter:

`add_filter( 'designmlp_viewport_content', fn() => 'width=device-width, initial-scale=1, viewport-fit=cover' );`

== Installation ==

1. Download the plugin zip from the latest release on GitHub: https://github.com/DesignMLP/designmlp-essentials/releases
2. In WordPress go to Plugins > Add Plugin > Upload Plugin, choose the zip and click Install Now.
3. Activate the plugin. Modules enabled by default start working immediately.
4. To enable or disable modules, go to Settings > DesignMLP Essentials.

The zip from a release already contains everything the plugin needs. The "Source code" archives on GitHub do not include the `vendor` folder and will not work without running `composer install`.

== Frequently Asked Questions ==

= Does the plugin work without Divi? =

Yes. Modules that need a specific theme stay inactive when that theme is not active, and the settings page shows them as not running.

= What happens to the settings when the plugin is deleted? =

They are removed from the database. Deactivating the plugin keeps them.

== Changelog ==

= Unreleased =
* Module system: each module can be enabled or disabled and declares its default state.
* Settings page (Settings > DesignMLP Essentials), for administrators only, with a Settings link in the plugins list.
* Viewport zoom module: replaces the viewport meta tag added by the theme (Divi 4 and Divi 5) with one without `maximum-scale` and `user-scalable`, so visitors can zoom the page. The content can be changed with the `designmlp_viewport_content` filter.
* Italian translation.
* Plugin settings removed from the database when the plugin is deleted.
