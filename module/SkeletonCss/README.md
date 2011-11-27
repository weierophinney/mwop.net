Skeleton CSS
============

From http://getskeleton.com:

> Skeleton is a small collection of CSS & JS files that can help you rapidly
> develop sites that look beautiful at any size, be it a 17" laptop screen or
> an iPhone.

This module provides the assets for Skeleton in a format that can be readily
dropped into a Zend Framework 2 application as a module.

Installation
------------

Simply drop this into your "module/" directory. To expose the CSS, images, and
JS under your document root, you have several options:

### 1 - Copy them

Probably the easiest way is to simply copy them:

    cp -a module/SkeletonCss/public/css public/css/SkeletonCss
    cp -a module/SkeletonCss/public/js public/js/SkeletonCss
    cp -a module/SkeletonCss/public/images public/images/SkeletonCss

### 2- Symlink them

If you are on a \*nix-based system, you can symlink.

    cd public/css/
    ln -s ../../../module/SkeletonCss/public/css SkeletonCss
    cd ../js
    ln -s ../../../module/SkeletonCss/public/js SkeletonCss
    cd ../images
    ln -s ../../../module/SkeletonCss/public/images SkeletonCss

This is also possible on Windows Server 2003 and above; however, you will have
to look up the methodology yourself at this time.

### 3- Use server-based aliasing

On Apache, you can use mod_alias to accomplish this. The most direct way is to
specify aliases for each module:

    Alias /css/SkeletonCss/ /path/to/site/module/SkeletonCss/public/css/
    Alias /js/SkeletonCss/ /path/to/site/module/SkeletonCss/public/js/
    Alias /images/SkeletonCss/ /path/to/site/module/SkeletonCss/public/images/

Alternately, you could use AliasMatch to condense this and serve many modules,
assuming they follow the same directory layout:

    AliasMatch /(css|js|images)/([^/]+)/(.*) /path/to/site/module/$2/public/$1/$3

I personally like this approach as it makes it trivial for me to keep my assets
module-specific, and thus managed as separate submodule projects.

Similar functionality exists on other web servers; check your server's
documentation for ideas on how you might accomplish this.

### Notes

You typically should not directly alter the files under a module. As such, the
last two examples above (symlinking and aliasing) are very good techniques.
However, if you _must_ alter the files, I recommend method 1 above (copying),
and then altering the _copies_. This allows you to version those files, while
retaining the module's integrity.
