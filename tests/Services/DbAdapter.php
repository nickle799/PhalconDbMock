<?php
namespace Tests\Services;
use NickLewis\PhalconDbMock\Models\Row;
use NickLewis\PhalconDbMock\Models\Table;
use NickLewis\PhalconDbMock\Services\DbAdapter;
use NickLewis\PhalconDbMock\Services\DependencyInjection;
use Phalcon\Db\Column;
use Phalcon\Di\FactoryDefault;
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
        if($this->getDi()->has('db')) {
            $this->getDi()->remove('db');
        }
        $this->getDi()->setShared('db', $dbAdapter);
    }

    /**
     * testOuterJoin
     * @return void
     * @throws \NickLewis\PhalconDbMock\Models\DbException
     */
    public function testOuterJoin() {
        //Arrange
        $this->createTable();
        $this->createJoinTable();

        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $this->getDi()->get('db');

        $dbAdapter->execute('INSERT INTO MockModel (columnA, columnB) VALUES (?, ?),(?, ?),(?, ?)', ['alpha', 'bravo', 'charlie', 'delta', 'echo', 'foxtrot']);
        $dbAdapter->execute('INSERT INTO MockModelJoin (mockModelJoinId, columnB) VALUES (?, ?)', ['5', 'golf']);

        //Act
        $result = $dbAdapter->query('SELECT a.columnB aB, b.columnB AS bB FROM MockModel a LEFT OUTER JOIN MockModelJoin AS b ON (a.id=b.mockModelJoinId) WHERE a.id=2');

        //Assert
        $this->assertEquals([['aB'=>'delta', 'bB'=>null]], $result->fetchAll());
    }

    public function testJoin_noWhere() {
        //Arrange
        $this->createTable();
        $this->createJoinTable();

        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $this->getDi()->get('db');

        $dbAdapter->execute('INSERT INTO MockModel (columnA, columnB) VALUES (?, ?),(?, ?),(?, ?)', ['alpha', 'bravo', 'charlie', 'delta', 'echo', 'foxtrot']);
        $dbAdapter->execute('INSERT INTO MockModelJoin (mockModelJoinId, columnB) VALUES (?, ?)', ['2', 'golf']);

        //Act
        $result = $dbAdapter->query('SELECT a.columnB aB, b.columnB AS bB FROM MockModel a JOIN MockModelJoin AS b ON (a.id=b.mockModelJoinId)');

        //Assert
        $this->assertEquals([['aB'=>'delta', 'bB'=>'golf']], $result->fetchAll());
    }

    /**
     * testFindFirst
     * @return void
     */
    public function testFindFirst() {
        //Arrange
        $this->createTable();

        $model = new MockModel();
        $model->setColumnA('awesome');
        $model->setColumnB('sauce');
        $model->save();

        $model2 = new MockModel();
        $model2->setColumnA('awesomer');
        $model2->setColumnB('saucer');
        $model2->save();

        //Actual
        /** @var MockModel|false $actualModel */
        $actualModel = MockModel::findFirst(1);
        /** @var MockModel|false $actualerModel */
        $actualerModel = MockModel::findFirst(2);

        //Assert
        $this->assertTrue($actualModel instanceof MockModel);
        $this->assertEquals($model->getId(), $actualModel->getId());
        $this->assertTrue($actualerModel instanceof MockModel);
        $this->assertEquals($model2->getId(), $actualerModel->getId());
    }

    /**
     * createTable
     * @return void
     * @throws \NickLewis\PhalconDbMock\Models\DbException
     */
    private function createTable() {
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
    }

    /**
     * createJoinTable
     * @return void
     * @throws \NickLewis\PhalconDbMock\Models\DbException
     */
    private function createJoinTable() {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $this->getDi()->get('db');
        $table = new Table($dbAdapter->getDatabase(), 'MockModelJoin');
        $table->addColumn(new Column('id', [
            'type' => Column::TYPE_INTEGER,
            'primary' => TRUE,
            'autoIncrement' => TRUE
        ]));
        $table->addColumn(new Column('mockModelJoinId', [
            'type' => Column::TYPE_INTEGER,
            'primary' => FALSE
        ]));
        $table->addColumn(new Column('columnB', [
            'type' => Column::TYPE_VARCHAR,
            'primary' => FALSE
        ]));
        $dbAdapter->getDatabase()->addTable($table);
    }

    /**
     * testInsert
     * @return void
     * @throws \NickLewis\PhalconDbMock\Models\DbException
     */
    public function testInsert() {
        $this->createTable();
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $this->getDi()->get('db');

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