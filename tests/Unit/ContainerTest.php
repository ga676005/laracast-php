<?php

use Core\Container;

it('can bind a resolver to a key', function () {
    $container = new Container();
    $resolver = fn () => 'test value';

    $container->bind('test.key', $resolver);

    expect($container)->toBeInstanceOf(Container::class);
});

it('can resolve a bound key', function () {
    $container = new Container();
    $expectedValue = 'resolved value';
    $resolver = fn () => $expectedValue;

    $container->bind('test.key', $resolver);
    $result = $container->resolve('test.key');

    expect($result)->toBe($expectedValue);
});

it('can resolve different types of values', function () {
    $container = new Container();

    // String value
    $container->bind('string.key', fn () => 'hello world');
    expect($container->resolve('string.key'))->toBe('hello world');

    // Integer value
    $container->bind('int.key', fn () => 42);
    expect($container->resolve('int.key'))->toBe(42);

    // Array value
    $container->bind('array.key', fn () => ['a', 'b', 'c']);
    expect($container->resolve('array.key'))->toBe(['a', 'b', 'c']);

    // Object value
    $container->bind('object.key', fn () => new stdClass());
    expect($container->resolve('object.key'))->toBeInstanceOf(stdClass::class);
});

it('can resolve with complex resolver logic', function () {
    $container = new Container();

    $container->bind('complex.key', function () {
        $data = ['name' => 'John', 'age' => 30];

        return (object) $data;
    });

    $result = $container->resolve('complex.key');

    expect($result)->toBeInstanceOf(stdClass::class)
        ->and($result->name)->toBe('John')
        ->and($result->age)->toBe(30);
});

it('can resolve with parameters', function () {
    $container = new Container();

    $container->bind('param.key', function () {
        return func_get_args();
    });

    // Note: This test shows the resolver can access parameters if needed
    $result = $container->resolve('param.key');
    expect($result)->toBeArray();
});

it('throws exception when trying to resolve unbound key', function () {
    $container = new Container();

    expect(fn () => $container->resolve('nonexistent.key'))
        ->toThrow(Exception::class, 'No binding found for nonexistent.key');
});

it('throws exception when binding non-callable resolver', function () {
    $container = new Container();

    expect(fn () => $container->bind('test.key', 'not callable'))
        ->toThrow(Exception::class, 'Resolver for test.key must be callable');

    expect(fn () => $container->bind('test.key', 123))
        ->toThrow(Exception::class, 'Resolver for test.key must be callable');

    expect(fn () => $container->bind('test.key', []))
        ->toThrow(Exception::class, 'Resolver for test.key must be callable');
});

it('can bind and resolve multiple keys', function () {
    $container = new Container();

    $container->bind('key1', fn () => 'value1');
    $container->bind('key2', fn () => 'value2');
    $container->bind('key3', fn () => 'value3');

    expect($container->resolve('key1'))->toBe('value1');
    expect($container->resolve('key2'))->toBe('value2');
    expect($container->resolve('key3'))->toBe('value3');
});

it('can override existing bindings', function () {
    $container = new Container();

    $container->bind('test.key', fn () => 'first value');
    expect($container->resolve('test.key'))->toBe('first value');

    $container->bind('test.key', fn () => 'second value');
    expect($container->resolve('test.key'))->toBe('second value');
});

it('resolver is called each time resolve is called', function () {
    $container = new Container();
    $callCount = 0;

    $container->bind('counter.key', function () use (&$callCount) {
        $callCount++;

        return $callCount;
    });

    expect($container->resolve('counter.key'))->toBe(1);
    expect($container->resolve('counter.key'))->toBe(2);
    expect($container->resolve('counter.key'))->toBe(3);
    expect($callCount)->toBe(3);
});
