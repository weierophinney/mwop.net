<?php
namespace Mwop;

use RuntimeException;
use Zend\Expressive\Router\RouterInterface;

/**
 * Trait to compose in views that require URI generation.
 */
trait UriTrait
{
    private $router;

    /**
     * Inject the router to use for URI generation.
     *
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Higher-order section for URI generation in templates.
     *
     * The function takes the text provided, and passes it to `json_decode()`;
     * if an array containing the key `name` is returned, it passes the data
     * along to the composed router's `generateUri()` method, otherwise
     * returning the original text verbatim.
     *
     * If an `options` key is also present in the data, and an array, that
     * information is passed to the `generateUri()` method's second argument.
     *
     * For consumers:
     *
     * <code>
     * <a href="{{#uri}}{"name": "route.name", "options": {"id": {{id}}}}{{/uri}}">
     *     Link text
     * </a>
     * </code>
     *
     * @return callable
     */
    public function uri()
    {
        if (! $this->router instanceof RouterInterface) {
            throw new RuntimeException(sprintf(
                '%s requires injection of a router in order to generate URIs',
                get_class($this)
            ));
        }

        return function ($text, $renderer) {
            error_log("In URI callable\n");
            // Decode the text .
            $data = json_decode($text, true);

            // Now, can we can use it?
            if (! is_array($data)
                || ! isset($data['name'])
            ) {
                error_log(sprintf("    Invalid data; returning text verbatim: %s\n", $text));
                return $text;
            }

            $route   = $data['name'];
            $options = $this->parseOptions($data, $renderer);
            error_log(sprintf("    Generating URI for route '%s' using options: %s\n", $route, var_export($options, 1)));
            $uri     = $this->router->generateUri($route, $options);

            error_log(sprintf("    Generated URI: %s\n", $uri));
            // Bug in URI generation; optional segments are not being stripped
            // in FastRoute.
            return str_replace('[/]', '', $uri);
        };
    }

    /**
     * Parse options
     *
     * It can be useful to use view data when providing options (e.g., to
     * inject an identifier into a generated URI); this method takes the
     * options, checking each value for templated items, and, when found,
     * passing them through the renderer.
     *
     * @param array $data
     * @param \Phly\Mustache\Renderer\RendererInterface $renderer
     * @return array
     */
    private function parseOptions(array $data, $renderer)
    {
        if (! isset($data['options']) || ! is_array($data['options'])) {
            return [];
        }

        $options = $data['options'];
        foreach ($options as $key => $value) {
            if (preg_match('/\{\{[^{]+\}\}/', $value)) {
                $options[$key] = $renderer($value);
            }
        }

        return $options;
    }
}
