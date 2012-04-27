<?php
use Zend\View\Model\ViewModel;

echo "Retrieving Github activity links\n";

$locator = $application->getLocator();
$reader  = $locator->get('GithubFeed\AtomReader');

try {
    $data = $reader->read();
} catch (Exception $e) {
    echo "Error retrieving Github atom feed:\n";
    echo $e->getMessage(), "\n";
    exit(1);
}

$model = new ViewModel(array(
    'github' => $data,
));
$model->setTemplate('github-feed/links');

$renderer = $locator->get('Zend\View\Renderer\PhpRenderer');
$content  = $renderer->render($model);
file_put_contents('data/github-feed-links.phtml', $content);
echo "[DONE]";
