<?php

namespace Mpyw\LaravelPdoEmulationControl;

use Mpyw\Unclosure\Value;
use PDO;

class EmulationController
{
    /**
     * @var \Closure[]|\PDO[]
     */
    protected $pdos;

    /**
     * EmulationController constructor.
     *
     * @param \Closure|\PDO &...$pdos
     */
    public function __construct(&...$pdos)
    {
        // @phpstan-ignore arrayFilter.same
        $this->pdos = array_filter($pdos);
    }

    /**
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return mixed
     */
    public function emulated(callable $callback, ...$args)
    {
        return $this->switchingTo(true, $callback, ...$args);
    }

    /**
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return mixed
     */
    public function native(callable $callback, ...$args)
    {
        return $this->switchingTo(false, $callback, ...$args);
    }

    /**
     * @param  bool     $bool
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return mixed
     */
    public function switchingTo(bool $bool, callable $callback, ...$args)
    {
        // @phpstan-ignore assign.propertyType
        return Value::withEffectForEach($this->pdos, function (PDO $pdo) use ($bool) {
            $original = $pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $bool);

            return function (PDO $pdo) use ($original) {
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $original);
            };
        }, $callback, ...$args);
    }
}
