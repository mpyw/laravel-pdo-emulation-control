<?php

namespace Mpyw\LaravelPdoEmulationControl\Connections;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Mpyw\LaravelPdoEmulationControl\ControlsEmulation;

class PostgresConnection extends BasePostgresConnection
{
    use ControlsEmulation;
}
