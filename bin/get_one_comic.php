<?php
ini_set('display_errors', true);
error_reporting(-1);

require_once __DIR__ . '/../library/zf2/Zend/Loader/ClassMapAutoloader.php';
$classmap = new Zend\Loader\ClassMapAutoloader(array(
    __DIR__ . '/../library/.classmap.php',
));
$classmap->register();

use mwop\Comic\ComicFactory,
    mwop\Comic\ComicSource,
    Zend\Console\Getopt,
    Zend\Console\Exception as GetoptException;

$scan = array_merge(
    ComicSource\GoComics::supports(), 
    ComicSource\Dilbert::supports(),
    ComicSource\ForBetterOrForWorse::supports(),
    ComicSource\NotInventedHere::supports(),
    ComicSource\UserFriendly::supports(),
    ComicSource\CtrlAltDel::supports(),
    ComicSource\Xkcd::supports(),
    ComicSource\BasicInstructions::supports(),
    ComicSource\ScenesFromAMultiverse::supports(),
    ComicSource\GarfieldMinusGarfield::supports(),
    ComicSource\PennyArcade::supports(),
    ComicSource\FoxTrot::supports()
);
ksort($scan);

$comic = false;
$list  = false;

try {
    $options = new Getopt(array(
        'help|h'    => 'Print this help message',
        'comic|c-s' => 'Comic to retrieve',
        'list|l'    => 'List comics available',
    ));
} catch (GetoptException $e) {
    file_put_contents('php://stderr', $e->getUsageMessage());
    exit(1);
}

if ($options->getOption('h')) {
    echo $options->getUsageMessage();
    exit(0);
}

if ($options->getOption('l')) {
    echo "Supported comics:\n";
    $mapped = array_map(function($name) {
        return strlen($name);
    }, array_keys($scan));
    $longest = array_reduce($mapped, function($count, $longest) {
        $longest = ($count > $longest) ? $count : $longest;
        return $longest;
    }, 0);
    foreach ($scan as $alias => $name) {
        printf("    %${longest}s: %s\n", $alias, $name);
    }
    exit(0);
}

if (!isset($options->c)) {
    $message  = "Please provide one of either --comic or --list\n\n";
    $message .= $options->getUsageMessage();
    file_put_contents('php://stderr', $message);
    exit(1);
}

$comicName  = $options->getOption('c');
if (!in_array($comicName, array_keys($scan))) {
    $message = sprintf('Comic "%s" is unsupported; please use --list to find supported comics', $comicName);
    file_put_contents('php://stderr', $message);
    exit(1);
}

$source = ComicFactory::factory($comicName);
try {
    $comic  = $source->fetch();
} catch (Exception $e) {
    file_put_contents('php://stderr', sprintf(
        'Unable to fetch comic "%s": %s',
        $comicName,
        $e->getMessage()
    ));
    exit(1);
}
if (!$comic) {
    file_put_contents('php://stderr', $source->getError());
    exit(1);
}

$template =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p><a href="%s"><img src="%s"/></a></p>
</div>
EOT;
$html = sprintf($template . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
echo $html, "\n";
