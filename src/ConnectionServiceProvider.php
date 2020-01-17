<?php

namespace Mpyw\LaravelPdoEmulationControl;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;
use Mpyw\LaravelPdoEmulationControl\Connections\MySqlConnection;
use Mpyw\LaravelPdoEmulationControl\Connections\PostgresConnection;
use Mpyw\LaravelPdoEmulationControl\Connections\SQLiteConnection;
use Mpyw\LaravelPdoEmulationControl\Connections\SqlServerConnection;

class ConnectionServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        Connection::resolverFor('mysql', $this->resolverFor(MySqlConnection::class));
        Connection::resolverFor('pgsql', $this->resolverFor(PostgresConnection::class));
        Connection::resolverFor('sqlite', $this->resolverFor(SQLiteConnection::class));
        Connection::resolverFor('sqlsrv', $this->resolverFor(SqlServerConnection::class));
    }

    /**
     * Create resolver for the connection.
     *
     * @param  string   $class
     * @return \Closure
     */
    protected function resolverFor(string $class): Closure
    {
        return static function (...$args) use ($class) {
            return new $class(...$args);
        };
    }
}
