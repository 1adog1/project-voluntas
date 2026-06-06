# Project Voluntas

Project Voluntas is a webapp designed to allow users to authenticate their Eve Online Character when making a Google Form submission.

## Requirements

This app requires the following:

* A Web Server Such As:
  * NGINX ≥ 1.18
    * The `root` option pointing to the `/public` folder
    * The `index` option set to `index.php`
    * The `try_files` option set to `$uri /index.php$is_args$args`
  * Apache ≥ 2.4
    * The `DocumentRoot` config option set to `/public`
    * The `FallbackResource` config option set to `/index.php`
* PHP ≥ 8.1
  * The `curl` Built-In Extension
  * The `pdo_mysql` Built-In Extension
  * The `openssl` Built-In Extension
* An SQL Server
  * If you are using MySQL, the Authentication Method **MUST** be the Legacy Version. PDO does not support the use of `caching_sha2_password` Authentication.
* A Registered Eve Online Application with the `esi-search.search_structures.v1` scope.
  * This can be setup via the [Eve Online Developers Site](https://developers.eveonline.com/).
* [When Using The Neucore Authentication Method] A Neucore Application
  * The application needs the `app-chars` and `app-groups` roles added, along with any groups that you want to be able to set access roles for.

## Setup

* Rename the Configuration File in `/config/config.ini.dist` to `/config/config.ini` and setup as needed.

## Using Environment Variables Instead of a Config File

* You can find environment variable keys associated with each config value in the comments of `/config/config.ini.dist`.
* Some variables are required, some have defaults, and some are only needed in specific circumstances. These are listed in the comments of the file.
* The app only supports either Environment Variables or a Config File, not both.
  * The Config File always takes priority. To use Environment Variables, delete `/config/config.ini` if it exists.