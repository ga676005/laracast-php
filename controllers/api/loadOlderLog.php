<?php

use Core\Log;

// API authentication and admin middleware are handled by the router
// No need for manual authentication checks here

// Get log file path
$logFile = Log::getErrorLogPath();

// Get parameters
$offset = (int) ($_GET['offset'] ?? 0);
$limit = (int) ($_GET['limit'] ?? 20);
$snapshotTotal = (int) ($_GET['snapshot_total'] ?? 0);

// Function to count lines and read specific lines in a single pass
function countAndReadLines($filePath, $startLine, $endLine)
{
    if (!file_exists($filePath)) {
        return ['totalLines' => 0, 'lines' => []];
    }

    $totalLines = 0;
    $lines = [];
    $handle = fopen($filePath, 'r');

    if ($handle) {
        $currentLine = 1;
        while (($line = fgets($handle)) !== false) {
            $totalLines++;

            // Read specific lines if we're in the target range
            if ($currentLine >= $startLine && $currentLine <= $endLine) {
                $trimmedLine = rtrim($line, "\r\n");
                // Only include non-empty lines
                if (!empty($trimmedLine)) {
                    $lines[] = $trimmedLine;
                }
            }

            $currentLine++;
        }
        fclose($handle);
    }

    return ['totalLines' => $totalLines, 'lines' => $lines];
}

// First, count total lines to determine effective total
$tempResult = countAndReadLines($logFile, 1, 1); // Just count, don't read lines
$currentTotal = $tempResult['totalLines'];
$effectiveTotal = min($snapshotTotal, $currentTotal);

// If snapshot_total is 0 (initial load), use current total
if ($snapshotTotal === 0) {
    $effectiveTotal = $currentTotal;
}

// Calculate which lines to read from the file
// Since we want newest logs first, we need to read from the end
$linesToRead = [];
$hasMore = false;

if ($effectiveTotal > 0) {
    // Calculate the range of lines to read from the original file
    $startFromEnd = $effectiveTotal - $offset;
    $endFromEnd = max(1, $startFromEnd - $limit + 1);

    // Convert to 1-indexed line numbers from the beginning of file
    $startLine = $endFromEnd;
    $endLine = $startFromEnd;

    // Read only the specific lines we need
    $result = countAndReadLines($logFile, $startLine, $endLine);
    $linesToRead = $result['lines'];

    // Reverse to show newest first
    $linesToRead = array_reverse($linesToRead);

    // Check if there are more logs to load
    $hasMore = ($offset + $limit) < $effectiveTotal;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'lines' => $linesToRead,
    'hasMore' => $hasMore,
    'total' => $effectiveTotal,
    'offset' => $offset + $limit,
    'snapshot_total' => $effectiveTotal,
    'current_total' => $currentTotal,
    'hasNewLogs' => $currentTotal > $effectiveTotal,
    'success' => true,
]);
