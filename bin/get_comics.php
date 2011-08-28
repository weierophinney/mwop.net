<?php
require_once __DIR__ . '/../library/zf2/Zend/Loader/ClassMapAutoloader.php';
$classmap = new Zend\Loader\ClassMapAutoloader(array(
    __DIR__ . '/../library/.classmap.php',
));
$classmap->register();

use mwop\Comic\ComicFactory,
    mwop\Comic\ComicSource;

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

$comics = array();
foreach ($scan as $alias => $name) {
    $source = ComicFactory::factory($alias);
    try {
        $comic  = $source->fetch();
    } catch (\Exception $e) {
        file_put_contents('php://stderr', sprintf(
            'Unable to fetch comic "%s": %s',
            $alias,
            $e->getMessage()
        ));
        continue;
    }
    if (!$comic) {
        file_put_contents('php://stderr', $source->getError());
        continue;
    }
    $comics[] = $comic;
}

$html     = '';
$template =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p><a href="%s"><img src="%s"/></a></p>
</div>
EOT;
foreach ($comics as $comic) {
    $html .= sprintf($template . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
}
echo $html;
