<?php
use PhlyBlog\Module;
use PhlyBlog\Compiler;
use PhlyBlog\CompilerOptions;
use Zend\Loader\AutoloaderFactory;
use Zend\Module\Listener;
use Zend\Module\Manager as ModuleManager;
use Zend\Mvc\Application;
use Zend\Mvc\Bootstrap;
use Zend\View\Model\ViewModel;

// Get locator, and grab renderer and view from it
$config   = Module::$config;
$locator  = $application->getLocator();
$view     = $locator->get('Zend\View\View');
$view->events()->clearListeners('renderer');
$view->events()->clearListeners('response');

// Setup renderer for layout, and layout view model
if ($config['blog']['view_callback'] && is_callable($config['blog']['view_callback'])) {
    $callable = $config['blog']['view_callback'];
    call_user_func($callable, $view, $config, $locator);
}

// Prepare compiler and grab tag cloud
$options   = new CompilerOptions($config['blog']['options']);
$postFiles = new Compiler\PhpFileFilter($config['blog']['posts_path']);
$writer    = new Compiler\FileWriter();
$compiler  = new Compiler($postFiles, $view, $writer, $options);

// Create tag cloud
if ($config['blog']['cloud_callback'] 
    && is_callable($config['blog']['cloud_callback'])
) {
    $callable = $config['blog']['cloud_callback'];
    echo "Creating and rendering tag cloud...";
    $cloud = $compiler->compileTagCloud();
    call_user_func($callable, $cloud, $view, $config, $locator);
    echo "DONE!\n";
}

// compile!

echo "Compiling paginated entries...";
$compiler->compilePaginatedEntries();
echo "DONE!\n";

echo "Compiling paginated entries by year...";
$compiler->compilePaginatedEntriesByYear();
echo "DONE!\n";

echo "Compiling paginated entries by month...";
$compiler->compilePaginatedEntriesByMonth();
echo "DONE!\n";

echo "Compiling paginated entries by date...";
$compiler->compilePaginatedEntriesByDate();
echo "DONE!\n";

echo "Compiling paginated entries by tag...";
$compiler->compilePaginatedEntriesByTag();
echo "DONE!\n";

echo "Compiling entries...";
$compiler->compileEntryViewScripts();
echo "DONE!\n";

echo "Compiling main Atom feed...";
$compiler->compileRecentFeed('atom');
echo "DONE!\n";

echo "Compiling main RSS feed...";
$compiler->compileRecentFeed('rss');
echo "DONE!\n";

echo "Compiling Atom tag feeds...";
$compiler->compileTagFeeds('atom');
echo "DONE!\n";

echo "Compiling RSS tag feeds...";
$compiler->compileTagFeeds('rss');
echo "DONE!\n";
