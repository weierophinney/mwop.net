<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('190-Setting-up-your-Zend_Test-test-suites');
$entry->setTitle('Setting up your Zend_Test test suites');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1221159600);
$entry->setUpdated(1221313060);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'mvc',
  2 => 'oop',
  4 => 'testing',
  5 => 'zend framework',
));

$body =<<<'EOT'
<p>
    Now that <a href="http://framework.zend.com/manual/en/zend.test.html">Zend_Test</a>
    has shipped, developers are of course asking, "How do I setup my test
    suite?" Fortunately, after some discussion with my colleagues and a little
    experimenting on my one, I can answer that now.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    <a href="http://phpunit.de">PHPUnit</a> offers a variety of methods for
    setting up test suites, some trivial and some complex. The Zend Framework
    test suite, for instance, goes for a more complex route, adding
    component-level suites that require a fair amount of initial setup, but
    which allow us fairly fine-grained control.
</p>

<p>
    However, testing and test automation should be easy and the complex approach
    is overkill for most of our applications. Fortunately, PHPUnit offers some
    other methods that make doing so relatively simple. The easiest method is to
    use an <a href="http://www.phpunit.de/pocket_guide/3.2/en/appendixes.configuration.html">XML configuration file</a>.
</p>

<p>
    As an example, consider the following:
</p>

<div class="example"><pre><code class="language-xml">
&lt;phpunit&gt;
    &lt;testsuite name=\&quot;My Test Suite\&quot;&gt;
        &lt;directory&gt;./&lt;/directory&gt;
    &lt;/testsuite&gt;

    &lt;filter&gt;
        &lt;whitelist&gt;
            &lt;directory suffix=\&quot;.php\&quot;&gt;../library/&lt;/directory&gt;
            &lt;directory suffix=\&quot;.php\&quot;&gt;../application/&lt;/directory&gt;
            &lt;exclude&gt;
                &lt;directory suffix=\&quot;.phtml\&quot;&gt;../application/&lt;/directory&gt;
            &lt;/exclude&gt;
        &lt;/whitelist&gt;
    &lt;/filter&gt;

    &lt;logging&gt;
        &lt;log type=\&quot;coverage-html\&quot; target=\&quot;./log/report\&quot; charset=\&quot;UTF-8\&quot;
            yui=\&quot;true\&quot; highlight=\&quot;true\&quot;
            lowUpperBound=\&quot;50\&quot; highLowerBound=\&quot;80\&quot;/&gt;
        &lt;log type=\&quot;testdox-html\&quot; target=\&quot;./log/testdox.html\&quot; /&gt;
    &lt;/logging&gt;
&lt;/phpunit&gt;
</code></pre></div>

<p>
    First thing to note, relative paths are relative to the configuration file.
    This allows you to run your tests from anywhere in your tests tree, Second,
    providing a <code>directory</code> directive to the
    <code>testsuite</code> directive scans for all files ending in "Test.php" in
    that directory, meaning you don't have to keep a list of your test cases
    manually. It's a great way to automate the suite. Third, the filter
    directive allows us to determine what classes to include and/or exclude
    from coverage reports. Finally, the <code>logging</code> directive lets us
    specify what kinds of logs to create and where.
</p>

<p>
    Drop the above into "tests/phpunit.xml" in your application, and you can
    start writing test cases and running the suite immediately, using the
    following command:
</p>

<div class="example"><pre><code class="language-text">
% phpunit --configuration phpunit.xml
</code></pre></div>

<p>
    I like to group my test cases by type. I have controllers, models, and often
    library code, and need to keep the tests organized both on the filesystem as
    well as for running the actual tests. There are two things I do to
    facilitate this.
</p>

<p>
    First, I create directories. For instance, I have the following hierarchy in
    my test suite:
</p>

<div class="example"><pre><code class="language-text">
tests/
    phpunit.xml
    TestHelper.php
    controllers/
        IndexControllerTest.php (contains IndexControllerTest)
        ErrorControllerTest.php (contains ErrorControllerTest)
        ...
    models/
        PasteTest.php           (contains PasteTest)
        DbTable/
            PasteTest.php       (contains DbTable_PasteTest)
        ...
    My/
        Form/
            Element/
                SimpleTextareaTest.php
</code></pre></div>

<p>
    "controllers/" contains my controllers, "models/" contains my models. If I
    were developing a modular application, I'd have something like
    "blog/controllers/" instead. Library code is given the same hierarchy as is
    found in my "library/" directory.
</p>

<p>
    Second, I use docblock annotations to group my tests. I add the following to
    my class-level docblock in my controller test cases:
</p>

<div class="example"><pre><code class="language-php">
/**
 * @group Controllers
 */
</code></pre></div>

<p>
    Models get the annotation "@group Models", etc. This allows me to run
    individual sets of tests on demand:
</p>

<div class="example"><pre><code class="language-text">
% phpunit --configuration phpunit.xml --group Controllers
</code></pre></div>

<p>
    You can specify multiple @group annotations, which means you can separate
    tests into modules, issue report identifiers, etc; additionally, you can add
    the annotations to individual test methods themselves to have really
    fine-grained test running capabilities.
</p>

<p>
    Astute readers will have noticed the "TestHelper.php" file in that directory
    listing earlier, and will be wondering what that's all about.
</p>

<p>
    A test suite needs some environmental information, just like your
    application does. It may need a default database adapter, altered
    include_paths, autoloading set up, and more. Here's what my TestHelper.php
    looks like:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php
/*
 * Start output buffering
 */
ob_start();

/*
 * Set error reporting to the level to which code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Set default timezone
 */
date_default_timezone_set('GMT');

/*
 * Testing environment
 */
define('APPLICATION_ENV', 'testing');

/*
 * Determine the root, library, tests, and models directories
 */
$root        = realpath(dirname(__FILE__) . '/../');
$library     = $root . '/library';
$tests       = $root . '/tests';
$models      = $root . '/application/models';
$controllers = $root . '/application/controllers';

/*
 * Prepend the library/, tests/, and models/ directories to the
 * include_path. This allows the tests to run out of the box.
 */
$path = array(
    $models,
    $library,
    $tests,
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * Register autoloader
 */
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

/**
 * Store application root in registry
 */
Zend_Registry::set('testRoot', $root);
Zend_Registry::set('testBootstrap', $root . '/application/bootstrap.php');

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $library, $models, $controllers, $tests, $path);
</code></pre></div>

<p>
    The above ensures that my APPLICATION_ENV constant is set appropriately,
    that error reporting is appropriate for tests (i.e., I want to see
    <em>all</em> errors), and that autoloading is enabled. Additionally, I place
    a couple items in my registry -- the bootstrap and test root directory.
</p>

<p>
    In each test case file, I then do a require_once on this file. In future
    versions of PHPUnit, you'll be able to specify a bootstrap file in your
    configuration XML that gets pulled in for each test case, and you'll be able
    to even further automate your testing environment setup.
</p>

<p>
    Hopefully this will get you started with your application testing; what are
    you waiting for?
</p>
EOT;
$entry->setExtended($extended);

return $entry;
