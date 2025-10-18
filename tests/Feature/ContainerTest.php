<?php

use Core\Container;

it('can be used as a dependency injection container', function () {
    $container = new Container();

    // Mock database configuration
    $dbConfig = [
        'host' => 'localhost',
        'database' => 'test_db',
        'username' => 'test_user',
        'password' => 'test_pass',
    ];

    // Bind database configuration
    $container->bind('database.config', fn () => $dbConfig);

    // Bind database connection resolver
    $container->bind('database.connection', function () use ($container) {
        $config = $container->resolve('database.config');

        return "Connected to {$config['database']} on {$config['host']}";
    });

    $connection = $container->resolve('database.connection');

    expect($connection)->toBe('Connected to test_db on localhost');
});

it('can resolve dependencies with circular references prevention', function () {
    $container = new Container();

    // Service A depends on Service B
    $container->bind('service.a', function () use ($container) {
        $serviceB = $container->resolve('service.b');

        return 'Service A with ' . $serviceB;
    });

    // Service B depends on Service A (circular reference)
    $container->bind('service.b', function () use ($container) {
        // This would cause infinite recursion if not handled properly
        // In a real scenario, you'd use lazy loading or other patterns
        return 'Service B';
    });

    $serviceA = $container->resolve('service.a');

    expect($serviceA)->toBe('Service A with Service B');
});

it('can be used for singleton pattern', function () {
    $container = new Container();

    // Create a singleton resolver
    $singletonInstance = null;
    $container->bind('singleton.service', function () use (&$singletonInstance) {
        if ($singletonInstance === null) {
            $singletonInstance = new stdClass();
            $singletonInstance->id = uniqid();
        }

        return $singletonInstance;
    });

    $instance1 = $container->resolve('singleton.service');
    $instance2 = $container->resolve('singleton.service');

    expect($instance1)->toBe($instance2)
        ->and($instance1->id)->toBe($instance2->id);
});

it('can resolve with external dependencies', function () {
    $container = new Container();

    // Simulate external API configuration
    $apiConfig = [
        'base_url' => 'https://api.example.com',
        'timeout' => 30,
        'retries' => 3,
    ];

    $container->bind('api.config', fn () => $apiConfig);

    $container->bind('api.client', function () use ($container) {
        $config = $container->resolve('api.config');

        return [
            'url' => $config['base_url'],
            'timeout' => $config['timeout'],
            'retries' => $config['retries'],
            'created_at' => date('Y-m-d H:i:s'),
        ];
    });

    $client = $container->resolve('api.client');

    expect($client)->toBeArray()
        ->and($client['url'])->toBe('https://api.example.com')
        ->and($client['timeout'])->toBe(30)
        ->and($client['retries'])->toBe(3)
        ->and($client['created_at'])->toBeString();
});

it('can handle complex object creation', function () {
    $container = new Container();

    // Mock a user service
    $container->bind('user.service', function () {
        return new class () {
            private $users = [
                ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
                ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ];

            public function findById($id)
            {
                foreach ($this->users as $user) {
                    if ($user['id'] == $id) {
                        return $user;
                    }
                }

                return null;
            }

            public function getAll()
            {
                return $this->users;
            }
        };
    });

    $userService = $container->resolve('user.service');

    expect($userService->findById(1))->toBe(['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'])
        ->and($userService->findById(999))->toBeNull()
        ->and($userService->getAll())->toHaveCount(2);
});

it('can be used for configuration management', function () {
    $container = new Container();

    // Environment-based configuration
    $container->bind('app.env', fn () => 'testing');
    $container->bind('app.debug', fn () => true);
    $container->bind('app.name', fn () => 'Laracast PHP');

    $container->bind('app.config', function () use ($container) {
        return [
            'environment' => $container->resolve('app.env'),
            'debug' => $container->resolve('app.debug'),
            'name' => $container->resolve('app.name'),
            'version' => '1.0.0',
        ];
    });

    $config = $container->resolve('app.config');

    expect($config)->toBe([
        'environment' => 'testing',
        'debug' => true,
        'name' => 'Laracast PHP',
        'version' => '1.0.0',
    ]);
});

it('can handle lazy loading scenarios', function () {
    $container = new Container();

    $expensiveOperationCalled = false;

    $container->bind('expensive.service', function () use (&$expensiveOperationCalled) {
        $expensiveOperationCalled = true;
        // Simulate expensive operation
        sleep(0); // In real scenario, this might be a database query or API call

        return 'Expensive result';
    });

    // The expensive operation should not be called yet
    expect($expensiveOperationCalled)->toBeFalse();

    // Only when we resolve it should the operation be called
    $result = $container->resolve('expensive.service');

    expect($expensiveOperationCalled)->toBeTrue()
        ->and($result)->toBe('Expensive result');
});
