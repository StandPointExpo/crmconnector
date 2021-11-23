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
class Version001Date20211122160137 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {

        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();
        $table = $schema->createTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_SHARE_NAME);

        $table->addColumn('id', CrmConnectorTypes::BIGINT, [
            'autoincrement' => true,
            'notnull' => true,
            'length' => 8,
        ]);

        $table->addColumn('user_id', CrmConnectorTypes::BIGINT, [
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('fileid', CrmConnectorTypes::BIGINT, [
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('crm_file_uuid', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 64
        ]);

        $table->addColumn('share_token', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 255,
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
        $table->addIndex(['user_id'], CrmConnectorTypes::CRM_CONNECTOR_TABLE_SHARE_NAME . 'user_id' );
        $table->addIndex(['fileid'], CrmConnectorTypes::CRM_CONNECTOR_TABLE_SHARE_NAME . '_fileid' );

        if ($schema->hasTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_USERS_NAME)) {
            $table->addForeignKeyConstraint(
                $schema->getTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_USERS_NAME),
                ['user_id'],
                ['id'],
                [],
                'fk_user_id_' . CrmConnectorTypes::CRM_CONNECTOR_TABLE_SHARE_NAME);
        }

        if ($schema->hasTable(CrmConnectorTypes::OCA_TABLE_FILECACHE_NAME)) {
            $table->addForeignKeyConstraint(
                $schema->getTable(CrmConnectorTypes::OCA_TABLE_FILECACHE_NAME),
                ['fileid'],
                ['fileid'],
                [],
                'fk_fileid_' . CrmConnectorTypes::CRM_CONNECTOR_TABLE_SHARE_NAME);
        }

        return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {

	}
}
