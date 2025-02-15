# UPGRADE FROM `v1.12.X` TO `v1.13.0`

1. Starting with Sylius 1.13, the `SyliusPriceHistoryPlugin` is included.
   If you are currently using the plugin in your project, we recommend following the upgrade guide located [here](UPGRADE-FROM-1.12-WITH-PRICE-HISTORY-PLUGIN-TO-1.13.md).

1. The `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion` command and its handler
   `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveInactiveCatalogPromotionHandler` have been deprecated.
   Use `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion` command instead.

1. Passing `Symfony\Component\Messenger\MessageBusInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second and third argument is deprecated.

1. Not passing `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second argument is deprecated.

1. Not passing `Doctrine\Persistence\ObjectManager` to `Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater`
   as a fifth argument is deprecated.

1. To allow to autoconfigure order processors and cart context and define a priority for them in `1.13` we have introduced
   `Sylius\Bundle\OrderBundle\Attribute\AsCartContext` and `Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor` attributes. By default, Sylius still configures them using interfaces, but this way you cannot define a priority.
   If you want to define a priority, you need to set the following configuration in your `_sylius.yaml` file:
   ```yaml
   sylius_core:
       autoconfigure_with_attributes: true
   ```
   and use one of the new attributes accordingly to a type of your class, e.g.:
   ```php
    <?php

    declare(strict_types=1);

    namespace App\OrderProcessor;

    use Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor;
    use Sylius\Component\Order\Model\OrderInterface;
    use Sylius\Component\Order\Processor\OrderProcessorInterface;

    #[AsOrderProcessor(priority: 10)] //priority is optional
    //#[AsOrderProcessor] can be used as well
    final class OrderProcessorWithAttributeStub implements OrderProcessorInterface
    {
        public function process(OrderInterface $order): void
        {
        }
    }
   ```
1. Not passing `Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface` 
   to `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator`
   as a first argument is deprecated.

1. Not passing an instance of `Symfony\Component\PropertyAccess\PropertyAccessorInterface`
   to `Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator`
   as the second argument is deprecated.

1. Class `\Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculator` has been deprecated. Order items subtotal calculation
   is now available on the Order model `\Sylius\Component\Core\Model\Order::getItemsSubtotal`.

1. The way of getting variants prices based on options has been changed,
   as such the following services were deprecated, please use their new counterpart.
   * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface`
   * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProvider` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsPricesMapProvider`
   * instead of `Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper` use `Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsMapHelper`
   * instead of `Sylius\Bundle\CoreBundle\Twig\ProductVariantsPricesExtension` use `Sylius\Bundle\CoreBundle\Twig\ProductVariantsMapExtension`

   Subsequently, the `sylius_product_variant_prices` twig function is deprecated, use `sylius_product_variants_map` instead.

   To add more data per variant create a service implementing the `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface` and tag it with `sylius.product_variant_data_map_provider`.
