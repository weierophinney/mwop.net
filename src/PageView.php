<?php
namespace Mwop;

class PageView
{
    use UriTrait;

    public function __construct(array $vars = [])
    {
        foreach ($vars as $var => $value) {
            switch ($var) {
                case 'router':
                case 'setRouter':
                case 'uri':
                    continue;
                default:
                    $this->{$var} = $value;
            }
        }
    }
}
