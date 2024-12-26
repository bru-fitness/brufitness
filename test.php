<?php
session_start();
require_once 'dbConnect.php';

// Fetch monthly revenue for service usage
$service_revenue_query = "SELECT MONTH(service_date) AS month, SUM(payrate_service) AS total_revenue 
                          FROM service_usage 
                          GROUP BY MONTH(service_date)";
$service_revenue_result = $conn->query($service_revenue_query);

$service_revenue_data = array_fill(1, 12, 0);
while ($row = $service_revenue_result->fetch_assoc()) {
    $service_revenue_data[(int)$row['month']] = (float)$row['total_revenue'];
}

// Fetch monthly revenue for signups
$signup_revenue_query = "SELECT MONTH(recorddate) AS month, SUM(payrate_signup) AS total_revenue 
                         FROM signup 
                         GROUP BY MONTH(recorddate)";
$signup_revenue_result = $conn->query($signup_revenue_query);

$signup_revenue_data = array_fill(1, 12, 0);
while ($row = $signup_revenue_result->fetch_assoc()) {
    $signup_revenue_data[(int)$row['month']] = (float)$row['total_revenue'];
}

// Fetch monthly revenue for renewals
$renewal_revenue_query = "SELECT MONTH(renewal_date) AS month, SUM(payrate_renewal) AS total_revenue 
                          FROM renewals 
                          GROUP BY MONTH(renewal_date)";
$renewal_revenue_result = $conn->query($renewal_revenue_query);

$renewal_revenue_data = array_fill(1, 12, 0);
while ($row = $renewal_revenue_result->fetch_assoc()) {
    $renewal_revenue_data[(int)$row['month']] = (float)$row['total_revenue'];
}

$months_th = [
    1 => "มกราคม", 2 => "กุมภาพันธ์", 3 => "มีนาคม", 4 => "เมษายน",
    5 => "พฤษภาคม", 6 => "มิถุนายน", 7 => "กรกฎาคม", 8 => "สิงหาคม",
    9 => "กันยายน", 10 => "ตุลาคม", 11 => "พฤศจิกายน", 12 => "ธันวาคม"
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานรายได้</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .chart-container {
        margin: 20px 0;
        width: 80%;
        max-width: 600px;
    }
</style>
</head>
<body>
    <h1>รายงานรายได้</h1>

    <div class="chart-container">
        <h2>รายได้จากการเข้าใช้บริการ</h2>
        <canvas id="serviceRevenueChart"></canvas>
    </div>

    <div class="chart-container">
        <h2>รายได้จากการสมัครสมาชิก</h2>
        <canvas id="signupRevenueChart"></canvas>
    </div>

    <div class="chart-container">
        <h2>รายได้จากการต่ออายุสมาชิก</h2>
        <canvas id="renewalRevenueChart"></canvas>
    </div>

    <script>
        const monthsTh = <?= json_encode(array_values($months_th)) ?>;
        const serviceRevenueData = <?= json_encode(array_values($service_revenue_data)) ?>;
        const signupRevenueData = <?= json_encode(array_values($signup_revenue_data)) ?>;
        const renewalRevenueData = <?= json_encode(array_values($renewal_revenue_data)) ?>;

        const renderChart = (ctx, label, data) => {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthsTh,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: '#4CAF50',
                        fill: false,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'เดือน'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1000
                            },
                            title: {
                                display: true,
                                text: 'จำนวนเงิน (บาท)'
                            }
                        },
                    },
                },
            });
        };

        renderChart(
            document.getElementById('serviceRevenueChart').getContext('2d'),
            'รายได้จากการเข้าใช้บริการ',
            serviceRevenueData
        );

        renderChart(
            document.getElementById('signupRevenueChart').getContext('2d'),
            'รายได้จากการสมัครสมาชิก',
            signupRevenueData
        );

        renderChart(
            document.getElementById('renewalRevenueChart').getContext('2d'),
            'รายได้จากการต่ออายุสมาชิก',
            renewalRevenueData
        );
    </script>
</body>
</html>

<?php
$conn->close();
?>
