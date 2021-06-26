<?php

namespace Mpyw\LaravelPdoEmulationControl\Tests;

use Closure;
use Illuminate\Support\Facades\DB;
use Mpyw\LaravelPdoEmulationControl\ConnectionServiceProvider;
use Orchestra\Testbench\TestCase;
use PDO;

class IntegrationTest extends TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $host = \gethostbyname('mysql') !== 'mysql' // Is "mysql" valid hostname?
            ? 'mysql' // Local
            : '127.0.0.1'; // CI

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'read' => [
                'host' => $host,
            ],
            'write' => [],
            'host' => $host,
            'username' => 'testing',
            'password' => 'testing',
            'database' => 'testing',
        ]);
    }

    /**
     * @param  string        $property
     * @return \Closure|\PDO
     */
    protected function getConnectionPropertyValue(string $property)
    {
        $db = DB::connection();
        $rp = new \ReflectionProperty($db, $property);
        $rp->setAccessible(true);
        return $rp->getValue($db);
    }

    protected function assertPdoResolved(): void
    {
        $this->assertInstanceOf(PDO::class, $this->getConnectionPropertyValue('pdo'));
    }

    protected function assertPdoNotResolved(): void
    {
        $this->assertInstanceOf(Closure::class, $this->getConnectionPropertyValue('readPdo'));
    }

    protected function assertReadPdoResolved(): void
    {
        $this->assertInstanceOf(PDO::class, $this->getConnectionPropertyValue('pdo'));
    }

    protected function assertReadPdoNotResolved(): void
    {
        $this->assertInstanceOf(Closure::class, $this->getConnectionPropertyValue('readPdo'));
    }

    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ConnectionServiceProvider::class,
        ];
    }

    public function testEagerEmulated(): void
    {
        $this->assertPdoNotResolved();
        $this->assertReadPdoNotResolved();

        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $result = DB::emulated(function ($n) {
            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            return ++$n;
        }, 1);

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(2, $result);
    }

    public function testLazyEmulated(): void
    {
        $this->assertPdoNotResolved();
        $this->assertReadPdoNotResolved();

        $result = DB::emulated(function ($n) {
            $this->assertPdoNotResolved();
            $this->assertReadPdoNotResolved();

            $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

            $this->assertPdoResolved();
            $this->assertReadPdoResolved();
            return ++$n;
        }, 1);

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(2, $result);
    }

    public function testCancelEmulated(): void
    {
        $this->assertPdoNotResolved();
        $this->assertReadPdoNotResolved();

        $result = DB::emulated(function ($n) {
            $this->assertPdoNotResolved();
            $this->assertReadPdoNotResolved();

            return ++$n;
        }, 1);

        $this->assertPdoNotResolved();
        $this->assertReadPdoNotResolved();

        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(2, $result);
    }

    public function testEagerNative(): void
    {
        config(['database.connections.mysql.options' => [PDO::ATTR_EMULATE_PREPARES => true]]);

        $this->assertPdoNotResolved();
        $this->assertReadPdoNotResolved();

        $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $result = DB::native(function ($n) {
            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            return ++$n;
        }, 1);

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(2, $result);
    }
}
