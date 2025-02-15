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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeTranslationInterface;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductAssociationContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FactoryInterface $productAssociationTypeFactory,
        private FactoryInterface $productAssociationTypeTranslationFactory,
        private FactoryInterface $productAssociationFactory,
        private ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository,
        private RepositoryInterface $productAssociationRepository,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @Given the store has (also) a product association type :name
     * @Given the store has (also) a product association type :name with a code :code
     */
    public function theStoreHasAProductAssociationType($name, $code = null)
    {
        $this->createProductAssociationType($name, $code);
    }

    /**
     * @Given /^the store has(?:| also) a product association type named "([^"]+)" in ("[^"]+" locale) and "([^"]+)" in ("[^"]+" locale)$/
     */
    public function itHasVariantNamedInAndIn($firstName, $firstLocale, $secondName, $secondLocale)
    {
        $productAssociationType = $this->createProductAssociationType($firstName);

        $names = [$firstName => $firstLocale, $secondName => $secondLocale];
        foreach ($names as $name => $locale) {
            $this->addProductAssociationTypeTranslation($productAssociationType, $name, $locale);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given the store has :firstName and :secondName product association types
     */
    public function theStoreHasProductAssociationTypes(...$names)
    {
        foreach ($names as $name) {
            $this->createProductAssociationType($name);
        }
    }

    /**
     * @Given the store has :firstName product association type
     */
    public function theStoreHasProductAssociationType($name)
    {
        $this->createProductAssociationType($name);
    }

    /**
     * @Given /^the (product "[^"]+") has(?:| also) an (association "[^"]+") with (product "[^"]+")$/
     */
    public function theProductHasAnAssociationWithProduct(
        ProductInterface $product,
        ProductAssociationTypeInterface $productAssociationType,
        ProductInterface $associatedProduct,
    ) {
        $this->createProductAssociation($product, $productAssociationType, [$associatedProduct]);
    }

    /**
     * @Given /^the (product "[^"]+") has(?:| also) an (association "[^"]+") with (products "[^"]+" and "[^"]+")$/
     */
    public function theProductHasAnAssociationWithProducts(
        ProductInterface $product,
        ProductAssociationTypeInterface $productAssociationType,
        array $associatedProducts,
    ) {
        $this->createProductAssociation($product, $productAssociationType, $associatedProducts);
    }

    /**
     * @param string $name
     * @param string|null $code
     *
     * @return ProductAssociationTypeInterface
     */
    private function createProductAssociationType($name, $code = null)
    {
        if (null === $code) {
            $code = $this->generateCodeFromName($name);
        }

        /** @var ProductAssociationTypeInterface $productAssociationType */
        $productAssociationType = $this->productAssociationTypeFactory->createNew();
        $productAssociationType->setCode($code);
        $productAssociationType->setName($name);

        $this->productAssociationTypeRepository->add($productAssociationType);
        $this->sharedStorage->set('product_association_type', $productAssociationType);

        return $productAssociationType;
    }

    private function createProductAssociation(
        ProductInterface $product,
        ProductAssociationTypeInterface $productAssociationType,
        array $associatedProducts,
    ) {
        /** @var ProductAssociationInterface $productAssociation */
        $productAssociation = $this->productAssociationFactory->createNew();
        $productAssociation->setType($productAssociationType);

        foreach ($associatedProducts as $associatedProduct) {
            $productAssociation->addAssociatedProduct($associatedProduct);
        }

        $product->addAssociation($productAssociation);

        $this->productAssociationRepository->add($productAssociation);
    }

    private function addProductAssociationTypeTranslation(
        ProductAssociationTypeInterface $productAssociationType,
        string $name,
        string $locale,
    ) {
        /** @var ProductAssociationTypeTranslationInterface $translation */
        $translation = $this->productAssociationTypeTranslationFactory->createNew();
        $translation->setLocale($locale);
        $translation->setName($name);

        $productAssociationType->addTranslation($translation);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function generateCodeFromName($name)
    {
        return str_replace([' ', '-'], '_', strtolower($name));
    }
}
