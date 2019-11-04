<?php

namespace Diggecard\Giftcard\Setup;

use Diggecard\Giftcard\Service\GiftcardSampleData;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    /** @var GiftcardSampleData */
    protected $giftcardSampleData;

    public function __construct(
        GiftcardSampleData $giftcardSampleData
    )
    {
        $this->giftcardSampleData = $giftcardSampleData;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $this->giftcardSampleData->addAssociateAttributes($setup, $context);
        }
    }
}

