<?php

/**
 * Simple PHP CS Fixer runner script
 * 
 * Usage:
 * php fix-php.php [options]
 * 
 * Options:
 * --dry-run    Show what would be fixed without making changes
 * --risky      Allow risky fixers
 * --verbose    Show detailed output
 */

$options = getopt('', ['dry-run', 'risky', 'verbose']);

$command = 'php php-cs-fixer.phar fix';

if (isset($options['dry-run'])) {
    $command .= ' --dry-run --diff';
}

if (isset($options['risky'])) {
    $command .= ' --allow-risky=yes';
}

if (isset($options['verbose'])) {
    $command .= ' --verbose';
}

echo "Running: $command\n\n";

$output = [];
$returnCode = 0;

exec($command, $output, $returnCode);

foreach ($output as $line) {
    echo $line . "\n";
}

if ($returnCode === 0) {
    echo "\n✅ PHP CS Fixer completed successfully!\n";
} else {
    echo "\n❌ PHP CS Fixer encountered errors.\n";
    exit($returnCode);
}
