<?php

declare(strict_types=1);

namespace OCA\CrmConnector\Migration;

use Closure;
use OCA\CrmConnector\Db\CrmConnectorMapper;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version001Date20211122144646 extends SimpleMigrationStep {


	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {

        $schema = $schemaClosure();

        $table = $schema->createTable(CrmConnectorMapper::CRM_CONNECTOR_TABLE_USERS_NAME);

        $table->addColumn('id' , \OCP\DB\Types::BIGINT, [
            'autoincrement' => true,
            'notnull'       => true,
            'length' => 8
        ]);

        $table->addColumn('name', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 255,
        ]);

        $table->addColumn('email', \OCP\DB\Types::STRING, [
            'notnull' => true,
            'length' => 255
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

        $table->addUniqueIndex(['email'], 'email');
        return $schema;
    }


}
