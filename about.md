---
layout: page
title: About
---

Gyro-PHP is a PHP application framework offering Model-View-Controller (MVC) pattern. It also comes with an extensive behavior layer based upon the command design pattern. This makes it easy to write complex, yet transaction-safe code. Commands offer undo functionality, too.

## Out of the box solutions

Gyro-PHP comes with a set of modules and contributions to make every day web development tasks easier.
User management, full text searching, sitemaps, CSS and JavaScript compressing and many other common features:
It's all already there, and new contributions are added frequently.

## Performance

Gyro-PHP is profiled and optimized regularly using real word applications, not abstract test cases.
Of course, Gyro-PHP can - and should! - be used with opcode caches as XCache, APC, and others.

Gyro-PHP's performance can even be enhanced by utilizing memory for cache and session handling.
This is as easy as stating Load::enable_module('cache.xcache').

However, having fast code alone does not make a high performance web site. Gyro-PHP additionally supports developing really fast sites by:

- *Caching*: Flexible caching policies can be easily applied, allowing fine-grained cache control even for a single page or per route.
- *Send "Not Modified" header*: When serving pages from cache, Gyro-PHP send a "304 Not modified" header if possible, rather than resending the whole page content.
- *GZip-Compression*: Gyro-PHP compresses all content by default, if the browser accepts it. Since most visitors support gzip compression, cache data is stored compressed right away.
- *Javascript and CSS compression*: Using the JCSSManager-module all your javascript and css files get cleaned up and combined into just one file - which additionally is compressed using gzip afterwards, saving bandwith and requests.
- *Content Distribution*: Serving all your images from a different domain is as easy as typing define('APP\_URL\_IMAGES', 'http://your.image.domain/'). Really.

Using Gyro-PHP, a lot of the 14 rules for faster-loading web sites can be fullfilled easily.

## Internationalization and Localization? Check!

Gyro-PHP is developed with localization and translations in mind. It will respect language settings,
when it comes to outputing or inputing numbers, dates, etc. Translations are simple to use and
always use the string to translate as a fallback, so a site stays usable even if a translation is missing. And of course it supports UTF-8, but also any other charset.

## Safety

Gyro-PHP has build in security features for the four most common types of attacks:

- *SQL injections*: The build-in database abstraction layer safely escapes all SQL queries.
- *Cross site scripting (XSS)*: Gyro-PHP helps to write safe templates and code by making the safest way
of outputing data the easiest. Actually outputing an unescaped string in a template is much more effort
than escaping it. Additionally, creating of HTML input widgets is wrapped up in a safe manner.
- *Cross-site request forgery (CSRF/XSRF)*: Gyro-PHP supports unique tokens for every POST request and makes using them a snap.
- *Mail header injection*: The build in Gyro-PHP mail class will check for header injections and refuse to send mail if an attack is detected.
