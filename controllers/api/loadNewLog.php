<?php

// API authentication and admin middleware are handled by the router
// No need for manual authentication checks here

// Get log file path
$logFile = getErrorLogPath();

// Get parameters
$lastNewLogIndex = (int) ($_GET['last_new_log_index'] ?? 0);
$limit = (int) ($_GET['limit'] ?? 50);

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

// Check if there are new logs and read them in one pass
$newLines = [];
$newLastIndex = $lastNewLogIndex;
$currentTotal = 0;
$hasNewLogs = false;

if ($lastNewLogIndex >= 0) {
    // Calculate which lines to read (newest logs)
    $startLine = max(1, $lastNewLogIndex + 1);
    $endLine = 999999; // Read to end of file

    // Count lines and read new lines in one pass
    $result = countAndReadLines($logFile, $startLine, $endLine);
    $currentTotal = $result['totalLines'];
    $newLines = $result['lines'];

    // Check if there are new logs
    $hasNewLogs = $currentTotal > $lastNewLogIndex;

    if ($hasNewLogs) {
        // Limit the number of new lines to fetch
        if (count($newLines) > $limit) {
            $newLines = array_slice($newLines, -$limit);
        }

        // Reverse to show newest first
        $newLines = array_reverse($newLines);

        // Update the last index to the current total
        $newLastIndex = $currentTotal;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'lines' => $newLines,
    'hasNewLogs' => $hasNewLogs,
    'newLogCount' => count($newLines),
    'lastNewLogIndex' => $newLastIndex,
    'currentTotal' => $currentTotal,
    'success' => true,
]);
