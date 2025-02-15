<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Validator;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ResourceInputDataPropertiesValidatorInterface
{
    public function validate(ResourceInterface $resource, array $inputData, array $validationGroups = []): void;
}
