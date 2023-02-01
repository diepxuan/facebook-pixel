<?php

declare(strict_types=1);

namespace Meta\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Meta\Catalog\Setup\MetaCatalogAttributes;

class AddProductAttributes implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var MetaCatalogAttributes
     */
    private $metaCatalogAttributes;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param MetaCatalogAttributes $metaCatalogAttributes
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        MetaCatalogAttributes $metaCatalogAttributes
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->metaCatalogAttributes = $metaCatalogAttributes;
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return  [];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $catalogAttributes = $this->metaCatalogAttributes->execute();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);

        foreach ($catalogAttributes as $attributeCode => $attributeData) {

            if (!$eavSetup->getAttributeId(Product::ENTITY, $attributeCode)) {
                $eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attributeData);
            }
            // Assign attributes to default attribute set
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $attributeData['group'],
                $attributeCode
            );
        }
    }

    /**
     * @return void
     */
    public function revert(): void
    {
        $catalogAttributes = $this->metaCatalogAttributes->execute();
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach ($catalogAttributes as $attributeCode => $attributeData) {
            $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
