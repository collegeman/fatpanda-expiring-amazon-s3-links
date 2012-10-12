=== Plugin Name ===
Contributors: aaroncollegeman
Donate link: http://fatpandadev.com
Tags: amazon, s3, aws, temporary links, shortcode, private
Requires at least: 2.9
Tested up to: 3.4
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A shortcode for generating temporary links to things stored privately on Amazon S3.

== Description ==

Everyone knows that Amazon S3 is the best place to host static files. 
But how do you use S3 to distribute access-restricted content - that eBook
that's for registered members only, or your band's latest album?

Fat Panda to the rescue!

With this plugin, you can easily generate temporary links to privately-hosted
content on S3. It couldn't be easier:

`[s3 bucket="your-S3-bucket" path="/path/to/restricted/file.whatever"]Download![/s3]`

The link generated will only be usable for five minutes, after which users
who click on it will simply see Amazon's standard 404 page.

You can control the expiration time using the *expires* attribute; this link
will expire after one hour:

`[s3 bucket="your-S3-bucket" path="/path/to/file" expires="60"]Download![/s3]`

Following **Installation* instructions to get this plugin up and running.


== Installation ==

1. Install this plugin in your WordPress site.
1. Activate this plugin through the 'Plugins' menu in WordPress.
1. Sign up for [Amazon AWS S3](http://aws.amazon.com/s3/).
1. Create a bucket in S3. Create folders to organize your files. Upload files - 
mark them as private (should be the default).
1. Install your AWS keys in WordPress. There are two ways. The first way is to
create constants in your `wp-config.php` file - the two constants are `FPEAS3_AWS_S3_ACCESS_ID`
and `FPEAS3_AWS_S3_SECRET`. The second way is to drop your keys into custom fields
in the Post or Page you're adding links to: `aws_s3_access_id` and `aws_s3_secret`
1. Add links to posts.
1. Publish. Rejoice.

== Changelog ==

= 0.1 =
* First release

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`