<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

class JsonMapperFactory
{
    public function __invoke(): TreeMapper
    {
        return (new MapperBuilder())->mapper();
    }
}
