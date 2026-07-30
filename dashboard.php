<?php
// Include the database connection file
require_once 'db_config.php';

// --- 1. Fetch Latest Sensor Readings ---
$latest_reading = [
    'temperature' => 'N/A',
    'ph_level' => 'N/A',
    'turbidity' => 'N/A',
    'timestamp' => 'N/A'
];
$status_messages = [
    'ph_level' => 'Unknown',
    'turbidity' => 'Unknown',
    'temperature' => 'Unknown'
];

$sql_latest = "SELECT * FROM sensorreadings ORDER BY timestamp DESC LIMIT 1";
$result_latest = $conn->query($sql_latest);

if ($result_latest && $result_latest->num_rows > 0) {
    $row = $result_latest->fetch_assoc();
    $latest_reading['temperature'] = $row['temperature'] . '°C';
    $latest_reading['ph_level'] = $row['ph_level'];
    $latest_reading['turbidity'] = $row['turbidity'] . '%';

    // Format the timestamp for display (e.g., October 14 2025 11:59pm)
    $timestamp_formatted = date('F d Y h:iA', strtotime($row['timestamp']));
    $latest_reading['timestamp'] = $timestamp_formatted;

    // Determine status messages based on values (Placeholder Logic)
    // You can adjust these ranges based on ideal aquarium parameters
    
    // PH Status
    if ($row['ph_level'] >= 6.5 && $row['ph_level'] <= 7.5) {
        $status_messages['ph_level'] = 'Neutral';
    } elseif ($row['ph_level'] > 7.5) {
        $status_messages['ph_level'] = 'Alkaline';
    } else {
        $status_messages['ph_level'] = 'Acidic';
    }

    // Turbidity Status
    if ($row['turbidity'] <= 10) {
        $status_messages['turbidity'] = 'Clear';
    } elseif ($row['turbidity'] <= 25) {
        $status_messages['turbidity'] = 'Cloudy';
    } else {
        $status_messages['turbidity'] = 'High';
    }

    // Temperature Status
    if ($row['temperature'] >= 24.0 && $row['temperature'] <= 28.0) {
        $status_messages['temperature'] = 'Stable';
    } elseif ($row['temperature'] > 28.0) {
        $status_messages['temperature'] = 'Hot';
    } else {
        $status_messages['temperature'] = 'Cool';
    }
}


// --- 2. Fetch Recent Log Entries (from Alerts Table) ---
$recent_logs = [];
$sql_logs = "SELECT * FROM alerts ORDER BY timestamp DESC LIMIT 5";
$result_logs = $conn->query($sql_logs);

if ($result_logs && $result_logs->num_rows > 0) {
    while ($row = $result_logs->fetch_assoc()) {
        $recent_logs[] = [
            'timestamp' => date('F d Y h:iA', strtotime($row['timestamp'])),
            'message' => $row['alert_message']
        ];
    }
} else {
    // If no alerts, display a simple log message
    $recent_logs[] = ['timestamp' => date('F d Y h:iA'), 'message' => 'System monitoring started.'];
}

// Close the connection
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquarium Monitoring Dashboard</title>
    <style>
        /* --- General Body Styles --- */
        body {
            background-color: #eaf1f7; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px;
            color: #2c3e50;
        }
        /* --- Header Styles --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            padding-bottom: 10px;
            border-bottom: 3px solid #3498db; 
        }
        .header h1 {
            font-size: 2.8em;
            font-weight: 400;
            margin: 0;
            color: #3498db;
        }
        .timestamp {
            font-size: 1em;
            font-weight: 500;
            color: #7f8c8d;
        }
        /* --- Monitoring Circles Section --- */
        .monitoring-data {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            margin-bottom: 70px;
            padding: 20px 0;
        }
        .circle-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 30%;
            padding: 20px;
        }
        .circle {
            width: 160px;
            height: 160px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); 
            border: 4px solid #3498db;
        }
        .circle-value {
            font-size: 3.5em;
            font-weight: 700;
            color: #2c3e50;
        }
        .circle-status {
            font-size: 1.2em;
            font-weight: 500;
            color: #95a5a6;
        }
        .circle-label {
            font-size: 1.3em;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 10px;
        }
        /* --- Logs Section --- */
        .logs-section {
            padding: 20px;
            border-top: 1px solid #bdc3c7;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .logs-section h2 {
            font-size: 2em;
            margin-top: 0;
            margin-bottom: 15px;
            color: #3498db;
        }
        .log-entry {
            font-size: 1em;
            color: #555;
            padding: 5px 0;
            border-bottom: 1px dotted #ecf0f1;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        /* --- Test Form Styles --- */
        .test-form {
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .test-form h3 {
            color: #3498db;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Aquarium Monitoring</h1>
        <span class="timestamp">
            <?php echo $latest_reading['timestamp']; ?>
        </span>
    </div>

    <div class="monitoring-data">
        
        <div class="circle-container">
            <div class="circle">
                <div class="circle-value"><?php echo $latest_reading['ph_level']; ?></div>
                <div class="circle-status"><?php echo $status_messages['ph_level']; ?></div>
            </div>
            <div class="circle-label">Ph Level</div>
        </div>

        <div class="circle-container">
            <div class="circle">
                <div class="circle-value"><?php echo $latest_reading['turbidity']; ?></div>
                <div class="circle-status"><?php echo $status_messages['turbidity']; ?></div>
            </div>
            <div class="circle-label">Turbidity</div>
        </div>

        <div class="circle-container">
            <div class="circle">
                <div class="circle-value"><?php echo $latest_reading['temperature']; ?></div>
                <div class="circle-status"><?php echo $status_messages['temperature']; ?></div>
            </div>
            <div class="circle-label">Temperature</div>
        </div>

    </div>

    <div class="logs-section">
        <h2>Logs</h2>
        <?php foreach ($recent_logs as $log): ?>
            <div class="log-entry">
                <?php echo $log['timestamp']; ?> - **Alert:** <?php echo $log['message']; ?>
            </div>
        <?php endforeach; ?>

        <?php if (empty($recent_logs)): ?>
            <div class="log-entry">No recent alerts recorded.</div>
        <?php endif; ?>
    </div>

    <!-- Test Form to Submit Sensor Data -->
    <div class="test-form">
        <h3>Test: Submit Sensor Data</h3>
        <form id="sensorForm">
            <div class="form-group">
                <label for="temperature">Temperature (°C):</label>
                <input type="number" id="temperature" name="temperature" step="0.01" placeholder="e.g., 26.5" required>
            </div>
            <div class="form-group">
                <label for="ph_level">pH Level:</label>
                <input type="number" id="ph_level" name="ph_level" step="0.01" placeholder="e.g., 7.0" required>
            </div>
            <div class="form-group">
                <label for="turbidity">Turbidity (%):</label>
                <input type="number" id="turbidity" name="turbidity" step="0.01" placeholder="e.g., 15" required>
            </div>
            <div class="form-group">
                <button type="submit">Submit Sensor Data</button>
            </div>
        </form>
    </div>

    <script>
        // Handle form submission
        document.getElementById('sensorForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const temperature = document.getElementById('temperature').value;
            const ph_level = document.getElementById('ph_level').value;
            const turbidity = document.getElementById('turbidity').value;
            
            try {
                const response = await fetch('save_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `temperature=${temperature}&ph_level=${ph_level}&turbidity=${turbidity}`
                });
                
                const result = await response.text();
                alert('Response: ' + result);
                
                // Reload page to show updated data
                if (response.ok) {
                    setTimeout(() => location.reload(), 1000);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });
    </script>

</body>
</html>