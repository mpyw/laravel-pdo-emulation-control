<?php

namespace Mpyw\LaravelPdoEmulationControl\PHPStan;

use \Illuminate\Database\ConnectionInterface;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

final class ConnectionClassExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return \in_array($methodName, ['emulated', 'native'], true)
            && $classReflection->is(ConnectionInterface::class);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return new EmulationMethod($classReflection, $methodName);
    }
}
