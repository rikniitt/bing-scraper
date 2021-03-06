Bing Scraper
=====================================

[Bing homepage image gallery](http://www.bing.com/gallery/) scraper implemented with PHP.


# Requirements

 * PHP version >= 5.5.9
 * SQLite3 and php5-sqlite

## Dependencies

Project uses following [composer](https://getcomposer.org/doc/) [dependencies](https://packagist.org/):

 * [symfony/console](https://symfony.com/doc/current/components/console.html)
 * [m1/env](https://github.com/m1/Env)
 * [illuminate/database](https://github.com/illuminate/database)
 * [monolog/monolog](https://github.com/Seldaek/monolog)
 * [psy/psysh](https://github.com/bobthecow/psysh)
 * [pimple/pimple](https://github.com/silexphp/Pimple)
 * [symfony/process](https://symfony.com/doc/current/components/process.html)


# Install

If you are using [vagrant](https://www.vagrantup.com/docs/index.html) with 
[virtualbox](https://www.virtualbox.org/wiki/Downloads), then just `vagrant up`. Even if not, see *./Vagrantfile* 
how to install the project. It contains "inline shell" scripts which should help you to 
install the project.


# Usage

Run `bin/bing-scraper hpia:scrape` to start scraping images from homepage image galler. 
Use `bin/bing-scraper hpia:download` to download scraped images.

You can see all available commands by running `bin/bing-scraper` without arguments.
