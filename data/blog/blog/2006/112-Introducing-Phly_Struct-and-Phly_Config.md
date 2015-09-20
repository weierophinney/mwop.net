---
id: 112-Introducing-Phly_Struct-and-Phly_Config
author: matthew
title: 'Introducing Phly_Struct and Phly_Config'
draft: false
public: true
created: '2006-05-19T17:00:00-04:00'
updated: '2006-05-19T22:54:56-04:00'
tags:
    - php
---
I often find myself needing a configuration module of some sort — for storing application parameters, bootstrapping, template variables, what have you. I typically will either:

1. Create a PHP file that creates and returns an array, and suck that in via [include](http://www.php.net/include), or
2. Create an INI file and suck it in via [parse_ini_file](http://www.php.net/parse_ini_file), or
3. Create an XML file and suck it in via [SimpleXML](http://www.php.net/simplexml).

The first method gives great flexibility of structure and types, but isn't portable to other languages (well, not easily; you could turn it into JSON, or serialize it, etc). The second method (INI files) is handy because the syntax is so concise, and can translate to other projects in other languages easily if necessary; however, you can only easily go two levels deep (using [sections] in the file). The third method is very portable, and allows nested structures — but doesn't allow usage of many specific PHP types.

I find, however, that each has their place. The problem, however, is: once I bring them into my project, how can I access them? Better yet, would there be a way to bring in configurations of many types and still access them all in the same way?

Not happy with solutions out there, I did the only logical thing: I reinvented the wheel, and added some new tread of my own.

<!--- EXTENDED -->

The solution started out as [Phly_Config](http://weierophinney.net/phly/index.php?package=Phly_Config), which was going to be a generic storage container with several adapters for loading configurations. However, in talking with [Paul](http://paul-m-jones.com/blog/), he noticed two tools emerging. To quote him:

1. A general-purpose array/object/struct implementation.
2. Config adapters to push data into the struct.

I got to thinking about it, and decided he was right -- and so [Phly_Struct](http://weierophinney.net/phly/index.php?package=Phly_Struct) was born.

Now that I've hyped it up, just what do the tools offer? I think examples sum it up best. First, `Phly_Struct`:

```php
$array = array(
    'key1 => 'value1',
    'key2 => array(
        'subkey1' => array(
            'subsubkey1' => 'subsubvalue1',
            'subsubkey2' => 'subsubvalue2'
        ),
        'subkey2' => 'subvalue2'
    ),
    'key3' => 'value3'
);
$struct = new Phly_Struct($array);

echo 'The value of the second subkey is ' . $struct->key2->subkey2;
$struct->key3 = 'Some new value!';
$struct->key2->subkey1->subsubkey1 = 'I am a third level value';

// Let's loop over values...
foreach ($struct->key2 as $key => $value) {
    if (is_scalar($value)) {
        echo "Key $key: $value\n";
    }
}

// Let's grab a sub array to pass elsewhere
$subarray = $struct->key2->subkey1->toArray();
```

As you can see, `Phly_Struct` provides object oriented access to values. You can retrieve and set them just as you would object properties. Additionally, since it implements the [SPL](http://www.php.net/spl) `Iterator`, you can loop over a `Phly_Struct` just like you would an array.

Now let's turn to `Phly_Config`:

```php
$array = array(
    'key1 => 'value1',
    'key2 => array(
        'subkey1' => array(
            'subsubkey1' => 'subsubvalue1',
            'subsubkey2' => 'subsubvalue2'
        ),
        'subkey2' => 'subvalue2'
    ),
    'key3' => 'value3'
);
// Load an in-memory array into the config
Phly_Config::load($array);

// Grab configuration; Phly_Config is a singleton, so this can be done at any
// time, and any configurations loaded will be available
$config = Phly_Config::getInstance();

// Load an INI file into the config, under the namespace 'ini'
Phly_Config::load('/path/to/some/config.ini', 'ini');

// Load an XML file into the config, under the namespace 'xml'
Phly_Config::load('/path/to/some/config.xml', 'xml');

// Load a PHP file returning an array into the config, under the namespace 'php'
Phly_Config::load('/path/to/some/config.php', 'php');

// By default, values are retrieved from the 'default' namespace, which is used
// if no namespace was specified when loading a config:
echo 'I am the third value: ' . $config->key3;

// Now grab the db host from the 'ini' namespace
$host = $config->ini->db->host;

// Set the db host in the xml config namespace
$config->xml->db->host = $host;

// Let's get back the whole db array from the xml config namespace
$db_data = $config->xml->db->toArray();
```

Since `Phly_Config` simply loads configuration structures into `Phly_Struct`s, you get all the syntactic yumminess of that class for free. Additionally, since `Phly_Config` is a singleton class, you can load configurations at different points in the code, and have access to those values anywhere else.

Now, you *can* set values with `Phly_Config`. However, they will only affect the current instance; nothing gets written back to the configuration files at this point. Typically, configuration is a one way street; you want to load configuration data and access it from your program, but not change it. I may in the future add implementations for writing configurations, or at least spitting out the formatted output. In the meantime, you can always serialize the object either with PHP or as JSON.

Comments, bugfixes, and other feedback always welcome!
