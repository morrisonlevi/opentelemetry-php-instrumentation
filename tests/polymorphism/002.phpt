--TEST--
Hook matches exact scopes only, parent delegation flavor
--EXTENSIONS--
otel_instrumentation
--FILE--
<?php

OpenTelemetry\Instrumentation\hook(
    Class1::class,
    'method1',
    pre: function (Class1 $self, array $params, string $class, string $function, ?string $filename, ?int $lineno) {
        echo "Class1::method1 pre.\n";
    },
    post: function (Class1 $self, array $params, $returnValue, ?Throwable $exception) {
        echo "Class1::method1 post.\n";
    });

OpenTelemetry\Instrumentation\hook(
    Class2::class,
    'method1',
    pre: function (Class2 $self, array $params, string $class, string $function, ?string $filename, ?int $lineno) {
        echo "Class2::method1 pre.\n";
    },
    post: function (Class2 $self, array $params, $returnValue, ?Throwable $exception) {
        echo "Class2::method1 post.\n";
    });


class Class1 {
    function method1(): void {
        echo __METHOD__, ".\n";
    }
}

class Class2 extends Class1 {
    function method1(): void {
        echo "Calling parent's method1.\n";
        parent::method1();
        echo "Done calling parent's method1.\n";
    }
}

$class = new Class2();
$class->method1();

?>
--EXPECT--
Class2::method1 pre.
Calling parent's method1.
Class1::method1 pre.
Class1::method1.
Class1::method1 post.
Done calling parent's method1.
Class2::method1 post.

