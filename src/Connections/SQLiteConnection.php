<?php

namespace Mpyw\LaravelPdoEmulationControl\Connections;

use Illuminate\Database\SQLiteConnection as BaseSQLiteConnection;
use Mpyw\LaravelPdoEmulationControl\ControlsEmulation;

class SQLiteConnection extends BaseSQLiteConnection
{
    use ControlsEmulation;
}
