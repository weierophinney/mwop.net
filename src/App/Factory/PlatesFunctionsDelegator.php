<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use DateTimeInterface;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Template;
use Mwop\Blog\BlogPost;
use Psr\Container\ContainerInterface;

use function array_map;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function strstr;

class PlatesFunctionsDelegator implements ExtensionInterface
{
    public Template $template;

    /**
     * @inheritDoc
     */
    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $factory
    ): Engine {
        /** @var Engine $engine */
        $engine = $factory();
        $engine->loadExtension($this);

        return $engine;
    }

    public function register(Engine $engine): void
    {
        $engine->registerFunction('ampifyContent', [$this, 'ampifyContent']);
        $engine->registerFunction('formatDate', [$this, 'formatDate']);
        $engine->registerFunction('formatDateRfc', [$this, 'formatDateRfc']);
        $engine->registerFunction('postUrl', [$this, 'postUrl']);
        $engine->registerFunction('processTags', [$this, 'processTags']);
    }

    public function ampifyContent(string $markup): string
    {
        return preg_replace_callback('#(<img)([^>]+>)#', function (array $matches): string {
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

    public function formatDate(DateTimeInterface $date, string $format = 'j F Y'): string
    {
        return $date->format($format);
    }

    public function formatDateRfc(DateTimeInterface $date): string
    {
        return $this->formatDate($date, 'c');
    }

    public function postUrl(BlogPost $post): string
    {
        return $this->template->url('blog.post', ['id' => $post->id]);
    }

    public function processTags(array $tags): array
    {
        return array_map(
            fn (string $tag): object => (object) [
                'name' => $this->template->e($tag),
                'link' => $this->template->url('blog.tag', ['tag' => $tag]),
                'atom' => $this->template->url('blog.tag.feed', ['tag' => $tag, 'type' => 'atom']),
                'rss'  => $this->template->url('blog.tag.feed', ['tag' => $tag, 'type' => 'rss']),
            ],
            $tags
        );
    }
}
