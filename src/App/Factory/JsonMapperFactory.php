<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\TreeMapper;

class JsonMapperFactory
{
    public function __invoke(): TreeMapper
    {
        return (new MapperBuilder)->mapper();
    }
}
