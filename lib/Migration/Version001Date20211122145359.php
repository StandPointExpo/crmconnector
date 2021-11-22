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
class Version001Date20211122145359 extends SimpleMigrationStep
{

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
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
    {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure;
        $table = $schema->createTable(CrmConnectorMapper::CRM_CONNECTOR_TABLE_TOKENS_NAME);

        $table->addColumn('id', \OCP\DB\Types::BIGINT, [
            'autoincrement' => true,
            'notnull' => true,
            'length' => 8,
        ]);

        $table->addColumn('user_id', \OCP\DB\Types::BIGINT, [
            'notnull' => true,
            'length' => 8
        ]);

        $table->addColumn('token', \OCP\DB\Types::TEXT, [
            'notnull' => true
        ]);

        $table->addColumn('last_used_at', \OCP\DB\Types::DATETIME, [
            'notnull' => false
        ]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['id', 'user_id'], CrmConnectorMapper::CRM_CONNECTOR_TABLE_TOKENS_NAME . '_id_user_id' );
        $table->addIndex(['token'], CrmConnectorMapper::CRM_CONNECTOR_TABLE_TOKENS_NAME . '_token' );

        return $schema;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     */
    public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void
    {
        $query = $this->connection->getQueryBuilder();
        $query->update('crm_connector_users')
            ->set('user_id', 'id');
        $query->execute();
    }
}
