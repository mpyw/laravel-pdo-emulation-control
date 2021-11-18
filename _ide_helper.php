<?php

namespace Illuminate\Database
{
    if (false) {
        interface ConnectionInterface
        {
            /**
             * Temporarily enable PDO prepared statement emulation.
             *
             * @param  callable $callback
             * @param  mixed    ...$args
             * @return mixed
             * @see \Mpyw\LaravelPdoEmulationControl\ControlsEmulation
             */
            public function emulated(callable $callback, ...$args);

            /**
             * Temporarily disable PDO prepared statement emulation.
             *
             * @param  callable $callback
             * @param  mixed    ...$args
             * @return mixed
             * @see \Mpyw\LaravelPdoEmulationControl\ControlsEmulation
             */
            public function native(callable $callback, ...$args);
        }

        class Connection implements ConnectionInterface
        {
            /**
             * Temporarily enable PDO prepared statement emulation.
             *
             * @param  callable $callback
             * @param  mixed    ...$args
             * @return mixed
             * @see \Mpyw\LaravelPdoEmulationControl\ControlsEmulation
             */
            public function emulated(callable $callback, ...$args)
            {
            }

            /**
             * Temporarily disable PDO prepared statement emulation.
             *
             * @param  callable $callback
             * @param  mixed    ...$args
             * @return mixed
             * @see \Mpyw\LaravelPdoEmulationControl\ControlsEmulation
             */
            public function native(callable $callback, ...$args)
            {
            }
        }
    }
}

namespace Illuminate\Support\Facades
{
    if (false) {
        class DB extends Facade
        {
            /**
             * Temporarily enable PDO prepared statement emulation.
             *
             * @param  callable $callback
             * @param  mixed    ...$args
             * @return mixed
             * @see \Mpyw\LaravelPdoEmulationControl\ControlsEmulation
             */
            public static function emulated(callable $callback, ...$args)
            {
            }

            /**
             * Temporarily disable PDO prepared statement emulation.
             *
             * @param  callable $callback
             * @param  mixed    ...$args
             * @return mixed
             * @see \Mpyw\LaravelPdoEmulationControl\ControlsEmulation
             */
            public static function native(callable $callback, ...$args)
            {
            }
        }
    }
}