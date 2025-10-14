<?php

use Core\Session;

// Simple authentication check
Session::start();
if (!Session::isLoggedIn()) {
    header('Location: /signin');
    exit;
}

// Get log file path for display purposes
$logFile = getErrorLogPath();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Logs</title>
    <style>
        :root {
            box-sizing: border-box;
        }
        *, *::before, *::after {
            box-sizing: inherit;
        }
        body {
            font-family: 'Courier New', monospace;
            background-color: #1e1e1e;
            color: #ffffff;
            margin: 0;
            padding: 20px;
            height: 100vh;
        }
        .log-container {
            background-color: #2d2d2d;
            border: 1px solid #444;
            border-radius: 5px;
            padding: 15px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .log-line {
            margin: 2px 0;
            padding: 5px;
            border-radius: 3px;
        }
        .log-line:hover {
            background-color: #3d3d3d;
        }
        .error { color: #ff6b6b; }
        .warning { color: #ffd93d; }
        .info { color: #6bcf7f; }
        .debug { color: #4fc3f7; }
        .timestamp {
            color: #888;
            font-size: 0.9em;
        }
        .refresh-btn {
            background-color: #007acc;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .refresh-btn:hover {
            background-color: #005a9e;
        }
        .auto-refresh {
            margin-left: 10px;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #888;
            font-style: italic;
        }
        .load-more-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px auto;
            display: block;
        }
        .load-more-btn:hover {
            background-color: #218838;
        }
        .load-more-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .load-new-btn {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .load-new-btn:hover {
            background-color: #138496;
        }
        .load-new-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .new-logs-notification {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Application Logs</h1>
    <div class="controls">
        <button class="refresh-btn" onclick="location.reload()">Refresh Logs</button>
        <button id="loadNewLogsBtn" class="load-new-btn" onclick="loadNewLogs()">
            Load New Logs
        </button>
        <label class="auto-refresh">
            <input type="checkbox" id="autoRefresh" onchange="toggleAutoRefresh()"> Auto-refresh (5s)
        </label>
    </div>
    <div id="newLogsNotification" class="new-logs-notification" style="display: none;">
        <span id="newLogsCount">0</span> new log entries available
    </div>
    
    <div class="log-container" id="logContainer">
        <div id="logEntries">
            <!-- Log entries will be loaded here via JavaScript -->
        </div>
        <div id="loadingIndicator" class="loading" style="display: none;">
            Loading logs...
        </div>
        <div id="noLogsMessage" class="log-line info" style="display: none;">
            No log entries found in: <?php echo htmlspecialchars($logFile); ?>
        </div>
        <button id="loadMoreBtn" class="load-more-btn" onclick="loadMoreOlderLogs()" style="display: none;">
            Load More Logs
        </button>
    </div>

    <script>
        let autoRefreshInterval;
        let currentOffset = 0;
        let snapshotTotal = 0;
        let lastNewLogIndex = 0;
        let isLoading = false;
        let hasMore = true;
        let hasNewLogs = false;
        
        // Load initial logs when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadInitialLogs();
            
            // Load auto-refresh state from localStorage
            const autoRefreshCheckbox = document.getElementById('autoRefresh');
            const savedAutoRefresh = localStorage.getItem('logsAutoRefresh');
            if (savedAutoRefresh === 'true') {
                autoRefreshCheckbox.checked = true;
                toggleAutoRefresh();
            }
        });
        
        async function loadInitialLogs() {
            isLoading = true;
            document.getElementById('loadingIndicator').style.display = 'block';
            
            try {
                const response = await fetch('/api/logs/older?offset=0&limit=20&snapshot_total=0');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.lines && data.lines.length > 0) {
                    const logEntries = document.getElementById('logEntries');
                    data.lines.forEach(line => {
                        const logLine = document.createElement('div');
                        logLine.className = 'log-line ' + getLogClass(line);
                        logLine.textContent = line;
                        logEntries.appendChild(logLine);
                    });
                    currentOffset = data.offset;
                    snapshotTotal = data.snapshot_total;
                    lastNewLogIndex = data.current_total;
                    hasMore = data.hasMore;
                    
                    if (hasMore) {
                        document.getElementById('loadMoreBtn').style.display = 'block';
                    }
                } else {
                    document.getElementById('noLogsMessage').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading logs:', error);
                document.getElementById('noLogsMessage').style.display = 'block';
            } finally {
                isLoading = false;
                document.getElementById('loadingIndicator').style.display = 'none';
            }
        }
        
        function toggleAutoRefresh() {
            const checkbox = document.getElementById('autoRefresh');
            
            // Save state to localStorage
            localStorage.setItem('logsAutoRefresh', checkbox.checked);
            
            if (checkbox.checked) {
                autoRefreshInterval = setInterval(async () => {
                    await checkAndLoadNewLogs();
                }, 5000);
            } else {
                clearInterval(autoRefreshInterval);
            }
        }
        
        async function checkAndLoadNewLogs() {
            if (isLoading) return;
            
            try {
                const response = await fetch(`/api/logs/new?last_new_log_index=${lastNewLogIndex}&limit=50`);
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success && data.hasNewLogs && data.lines && data.lines.length > 0) {
                        const logEntries = document.getElementById('logEntries');
                        
                        // Insert new logs at the beginning (newest first)
                        data.lines.forEach(line => {
                            const logLine = document.createElement('div');
                            logLine.className = 'log-line ' + getLogClass(line);
                            logLine.textContent = line;
                            logEntries.insertBefore(logLine, logEntries.firstChild);
                        });
                        
                        // Update the last new log index
                        lastNewLogIndex = data.lastNewLogIndex;
                        
                        // Hide the new logs notification
                        document.getElementById('newLogsNotification').style.display = 'none';
                        hasNewLogs = false;
                    }
                }
            } catch (error) {
                console.error('Error checking for new logs:', error);
            }
        }
        
        async function loadMoreOlderLogs() {
            if (isLoading || !hasMore) return;
            
            isLoading = true;
            document.getElementById('loadingIndicator').style.display = 'block';
            document.getElementById('loadMoreBtn').disabled = true;
            
            try {
                const response = await fetch(`/api/logs/older?offset=${currentOffset}&limit=20&snapshot_total=${snapshotTotal}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.lines && data.lines.length > 0) {
                    const logEntries = document.getElementById('logEntries');
                    data.lines.forEach(line => {
                        const logLine = document.createElement('div');
                        logLine.className = 'log-line ' + getLogClass(line);
                        logLine.textContent = line;
                        logEntries.appendChild(logLine);
                    });
                    currentOffset = data.offset;
                    hasMore = data.hasMore;
                    
                    // Check for new logs using lastNewLogIndex for accurate detection
                    if (data.current_total > lastNewLogIndex) {
                        hasNewLogs = true;
                        document.getElementById('newLogsNotification').style.display = 'block';
                        document.getElementById('newLogsCount').textContent = data.current_total - lastNewLogIndex;
                    }
                } else {
                    hasMore = false;
                }
            } catch (error) {
                console.error('Error loading more logs:', error);
            } finally {
                isLoading = false;
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('loadMoreBtn').disabled = false;
                
                if (!hasMore) {
                    document.getElementById('loadMoreBtn').style.display = 'none';
                }
            }
        }
        
        async function loadNewLogs() {
            if (isLoading) return;
            
            isLoading = true;
            document.getElementById('loadNewLogsBtn').disabled = true;
            document.getElementById('loadingIndicator').style.display = 'block';
            
            try {
                const response = await fetch(`/api/logs/new?last_new_log_index=${lastNewLogIndex}&limit=50`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.hasNewLogs && data.lines && data.lines.length > 0) {
                    const logEntries = document.getElementById('logEntries');
                    
                    // Insert new logs at the beginning (newest first)
                    data.lines.forEach(line => {
                        const logLine = document.createElement('div');
                        logLine.className = 'log-line ' + getLogClass(line);
                        logLine.textContent = line;
                        logEntries.insertBefore(logLine, logEntries.firstChild);
                    });
                    
                    // Update the last new log index
                    lastNewLogIndex = data.lastNewLogIndex;
                    
                    // Hide the new logs notification
                    document.getElementById('newLogsNotification').style.display = 'none';
                    hasNewLogs = false;
                }
            } catch (error) {
                console.error('Error loading new logs:', error);
            } finally {
                isLoading = false;
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('loadNewLogsBtn').disabled = false;
            }
        }
        
        
        function getLogClass(line) {
            if (line.includes('[ERROR]') || line.includes('PHP Error') || line.includes('PHP Fatal')) {
                return 'error';
            } else if (line.includes('[WARNING]') || line.includes('PHP Warning') || line.includes('PHP Notice')) {
                return 'warning';
            } else if (line.includes('[DEBUG]')) {
                return 'debug';
            } else {
                return 'info';
            }
        }
        
        // Infinite scroll - load more when user scrolls near bottom
        document.getElementById('logContainer').addEventListener('scroll', function() {
            const container = this;
            const scrollTop = container.scrollTop;
            const scrollHeight = container.scrollHeight;
            const clientHeight = container.clientHeight;
            
            // Load more when user is within 100px of bottom
            if (scrollHeight - scrollTop - clientHeight < 100 && hasMore && !isLoading) {
                loadMoreOlderLogs();
            }
        });
        
        // Auto-scroll to top to show newest logs
        document.getElementById('logContainer').scrollTop = 0;
    </script>
</body>
</html>
