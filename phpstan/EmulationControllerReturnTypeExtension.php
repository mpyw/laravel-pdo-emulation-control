<?php

namespace Mpyw\LaravelPdoEmulationControl\PHPStan;

use Mpyw\LaravelPdoEmulationControl\EmulationController;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class EmulationControllerReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return EmulationController::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return \in_array($methodReflection->getName(), ['emulated', 'native', 'switchingTo'], true);
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $offset = (int)($methodReflection->getName() === 'switchingTo');

        if (\count($methodCall->getArgs()) > $offset) {
            $type = $scope->getType($methodCall->getArgs()[$offset]->value);

            if ($type instanceof ParametersAcceptor) {
                return $type->getReturnType();
            }
        }

        return new MixedType();
    }
}
