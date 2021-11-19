<?php

namespace Mpyw\LaravelPdoEmulationControl\PHPStan;

use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class CallableFacadeReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return DB::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return \in_array($methodReflection->getName(), ['emulated', 'native'], true);
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        if (\count($methodCall->getArgs()) > 0) {
            $type = $scope->getType($methodCall->getArgs()[0]->value);

            if ($type instanceof ParametersAcceptor) {
                return $type->getReturnType();
            }
        }

        return new MixedType();
    }
}
