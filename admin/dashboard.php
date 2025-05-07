<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "WildAlert_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get counts for dashboard
$totalSpecies = $conn->query("SELECT COUNT(*) as count FROM tbl_species")->fetch_assoc()['count'];
$totalReports = $conn->query("SELECT COUNT(*) as count FROM tbl_reports")->fetch_assoc()['count'];
$pendingReports = $conn->query("SELECT COUNT(*) as count FROM tbl_reports WHERE status_report_id = 1")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM tbl_account WHERE role_id = 1")->fetch_assoc()['count'];

// Get recent reports
$recentReports = $conn->query("
    SELECT r.*, s.status_rp_name 
    FROM tbl_reports r
    JOIN tbl_status_report s ON r.status_report_id = s.status_rp_id
    ORDER BY r.date_time DESC
    LIMIT 5
");

// Get species distribution by category
$speciesDistribution = $conn->query("
    SELECT c.category_name, COUNT(*) as count 
    FROM tbl_species s
    JOIN tbl_category c ON s.category_id = c.category_id
    GROUP BY c.category_name
    ORDER BY count DESC
");

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildAlert Admin - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/sidebar_style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        .dashboard-container {
            padding: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            color: #054a29;
            margin: 0;
        }

        .date-time {
            background-color: #054a29;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            align-items: center;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            color: white;
        }

        .species-icon {
            background-color: #0e6245;
        }

        .reports-icon {
            background-color: #d35400;
        }

        .pending-icon {
            background-color: #e74c3c;
        }

        .users-icon {
            background-color: #3498db;
        }

        .card-content h3 {
            margin: 0;
            font-size: 1.8rem;
        }

        .card-content p {
            margin: 5px 0 0 0;
            color: #777;
            font-size: 0.9rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .recent-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .recent-section h2 {
            margin-top: 0;
            color: #054a29;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            font-size: 1.3rem;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: white;
            display: inline-block;
        }

        .status-pending {
            background-color: #f39c12;
        }

        .status-surveillance {
            background-color: #3498db;
        }

        .status-investigation {
            background-color: #9b59b6;
        }

        .status-verified {
            background-color: #2ecc71;
        }

        .status-no-action {
            background-color: #95a5a6;
        }

        .status-closed {
            background-color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        table th {
            color: #054a29;
            font-weight: 600;
        }

        .chart-container {
            height: 300px;
            margin-top: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .date-time {
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <div class="date-time" id="datetime">
                    <i class="fas fa-calendar-alt"></i>
                    <span id="current-datetime"></span>
                </div>
            </div>

            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-icon species-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $totalSpecies; ?></h3>
                        <p>Total Species</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon reports-icon">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $totalReports; ?></h3>
                        <p>Total Reports</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon pending-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $pendingReports; ?></h3>
                        <p>Pending Reports</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon users-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $totalUsers; ?></h3>
                        <p>Registered Users</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="recent-section">
                    <h2><i class="fas fa-clipboard-list"></i> Recent Reports</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Species</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($recentReports && $recentReports->num_rows > 0) {
                                while ($report = $recentReports->fetch_assoc()) {
                                    $statusClass = '';
                                    switch ($report['status_report_id']) {
                                        case 1:
                                            $statusClass = 'status-pending';
                                            break;
                                        case 2:
                                            $statusClass = 'status-surveillance';
                                            break;
                                        case 3:
                                            $statusClass = 'status-investigation';
                                            break;
                                        case 4:
                                            $statusClass = 'status-verified';
                                            break;
                                        case 5:
                                            $statusClass = 'status-no-action';
                                            break;
                                        case 6:
                                            $statusClass = 'status-closed';
                                            break;
                                    }
                                    echo '<tr>
                                        <td>' . htmlspecialchars($report['species_name']) . '</td>
                                        <td>' . htmlspecialchars($report['location']) . '</td>
                                        <td>' . date('M d, Y', strtotime($report['date_time'])) . '</td>
                                        <td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($report['status_rp_name']) . '</span></td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" style="text-align: center;">No recent reports found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="recent-section">
                    <h2><i class="fas fa-chart-pie"></i> Species Distribution</h2>
                    <div class="chart-container" id="species-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.0/apexcharts.min.js"></script>
    <script>
        // Current date and time
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('current-datetime').textContent = now.toLocaleDateString('en-US', options);
        }

        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Species distribution chart
        document.addEventListener('DOMContentLoaded', function() {
            const speciesData = [
                <?php
                if ($speciesDistribution && $speciesDistribution->num_rows > 0) {
                    while ($category = $speciesDistribution->fetch_assoc()) {
                        echo '{
                            category: "' . $category['category_name'] . '",
                            count: ' . $category['count'] . '
                        },';
                    }
                }
                ?>
            ];

            const options = {
                series: speciesData.map(item => item.count),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: speciesData.map(item => item.category),
                colors: ['#0e6245', '#2ecc71', '#3498db', '#9b59b6', '#e74c3c'],
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 250
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const chart = new ApexCharts(document.querySelector("#species-chart"), options);
            chart.render();
        });
    </script>
</body>

</html>