<?php

namespace Mpyw\LaravelPdoEmulationControl;

/**
 * Trait ControlsEmulation
 *
 * @mixin \Illuminate\Database\Connection
 */
trait ControlsEmulation
{
    /**
     * Temporarily enable PDO prepared statement emulation.
     *
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return mixed
     */
    public function emulated(callable $callback, ...$args)
    {
        return (new EmulationController($this->readPdo, $this->pdo))->emulated($callback, ...$args);
    }

    /**
     * Temporarily disable PDO prepared statement emulation.
     *
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return mixed
     */
    public function native(callable $callback, ...$args)
    {
        return (new EmulationController($this->readPdo, $this->pdo))->native($callback, ...$args);
    }
}
