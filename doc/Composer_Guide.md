# Composer Guide for Laracast PHP Project

## Install composer in current directory
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'ed0feb545ba87161262f2d45a633e34f591ebb3381f2e0063c345ebea4d228dd0043083717770234ec00c5a9f9593792') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

## What is Composer?

Composer is a dependency manager for PHP that allows you to:
- Install and manage PHP packages/libraries
- Handle autoloading of classes
- Manage project dependencies
- Keep track of package versions

## Current Project Setup

This project uses Composer locally with `composer.phar` in the project root directory.

### Files in this project:
- `composer.phar` - Composer executable (local installation)
- `composer.json` - Project dependencies and configuration (to be created)
- `composer.lock` - Locked dependency versions (generated automatically)
- `vendor/` - Directory containing installed packages (generated automatically)

## Basic Composer Commands

### Using the local composer.phar:

```bash
# Install dependencies
php composer.phar install

# Add a new package
php composer.phar require package/name

# Add a development dependency
php composer.phar require --dev package/name

# Update dependencies
php composer.phar update

# Update a specific package
php composer.phar update package/name

# Remove a package
php composer.phar remove package/name

# Show installed packages
php composer.phar show

# Check for outdated packages
php composer.phar outdated

# Validate composer.json
php composer.phar validate

# Show package information
php composer.phar show package/name
```

### Alternative: Create an alias (optional)

Add this to your `.bash_profile` to use `composer` instead of `php composer.phar`:

```bash
alias composer="php composer.phar"
```

Then you can use:
```bash
composer install
composer require package/name
# etc.
```

## Setting Up Composer for This Project

### Step 1: Initialize Composer (if not done already)

```bash
php composer.phar init
```

This will create a `composer.json` file with basic project information.

### Step 2: Example composer.json for this project

```json
{
    "name": "laracast/php-project",
    "description": "A PHP web application built following Laracast tutorials",
    "type": "project",
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "core/",
            "App\\Controllers\\": "controllers/",
            "App\\Models\\": "models/"
        },
        "files": [
            "core/helpers.php"
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "serve": "php -S localhost:8080 -t public"
    },
    "config": {
        "optimize-autoloader": true,
        "sort-packages": true
    }
}
```

### Step 3: Install dependencies

```bash
php composer.phar install
```

## Autoloading

Composer can automatically load your classes without manual `require` statements.

### PSR-4 Autoloading

With the above `composer.json` configuration:

```php
// Instead of: require 'core/Database.php';
// You can directly use:
$db = new App\Database();

// Instead of: require 'controllers/UserController.php';
// You can directly use:
$controller = new App\Controllers\UserController();
```

### Files Autoloading

Helper functions in `core/helpers.php` will be automatically loaded.

### Using Autoloader

In your `bootstrap.php` or main entry point:

```php
<?php
require_once 'vendor/autoload.php';

// Now all your classes are available
$app = new App\App();
```

## Common Packages for PHP Projects

### Essential Packages

```bash
# HTTP client
php composer.phar require guzzlehttp/guzzle

# Database ORM
php composer.phar require illuminate/database

# Validation
php composer.phar require respect/validation

# Environment variables
php composer.phar require vlucas/phpdotenv

# Logging
php composer.phar require monolog/monolog
```

### Development Packages

```bash
# Testing
php composer.phar require --dev phpunit/phpunit

# Code formatting
php composer.phar require --dev friendsofphp/php-cs-fixer

# Static analysis
php composer.phar require --dev phpstan/phpstan
```

## Project-Specific Recommendations

### For this Laracast PHP project:

1. **Add PSR-4 autoloading** to eliminate manual `require` statements
2. **Use environment variables** for configuration (database credentials, etc.)
3. **Add validation library** for form validation
4. **Add logging** for better debugging
5. **Add testing framework** for unit tests

### Example package additions:

```bash
# Add environment variable support
php composer.phar require vlucas/phpdotenv

# Add validation
php composer.phar require respect/validation

# Add logging
php composer.phar require monolog/monolog

# Add testing (development only)
php composer.phar require --dev phpunit/phpunit
```

## Environment Setup

### Using .env files:

1. Install dotenv package:
```bash
php composer.phar require vlucas/phpdotenv
```

2. Create `.env` file:
```env
DB_HOST=localhost
DB_NAME=laracast_php
DB_USER=root
DB_PASS=
APP_ENV=local
APP_DEBUG=true
```

3. Load in `bootstrap.php`:
```php
<?php
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

## Scripts

Add custom scripts to `composer.json`:

```json
{
    "scripts": {
        "serve": "php -S localhost:8080 -t public",
        "test": "phpunit",
        "format": "php-cs-fixer fix",
        "analyze": "phpstan analyse"
    }
}
```

Run with:
```bash
php composer.phar run serve
php composer.phar run test
php composer.phar run format
```

## Best Practices

1. **Commit composer.lock** to version control
2. **Don't commit vendor/** directory
3. **Use specific versions** in production
4. **Update regularly** but test thoroughly
5. **Use dev dependencies** for development tools
6. **Use autoloading** instead of manual requires

## Troubleshooting

### Common Issues:

1. **Memory limit errors**:
```bash
php -d memory_limit=2G composer.phar install
```

2. **Permission errors** (Windows):
Run Git Bash as Administrator

3. **Network issues**:
```bash
php composer.phar install --prefer-dist
```

4. **Clear cache**:
```bash
php composer.phar clear-cache
```

## Integration with Current Project

To integrate Composer with your existing project:

1. **Create composer.json** with autoloading configuration
2. **Add require 'vendor/autoload.php'** to `bootstrap.php`
3. **Refactor existing requires** to use autoloading
4. **Add useful packages** gradually
5. **Update .gitignore** to exclude `vendor/` directory

## .gitignore Addition

Add to your `.gitignore`:
```
/vendor/
composer.lock
.env
```

## Summary

Composer will help you:
- ✅ Manage dependencies cleanly
- ✅ Use autoloading for better code organization
- ✅ Add useful packages easily
- ✅ Maintain consistent development environment
- ✅ Prepare for production deployment

Start with basic autoloading and gradually add packages as needed!
