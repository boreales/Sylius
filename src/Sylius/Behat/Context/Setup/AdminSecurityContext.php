<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AdminSecurityContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var TestUserFactoryInterface
     */
    private $testUserFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SecurityServiceInterface $securityService
     * @param TestUserFactoryInterface $testUserFactory
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        TestUserFactoryInterface $testUserFactory,
        UserRepositoryInterface $userRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->testUserFactory = $testUserFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        $user = $this->testUserFactory->createDefault();
        $this->userRepository->add($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('admin', $user);
    }
}
