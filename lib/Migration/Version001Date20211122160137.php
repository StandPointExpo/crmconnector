<?php

declare(strict_types=1);

namespace OCA\CrmConnector\Migration;

use Closure;
use OCA\CrmConnector\Db\CrmConnectorMapper;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version001Date20211122160137 extends SimpleMigrationStep {

    /** @var IDBConnection */
    protected $connection;

    public function __construct(IDBConnection $connection)
    {
        $this->connection = $connection;
    }
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
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();
        $table = $schema->createTable(CrmConnectorMapper::CRM_CONNECTOR_TABLE_SHARE_NAME);

        $table->addColumn('id', \OCP\DB\Types::BIGINT, [
            'autoincrement' => true,
            'notnull' => true,
            'length' => 8,
        ]);

        $table->addColumn('crm_user_id', \OCP\DB\Types::BIGINT, [
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('fileid', \OCP\DB\Types::BIGINT, [
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('crm_file_uuid', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 64
        ]);

        $table->addColumn('share_token', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 255,
        ]);

        $table->addColumn('created_at', \OCP\DB\Types::DATETIME, [
            'notnull' => false,
            'default' => null
        ]);

        $table->addColumn('updated_at', \OCP\DB\Types::DATETIME, [
            'notnull' => false,
            'default' => null
        ]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['fileid'], CrmConnectorMapper::CRM_CONNECTOR_TABLE_SHARE_NAME . '_fileid' );

        return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {

        $query = $this->connection->getQueryBuilder();
        $query->update(CrmConnectorMapper::CRM_CONNECTOR_TABLE_SHARE_NAME)
            ->set('crm_user_id', 'id');

        $query->update(CrmConnectorMapper::CRM_CONNECTOR_TABLE_SHARE_NAME)
            ->set('fileid', 'fileid');
        $query->execute();
	}
}
