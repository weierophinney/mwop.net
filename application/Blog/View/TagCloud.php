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
                    'url' => '/blog/tag/' . $tag['title'],
                );
            }
            $cloud = new Cloud(array(
                'tags' => $tags,
                'tagDecorator' => array(
                    'decorator' => 'html_tag',
                    'options'   => array(
                        'fontSizeUnit' => '%',
                        'minFontSize' => 80,
                        'maxFontSize' => 300,
                    ),
                ),
            ));
            return array('content' => $cloud->render());
        };
    }
}
