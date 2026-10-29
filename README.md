# DesignMLP Essentials

WordPress plugin with the recurring fixes that [DesignMLP](https://designmlp.com/) applies to the websites it builds, split into modules that can be enabled one by one (Settings > DesignMLP Essentials).

## Modules

| Module | What it does |
| --- | --- |
| Viewport zoom | Replaces the viewport meta tag added by the theme (Divi 4 and Divi 5) with one without `maximum-scale` and `user-scalable`, so visitors can zoom the page. |

## Requirements

- WordPress 7.0 or later
- PHP 8.2 or later

## Installation

Download the zip from the [latest release](https://github.com/DesignMLP/designmlp-essentials/releases) and upload it in Plugins > Add Plugin > Upload Plugin.
The "Source code" archives do not include the `vendor` folder: they work only after running `composer install`.

## Development

```
composer install
```

The code uses the `DesignMLP\Essentials` namespace with a PSR-4 autoloader (`includes/` and `modules/`). Translations are in `languages/`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

[GPL-3.0-or-later](LICENSE)
