<?php

declare(strict_types=1);

namespace OCA\CrmConnector\Migration;

use Closure;
use OCA\CrmConnector\Db\CrmConnectorMapper;
use OCA\CrmConnector\Db\CrmConnectorTypes;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version001Date20211122145359 extends SimpleMigrationStep
{

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
    {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();
        $table = $schema->createTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_TOKENS_NAME);

        $table->addColumn('id', CrmConnectorTypes::BIGINT, [
            'autoincrement' => true,
            'notnull' => true,
            'length' => 8,
        ]);

        $table->addColumn('user_id', CrmConnectorTypes::BIGINT, [
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('token', CrmConnectorTypes::TEXT, [
            'notnull' => true
        ]);

        $table->addColumn('last_used_at', CrmConnectorTypes::DATETIME, [
            'notnull' => false
        ]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['id', 'user_id'], CrmConnectorTypes::CRM_CONNECTOR_TABLE_TOKENS_NAME . '_id_user_id' );

        if ($schema->hasTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_USERS_NAME)) {
            $table->addForeignKeyConstraint(
                $schema->getTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_USERS_NAME),
                ['user_id'],
                ['id'],
                [],
                'fk_user_id_' . CrmConnectorTypes::CRM_CONNECTOR_TABLE_TOKENS_NAME);
        }

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
