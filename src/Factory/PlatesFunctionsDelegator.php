<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use DateTimeInterface;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Mwop\Blog\BlogPost;
use Psr\Container\ContainerInterface;

use function is_numeric;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function strstr;

class PlatesFunctionsDelegator implements ExtensionInterface
{
    public $template;

    private $engine;

    public function __invoke(ContainerInterface $container, $name, callable $factory)
    {
        $engine = $factory();
        $engine->loadExtension($this);

        return $engine;
    }

    public function register(Engine $engine) : void
    {
        $engine->registerFunction('ampifyContent', [$this, 'ampifyContent']);
        $engine->registerFunction('formatDate', [$this, 'formatDate']);
        $engine->registerFunction('formatDateRfc', [$this, 'formatDateRfc']);
        $engine->registerFunction('postUrl', [$this, 'postUrl']);
        $engine->registerFunction('processTags', [$this, 'processTags']);
    }

    public function ampifyContent(string $markup) : string
    {
        return preg_replace_callback('#(<img)([^>]+>)#', function (array $matches) {
            $attributes = preg_replace('#\s*/>$#', '>', $matches[2]);
            if (false === strstr($attributes, 'width=')) {
                $attributes = ' width="400" ' . $attributes;
            }
            if (false === strstr($attributes, 'height=')) {
                $attributes = ' height="600" ' . $attributes;
            }
            return sprintf('<amp-img layout="responsive"%s</amp-img>', $attributes);
        }, $markup);
    }

    public function formatDate(DateTimeInterface $date, string $format = 'j F Y') : string
    {
        return $date->format($format);
    }

    public function formatDateRfc(DateTimeInterface $date) : string
    {
        return $this->formatDate($date, 'c');
    }

    public function postUrl(BlogPost $post) : string
    {
        return $this->template->url('blog.post', ['id' => $post->id]);
    }

    public function processTags(array $tags) : array
    {
        return array_map(function ($tag) {
            return (object) [
                'name' => $this->template->e($tag),
                'link' => $this->template->url('blog.tag', ['tag' => $tag]),
                'atom' => $this->template->url('blog.tag.feed', ['tag' => $tag, 'type' => 'atom']),
                'rss'  => $this->template->url('blog.tag.feed', ['tag' => $tag, 'type' => 'rss']),
            ];
        }, $tags);
    }
}
