---
layout: post
title: 'Installing and setting up a new project'
description: 'First steps using the Gyro-PHP Framework'
---

This tutorial shows how to install Gyro and to set up a new project using the systemupdate module.

This guide is for \*nix systems only.

## Downloading and installing Gyro

First check out Gyro-PHP from the repository.

Once you extracted the Gyro files, there is a directory called `gyro`, containing three subdirectories `core`, `install`, and `modules`.
This directory is referred to as the **Gyro root directory**.

There is also a directory called `contributions` containing additional modules.

Gyro is now installed on your system.

## Setting up a new project

### Creating directory structure

Gyro supports creating new projects with a set of scripts and the systemupdate module.

First, the application's directory must be created. There is a shell script to do this, which takes two parameters:

- The path to the project's directory, absolute or relative. Missing directories will be created.
- The path to the Gyro root directory. This must be absolute!

You invoke the script like this:

{% highlight bash %}
{Gyro root}/install/makedirs.sh {/path/to/your/project} {Gyro root}
{% endhighlight %}

For example if Gyro is located at `/var/lib/gyro-php/gyro` and your project goes to `/var/www/project`, the command line is

{% highlight bash %}
/var/lib/gyro-php/install/makedirs.sh /var/www/project /var/lib/gyro-php
{% endhighlight %}


The script will create a couple of directories and copy some basic files required for a Gyro project.
Right beneath your project directory you will find the following folders:

- `app`: This is were your application code resides. It is called the application root.
There are a couple of subdirecrories here for model, view, controller, behaviour, and the web root.
- `data`: Used for keeping project related data, like specs, documentation, and such.
- `tmp`: Temporary files are stored here, like logs, and compiled views. The webserver must have write access to this directory.

If you use version control: Now is a good time to initially upload your project into your repository.

### Configuring webserver and database

For the next steps, we need the webserver and the database to be ready, so

- Create a virtual host for your project and point it to `app/www` beneath you project directory.
- Create a database and - if you like - a database user for your project.

Now copy `app/config.php.example` to `app/config.php` and adjust the following properties:

{% highlight php %}
<?php
/**
 * The domain of the app, excluding 'http://'.
 */
define('APP_URL_DOMAIN', 'fill in!');
/**
 * DB Constants
 */
define('APP_DB_TYPE', 'mysql');
define('APP_DB_NAME', 'fill in!');
define('APP_DB_USER', 'fill in!');
define('APP_DB_PWD', 'fill in!');
define('APP_DB_HOST', 'localhost');
/**
 * Mail related
 */
define('APP_MAIL_SENDER', 'fill in!');
define('APP_MAIL_ADMIN', 'fill in!');
define('APP_MAIL_SUPPORT', 'fill in!');
{% endhighlight %}

The `APP\_DB\_\*` constants contain the name, user, password, and host of your database. Currently only "mysql" is fully supported as type, so don't change this.

The `APP\_MAIL\_\*` constants contain mail addresses:

- `APP\_MAIL\_SENDER`: The mail address to be used as sender by default, when sending mail.
- `APP\_MAIL\_ADMIN`: The admin's mail address. This mail gets system mails send.
- `APP\_MAIL\_SUPPORT`: Mail for contacts, feedback, and everything that involves real people.

### More configuration options

Gyro allows several instances of your project to run, like a development version, a staging distribution,
and a live system. These are called *installations*. Therefore, configuration is split in two files:

- `app/config.php`: This is for local settings, like the database, the host, and the Gyro path.
- `app/constants.php`: This contains configuration settings that are the same for all installations, like the title of the application, the language, encoding, and so on.

Take a look at `app/constants.php` and change it to fit your needs, too.

If you use version control, it is a good idea to exclude `app/config.php` from your repository.

### Running systemupdate to install tables

While everything now is configured properly, the Gyro core tables are still missing in the database. Fortunately, we can let the *systemupdate* module do all the required steps.

To use *systemupdate*, we also need the Gyro console. Open `app/modules.php` in your favorite text editor and make sure the modules *console*, and *systemupdate* are enabled:

{% highlight php %}
<?php
Load::enable_module('console');
Load::enable_module('systemupdate');
Load::enable_module('staticmainpage');
{% endhighlight %}

The *staticmainpage* module isn't really needed, but allows to easily check if everything is running.

If all required modules are enabled, run

{% highlight bash %}
{Gyro root}/modules/console/install/install.sh {/path/to/your/project}
{% endhighlight %}

This will copy run_console.php to your application root directory. Change to your project directory and run

{% highlight bash %}
php app/run_console.php systemupdate
{% endhighlight %}

If everything is OK, there should be a couple of success messages being displayed.

If you now point your browser to your project domain, you should see the *staicmainpage* module's default message:
"It works, GYRO is ready to be used on your system". Congratulations!