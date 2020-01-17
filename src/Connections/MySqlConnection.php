<?php

namespace Mpyw\LaravelPdoEmulationControl\Connections;

use Illuminate\Database\MySqlConnection as BaseMySqlConnection;
use Mpyw\LaravelPdoEmulationControl\ControlsEmulation;

class MySqlConnection extends BaseMySqlConnection
{
    use ControlsEmulation;
}
