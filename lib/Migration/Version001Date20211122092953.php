<?php

declare(strict_types=1);

namespace OCA\CrmConnector\Migration;

use Closure;
use OCA\CrmConnector\Db\CrmConnectorMapper;
use OCA\CrmConnector\Db\CrmConnectorTypes;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version001Date20211122092953 extends SimpleMigrationStep
{

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     */
    public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void
    {
        //
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
    {

        $schema = $schemaClosure();

        $table = $schema->createTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_FILES_NAME);

        $table->addColumn('id', CrmConnectorTypes::BIGINT, [
            'autoincrement' => true,
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('user_id', CrmConnectorTypes::BIGINT, [
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('uuid', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 64
        ]);

        $table->addColumn('publication', CrmConnectorTypes::BOOLEAN, [
            'notnull' => false,
            'default' => true
        ]);

        $table->addColumn('file_original_name', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 255,
        ]);

        $table->addColumn('file_type', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 255,
        ]);

        $table->addColumn('file_source', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 255,
        ]);

        $table->addColumn('file_share', CrmConnectorTypes::STRING, [
            'notnull' => false,
            'length' => 255,
        ]);

        $table->addColumn('extension', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 255,
        ]);

        $table->addColumn('deleted_at', CrmConnectorTypes::DATETIME, [
            'notnull' => false,
            'default' => null
        ]);

        $table->addColumn('created_at', CrmConnectorTypes::DATETIME, [
            'notnull' => false,
            'default' => null
        ]);

        $table->addColumn('updated_at', CrmConnectorTypes::DATETIME, [
            'notnull' => false,
            'default' => null
        ]);

        $table->setPrimaryKey(['id']);

        return $schema;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     */
    public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void
    {
    }
}
