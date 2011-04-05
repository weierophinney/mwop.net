<?php
namespace Blog\View;

use Zend\Tag\Cloud;

class TagCloud
{
    public function __construct($tags)
    {
        $this->cloud = function() use ($tags) {
            foreach ($tags as $key => $tag) {
                $tags[$key]['params'] = array(
                    'url' => '/blog/tag/' . $tag,
                );
            }
            $cloud = new Cloud(array('tags' => $tags));
            return array('content' => $cloud->render());
        };
    }
}
