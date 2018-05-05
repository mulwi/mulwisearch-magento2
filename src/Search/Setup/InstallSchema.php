<?php

namespace Mulwi\Search\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mulwi\Search\Api\Data\QueueInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$context->getVersion() || version_compare($context->getVersion(), '1.0.0') < 0) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable(QueueInterface::TABLE_NAME)
            )->addColumn(
                QueueInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Queue ID'
            )->addColumn(
                QueueInterface::INDEX,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Object'
            )->addColumn(
                QueueInterface::ACTION,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Action'
            )->addColumn(
                QueueInterface::VALUE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Value'
            )->addColumn(
                QueueInterface::RETRIES,
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => 0],
                'Retries'
            )->addColumn(
                QueueInterface::IS_PROCESSED,
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false, 'default' => 0],
                'Retries'
            )->addColumn(
                QueueInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                QueueInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            );
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
