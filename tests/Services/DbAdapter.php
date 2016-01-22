<?php
namespace Tests\Services;
use NickLewis\PhalconDbMock\Models\Row;
use NickLewis\PhalconDbMock\Models\Table;
use NickLewis\PhalconDbMock\Services\DbAdapter;
use NickLewis\PhalconDbMock\Services\DependencyInjection;
use Phalcon\Db\Column;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class DbAdapterTest extends PHPUnit_Framework_TestCase implements InjectionAwareInterface {
    use DependencyInjection;
    const DESCRIPTOR_HOST = 'host';
    const DESCRIPTOR_USERNAME = 'username';
    const DESCRIPTOR_PASSWORD = 'password';
    const DESCRIPTOR_DB_NAME = 'dbName';
    const DESCRIPTOR_PORT = 'port';
    const TABLE_NAME = 'alpha';

    /**
     * getDescriptor
     * @return string[]
     */
    private function getDescriptor() {
        return [
            'host' => self::DESCRIPTOR_HOST,
            'username' => self::DESCRIPTOR_USERNAME,
            'password' => self::DESCRIPTOR_PASSWORD,
            'dbname' => self::DESCRIPTOR_DB_NAME,
            'port' => self::DESCRIPTOR_PORT
        ];
    }

    /**
     * getDbAdaptor
     * @return DbAdapter
     */
    private function getDbAdaptor() {
        return new DbAdapter($this->getDescriptor());
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();
        $dbAdapter = $this->getDbAdaptor();
        $this->getDi()->set('db', $dbAdapter);
    }

    /**
     * testInsert
     * @return void
     * @throws \NickLewis\PhalconDbMock\Models\DbException
     */
    public function testInsert() {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $this->getDi()->get('db');
        $table = new Table($dbAdapter->getDatabase(), 'MockModel');
        $table->addColumn(new Column('id', [
            'type' => Column::TYPE_INTEGER,
            'primary' => TRUE,
            'autoIncrement' => TRUE
        ]));
        $table->addColumn(new Column('columnA', [
            'type' => Column::TYPE_VARCHAR,
            'primary' => FALSE
        ]));
        $table->addColumn(new Column('columnB', [
            'type' => Column::TYPE_VARCHAR,
            'primary' => FALSE
        ]));
        $dbAdapter->getDatabase()->addTable($table);

        $model = new MockModel();
        $model->setColumnA('awesome');
        $model->setColumnB('sauce');
        $model->save();

        $dbAdapter->execute('INSERT INTO MockModel VALUES (DEFAULT, "specific", \'direct\')');

        $this->assertEquals([
            new Row(['id'=>1, 'columnA'=>'awesome', 'columnB'=>'sauce']),
            new Row(['id'=>2, 'columnA'=>'specific', 'columnB'=>'direct'])
        ], $dbAdapter->getDatabase()->getTable('MockModel')->getRows());
        $this->assertEquals(1, $model->getId());
    }
}