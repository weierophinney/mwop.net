<?php
use Comic\ComicFactory;

$supported = ComicFactory::getSupported();
ksort($supported);

$comics = array();
foreach (array_keys($supported) as $alias) {
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

$errTemplate =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p class="error">%s</p>
</div>
EOT;

foreach ($comics as $comic) {
    if ($comic->hasError()) {
        $html .= sprintf($errTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getError());
        continue;
    }
    $html .= sprintf($template . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
}
echo $html;
