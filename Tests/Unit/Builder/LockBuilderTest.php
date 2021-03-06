<?php

namespace Higidi\Lock\Tests\Unit;

use Higidi\Lock\Builder\LockBuilder;
use NinjaMutex\Lock;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Test case for "\Higidi\Lock\LockBuilder".
 *
 * @covers \Higidi\Lock\LockBuilder
 */
class LockBuilderTest extends UnitTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->clearLockPath();
    }

    /**
     * Unset all additional properties of test classes to help PHP
     * garbage collection. This reduces memory footprint with lots
     * of tests.
     *
     * If owerwriting tearDown() in test classes, please call
     * parent::tearDown() at the end. Unsetting of own properties
     * is not needed this way.
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->clearLockPath();
    }

    /**
     * @return void
     */
    protected function clearLockPath()
    {
        $path = $this->getLockPath();
        if (is_dir($path)) {
            GeneralUtility::rmdir($path, true);
        }
    }

    /**
     * @return string
     */
    protected function getLockPath()
    {
        return PATH_site . '/typo3temp/locks/';
    }

    /**
     * @test
     */
    public function itIsASingleton()
    {
        $sut = new LockBuilder();

        $this->assertInstanceOf(SingletonInterface::class, $sut);
    }

    /**
     * @test
     */
    public function itBuildsADirectoryLock()
    {
        $configurtion = [
            'path' => $this->getLockPath(),
        ];

        $builder = new LockBuilder();

        $lock = $builder->buildDirectoryLock($configurtion);

        $this->assertInstanceOf(Lock\DirectoryLock::class, $lock);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\InvalidConfigurationException
     * @expectedExceptionCode 1510318044
     */
    public function itThrowsAnInvalidConfigurationExceptionOnBuildADirectoryLockWithMissingPathConfiguration()
    {
        $builder = new LockBuilder();

        $builder->buildDirectoryLock([]);
    }

    /**
     * @test
     */
    public function itBuildsAFlockLock()
    {
        $configurtion = [
            'path' => $this->getLockPath(),
        ];

        $builder = new LockBuilder();

        $lock = $builder->buildFlockLock($configurtion);

        $this->assertInstanceOf(Lock\FlockLock::class, $lock);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\InvalidConfigurationException
     * @expectedExceptionCode 1510318044
     */
    public function itThrowsAnInvalidConfigurationExceptionOnBuildAFlockLockWithMissingPathConfiguration()
    {
        $builder = new LockBuilder();

        $builder->buildFlockLock([]);
    }

    /**
     * @test
     */
    public function itBuildsAMySqlLock()
    {
        $configuration = [
            'host' => 'mysql',
        ];

        $builder = new LockBuilder();

        $lock = $builder->buildMySqlLock($configuration);

        $this->assertInstanceOf(Lock\MySqlLock::class, $lock);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\InvalidConfigurationException
     * @expectedExceptionCode 1510327148
     */
    public function itThrowsAInvalidConfigurationExceptionOnBuildAMySQLLockWithMissingHostConfiguration()
    {
        $builder = new LockBuilder();

        $builder->buildMysqlLock([]);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\InvalidConfigurationException
     * @expectedExceptionCode 1510327151
     */
    public function itThrowsAInvalidConfigurationExceptionOnBuildAMySQLLockWithInvalidClassNameConfiguration()
    {
        $configuration = [
            'host' => '127.0.0.1',
            'className' => \stdClass::class,
        ];

        $builder = new LockBuilder();

        $builder->buildMysqlLock($configuration);
    }

    /**
     * @test
     */
    public function itBuildsAPhpRedisLock()
    {
        $configuration = [
            'host' => 'redis',
        ];

        $builder = new LockBuilder();

        $lock = $builder->buildPhpRedisLock($configuration);

        $this->assertInstanceOf(Lock\PhpRedisLock::class, $lock);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\InvalidConfigurationException
     * @expectedExceptionCode 1510321408
     */
    public function itThrowsAInvalidConfigurationExceptionOnBuildAPhpRedisLockWithMissingHostConfiguration()
    {
        $builder = new LockBuilder();

        $builder->buildPhpRedisLock([]);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\LockCreateException
     * @expectedExceptionCode 1510321516
     */
    public function itThrowsALockCreateExceptionOnBuildPhpRedisLockIfCanNotConnectToRedis()
    {
        $configuration = [
            'host' => '1.2.3.4',
            'port' => 1,
        ];

        $builder = new LockBuilder();

        $builder->buildPhpRedisLock($configuration);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\LockCreateException
     * @expectedExceptionCode 1510321753
     */
    public function itThrowsALockCreateExceptionOnBuildPhpRedisLockIfCanNotAuthWithRedis()
    {
        $configuration = [
            'host' => 'redis',
            'password' => 'invalidPassword',
        ];

        $builder = new LockBuilder();

        $builder->buildPhpRedisLock($configuration);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\LockCreateException
     * @expectedExceptionCode 1510321791
     */
    public function itThrowsALockCreateExceptionOnBuildPhpRedisLockIfCanNotSelectRedisDatabase()
    {
        $configuration = [
            'host' => 'redis',
            'database' => -1,
        ];

        $builder = new LockBuilder();

        $builder->buildPhpRedisLock($configuration);
    }

    /**
     * @test
     */
    public function itBuildsAPredisRedisLock()
    {
        $configuration = [
            'parameters' => 'redis',
        ];

        $builder = new LockBuilder();

        $lock = $builder->buildPredisRedisLock($configuration);

        $this->assertInstanceOf(Lock\PredisRedisLock::class, $lock);
    }

    /**
     * @test
     * @expectedException \Higidi\Lock\Builder\Exception\InvalidConfigurationException
     * @expectedExceptionCode 1510325325
     */
    public function itThrowsAInvalidConfigurationExceptionOnBuildAPredisRedisLockWithMissingParametersConfiguration()
    {
        $builder = new LockBuilder();

        $builder->buildPredisRedisLock([]);
    }
}
