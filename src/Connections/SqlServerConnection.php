<?php

namespace Mpyw\LaravelPdoEmulationControl\Connections;

use Illuminate\Database\SqlServerConnection as BaseSqlServerConnection;
use Mpyw\LaravelPdoEmulationControl\ControlsEmulation;

class SqlServerConnection extends BaseSqlServerConnection
{
    use ControlsEmulation;
}
