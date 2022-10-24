--TEST--
Hook match along the inheritance chain from parent to child
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
    // no override of method1
}

$class1 = new Class1();
$class1->method1();

echo PHP_EOL;

$class2 = new Class2();
$class2->method1();

?>
--EXPECT--
Class1::method1 pre.
Class1::method1.
Class1::method1 post.

Class2::method1 pre.
Class1::method1 pre.
Class1::method1.
Class1::method1 post.
Class2::method1 post.

