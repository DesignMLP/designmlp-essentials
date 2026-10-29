# Changelog

All notable changes to DesignMLP Essentials are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and the project follows [Semantic Versioning](https://semver.org/).
The "Changelog" section of `readme.txt` is generated from this file when a version is released.

## [Unreleased]

### Added

- Module system: each module can be enabled or disabled and declares its default state.
- Settings page (Settings > DesignMLP Essentials), for administrators only, with a Settings link in the plugins list.
- Viewport zoom module: replaces the viewport meta tag added by the theme (Divi 4 and Divi 5) with one without `maximum-scale` and `user-scalable`, so visitors can zoom the page. The content can be changed with the `designmlp_viewport_content` filter.
- Italian translation.
- Plugin settings removed from the database when the plugin is deleted.
