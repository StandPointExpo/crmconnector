<?php

declare(strict_types=1);

namespace OCA\CrmConnector\Migration;

use Closure;
use OCA\CrmConnector\Db\CrmFileMapper;
use OCA\CrmConnector\Db\CrmConnectorTypes;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version001Date20211122144646 extends SimpleMigrationStep {


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
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {

        $schema = $schemaClosure();

        $table = $schema->createTable(CrmConnectorTypes::CRM_CONNECTOR_TABLE_USERS_NAME);

        $table->addColumn('id' , CrmConnectorTypes::BIGINT, [
            'autoincrement' => true,
            'notnull'       => true,
            'length' => 8
        ]);

        $table->addColumn('name', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 255,
        ]);

        $table->addColumn('email', CrmConnectorTypes::STRING, [
            'notnull' => true,
            'length' => 255
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

        $table->addUniqueIndex(['email'], 'email');

        return $schema;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     */
    public function postSchemaChange(IOutput $output, \Closure $schemaClosure, array $options)
    {
    }
}


