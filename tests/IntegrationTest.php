<?php

namespace Mpyw\LaravelPdoEmulationControl\Tests;

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
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'read' => [
                'host' => 'mysql',
            ],
            'write' => [],
            'host' => 'mysql',
            'username' => 'user',
            'password' => 'password',
            'database' => 'testing',
        ]);
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
        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $result = DB::emulated(function ($n) {
            $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            return ++$n;
        }, 1);

        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(2, $result);
    }

    public function testLazyEmulated(): void
    {
        $result = DB::emulated(function ($n) {
            $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            return ++$n;
        }, 1);

        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(2, $result);
    }

    public function testCancelEmulated(): void
    {
        $result = DB::emulated(function ($n) {
            return ++$n;
        }, 1);

        $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(2, $result);
    }

    public function testEagerNative(): void
    {
        config(['database.connections.mysql.options' => [PDO::ATTR_EMULATE_PREPARES => true]]);

        $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));

        $result = DB::native(function ($n) {
            $this->assertSame(0, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            $this->assertSame(0, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
            return ++$n;
        }, 1);

        $this->assertSame(1, DB::getReadPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(1, DB::getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        $this->assertSame(2, $result);
    }
}
