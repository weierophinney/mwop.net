---
id: 105-PHP-error-reporting-for-Perl-users
author: matthew
title: 'PHP error reporting for Perl users'
draft: false
public: true
created: '2006-03-27T23:10:00-05:00'
updated: '2006-03-28T09:19:35-05:00'
tags:
    - perl
    - php
---
On [perlmonks](http://www.perlmonks.org) today, a user was needing to maintain a PHP app, and wanted to know what the PHP equivalent of `perl -wc script.pl` was — specifically, they wanted to know how to run a PHP script from the commandline and have it display any warnings (ala perl's strict and warnings pragmas).

Unfortunately, there's not as simple a way to do this in PHP as in perl. Basically, you need to do the following:

- **To display errors:**
  - In your `php.ini` file, set `display_errors = On`, **or**
  - In your script, add the line `ini_set('display_errors', true);`

- **To show notices, warnings, errors, deprecation notices:**
  - In your `php.ini` file, set `error_reporting = E_ALL | E_STRICT`, **or**
  - In your script, add the line `error_reporting(E_ALL | E_STRICT);`

Alternatively, you can create a file with the lines:

```php
<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
```

and then set the `php.ini` setting `auto_prepend_file` to the path to that file.

**NOTE: do not do any of the above on a production system!** PHP's error messages often reveal a lot about your applications, including file layout and potential vectors of attack. Turn `display_errors` off on production machines, set your `error_reporting` somewhat lower, and `log_errors` to a file so you can keep track of what's going on on your production system.

The second part of the question was how to run a PHP script on the command line. This is incredibly simple: `php myscript.php`. No different than any other scripting language.

You can get some good information by using some of the switches, though. **`-l`** turns the PHP interpreter into a linter, and can let you know if your code is well-formed (which doesn't necessarily preclude runtime or parse errors). **`-f`** will run the script through the parser, which can give you even more information. I typically bind these actions to keys in vim so I can check my work as I go.

If you plan on running your code *solely* on the commandline, add a shebang to the first line of your script: `#!/path/to/php`. Then make the script executable, and you're good to go. This is handy for cronjobs, or batch processing scripts.

All of this information is readily available in [the PHP manual](http://www.php.net/manual), and the commandline options are always available by passing the `--help` switch to the PHP executable. So, start testing your scripts already!
