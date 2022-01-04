<?php // phpcs:disable Squiz.PHP.NonExecutableCode.Unreachable


declare(strict_types=1);

namespace Mwop\Blog;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Mni\FrontYAML\Parser;
use RuntimeException;

use function explode;
use function file_get_contents;
use function is_array;
use function is_numeric;
use function sprintf;
use function trim;

trait CreateBlogPostFromDataArrayTrait
{
    /** @var Parser */
    private $parser;

    /**
     * Delimiter between post summary and extended body
     *
     * @var string
     */
    private $postDelimiter = '<!--- EXTENDED -->';

    private function getParser(): Parser
    {
        if (! $this->parser) {
            $this->parser = new Parser();
        }

        return $this->parser;
    }

    private function createBlogPostFromDataArray(array $post): BlogPost
    {
        $path     = $post['path'] ?? throw new RuntimeException(sprintf(
            'Blog data provided does not include a "path" element; cannot create %s instance',
            BlogPost::class
        ));
        $parser   = $this->getParser();
        $document = $parser->parse(file_get_contents($path));
        $post     = $document->getYAML();
        $parts    = explode($this->postDelimiter, $document->getContent(), 2);
        $created  = $this->createDateTimeFromString($post['created']);
        $updated  = $post['updated'] && $post['updated'] !== $post['created']
            ? $this->createDateTimeFromString($post['updated'])
            : $created;
        $tags     = is_array($post['tags'])
            ? $post['tags']
            : explode('|', trim((string) $post['tags'], '|'));

        return new BlogPost(
            id: $post['id'],
            title: $post['title'],
            author: $post['author'],
            created: $created,
            updated: $updated,
            tags: $tags,
            body: $parts[0],
            extended: $parts[1] ?? '',
            isDraft: (bool) $post['draft'],
            isPublic: (bool) $post['public'],
        );
    }

    private function createDateTimeFromString(string $dateString): DateTimeInterface
    {
        return is_numeric($dateString)
            ? new DateTimeImmutable('@' . $dateString, new DateTimeZone('America/Chicago'))
            : new DateTimeImmutable($dateString);
    }
}
