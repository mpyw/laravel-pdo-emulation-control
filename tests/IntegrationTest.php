<?php

namespace Mpyw\LaravelPdoEmulationControl\Tests;

use Closure;
use Illuminate\Support\Facades\DB;
use Mpyw\LaravelPdoEmulationControl\ConnectionServiceProvider;
use Orchestra\Testbench\TestCase;
use PDO;
use PHPUnit\Framework\ExpectationFailedException;

class IntegrationTest extends TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
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
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function getConnectionPropertyValue(string $property): \Closure|\PDO
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $value = (new \ReflectionProperty($db = DB::connection(), $property))->getValue($db);

        assert($value instanceof Closure || $value instanceof PDO);

        return $value;
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
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            ConnectionServiceProvider::class,
        ];
    }

    /**
     * @throws ExpectationFailedException
     *
     * @phpstan-assert false $condition
     */
    protected function assertPdoAttrTruthy(mixed $condition, string $message = ''): void {
        if (version_compare(PHP_VERSION, '8.4', '>=')) {
            $this->assertTrue($condition, $message);
        } else {
            $this->assertSame(1, $condition, $message);
        }
    }

    /**
     * @throws ExpectationFailedException
     *
     * @phpstan-assert false $condition
     */
    protected function assertPdoAttrFalsy(mixed $condition, string $message = ''): void {
        if (version_compare(PHP_VERSION, '8.4', '>=')) {
            $this->assertFalse($condition, $message);
        } else {
            $this->assertSame(0, $condition, $message);
        }
    }

    public function testEagerEmulated(): void
    {
        $this->assertPdoNotResolved();
        $this->assertReadPdoNotResolved();

        $this->assertPdoAttrFalsy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertPdoAttrFalsy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $result = DB::emulated(function ($n) {
            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            $this->assertPdoAttrTruthy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertPdoAttrTruthy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            return ++$n;
        }, 1);

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertPdoAttrFalsy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertPdoAttrFalsy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

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

            $this->assertPdoAttrTruthy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertPdoAttrTruthy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

            $this->assertPdoResolved();
            $this->assertReadPdoResolved();
            return ++$n;
        }, 1);

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertPdoAttrFalsy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertPdoAttrFalsy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

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

        $this->assertPdoAttrFalsy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertPdoAttrFalsy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(2, $result);
    }

    public function testEagerNative(): void
    {
        config(['database.connections.mysql.options' => [PDO::ATTR_EMULATE_PREPARES => true]]);

        $this->assertPdoNotResolved();
        $this->assertReadPdoNotResolved();

        $this->assertPdoAttrTruthy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertPdoAttrTruthy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $result = DB::native(function ($n) {
            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            $this->assertPdoAttrFalsy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertPdoAttrFalsy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

            $this->assertPdoResolved();
            $this->assertReadPdoResolved();

            return ++$n;
        }, 1);

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertPdoAttrTruthy(DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertPdoAttrTruthy(DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $this->assertPdoResolved();
        $this->assertReadPdoResolved();

        $this->assertSame(2, $result);
    }
}
