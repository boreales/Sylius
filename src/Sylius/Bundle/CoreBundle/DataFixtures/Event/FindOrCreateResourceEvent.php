<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateResourceEvent extends AbstractResourceEvent
{
    public function getResource(): Proxy
    {
        Assert::notNull($this->resource, 'No Resource has been found or created.');

        return $this->resource;
    }
}
