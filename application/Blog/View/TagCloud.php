<?php
namespace Blog\View;

use Zend\Tag\Cloud;

class TagCloud
{
    public function __construct($tags, $presentation)
    {
        $this->cloud = function() use ($tags, $presentation) {
            foreach ($tags as $key => $tag) {
                $tags[$key]['params'] = array(
                    'url' => $presentation->helper('url')->generate(array('tag' => $tag['title']), array('name' => 'blog-tag')),
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
