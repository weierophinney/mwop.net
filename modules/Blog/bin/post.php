<?php
/**
 * Workflow:
 *
 * php post.php <filename>
 *
 * - include file
 * - file should return a EntryEntity instance
 * - POST the entity
 *   - Use api key and provide header
 *   - cast entity to array and use array as POST parameters
 *   - post to /blog on configured server
 * - Retrieve Location from response headers, and return as status
 */

use Blog\EntryEntity,
    Zend\Console\Exception as GetoptException,
    Zend\Console\Getopt,
    Zend\Http\Client as HttpClient,
    Zend\Loader\AutoloaderFactory;

require_once 'Zend/Loader/AutoloaderFactory.php';
require __DIR__ . '/../../CommonResource/autoload_register.php';
require __DIR__ . '/../autoload_register.php';

AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array()
));

try {
    $options = new Getopt(array(
        'help|h'     => 'Print this help message',
        'config|c-s' => 'Path to configuration file',
        'post|p=s'   => 'Path to file returning a post',
    ));
} Catch (GetoptException $e) {
    echo $e->getUsageMessage();
    exit(1);
}

$usage = function($message = '', $exit = 1) use ($options) {
    echo $message, "\n\n";
    echo $options->getUsageMessage(), "\n";
    echo "Config files should return PHP arrays, and contain the keys \n"
       . "'apikey' and 'url', referring to the API key to use and URI \n"
       . "to the blog, respectively.\n";
    exit($exit);
};

if ($options->getOption('h')) {
    $usage('', 0);
}

$postFile = $options->getOption('p');
if (!file_exists($postFile)) {
    $usage("Invalid file provided!", 1);
}
if (!is_readable($postFile)) {
    $usage("File provided is not readable!", 1);
}

$post = include $postFile;

if (!$post instanceof EntryEntity) {
    $usage("Entry file returned an invalid entity: " . (is_object($post) ? get_class($post) : var_export($post, 1)), 1);
}

$configFile = __DIR__ . '/../configs/module.config.php';
if (isset($options->c)) {
    $configFile = $options->getOption('c');
}
$config     = include $configFile;

if (!$config) {
    $usage("Config file not found or invalid!", 1);
}

if (!isset($config['apikey'])) {
    $usage('Missing API key in configuration file!', 1);
}
$apiKey = $config['apikey'];
if (!isset($config['url'])) {
    $usage('Missing blog URL for posting!', 1);
}
$url = $config['url'];

$client  = new HttpClient;
$client->setUri($url);
$client->setMethod('POST');
$client->setParameterPost($post->toArray());
$request = $client->getRequest();
$request->headers()->addHeaderLine('X-MWOP-APIKEY', $apiKey);
$response = $client->send();

$status = $response->getStatusCode();
if ($status != 201) {
    $string  = $client->getLastRawRequest() . "\n\n";
    $string .= $response->toString();
    $logfile = getcwd() . '/post.log.html';
    file_put_contents($logfile, $string);
    $message = "Post creation failed; review $logfile for details";
    $usage($message, 1);
}
$headers = $response->headers();
if (!$headers->has('Location')) {
    $usage("Post was successful, but unable to determine URL", 0);
}
$location = $headers->get('Location')->getFieldValue();
echo "Post created: $location";
exit(0);

