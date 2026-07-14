<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<style>
    /* Custom Stats Cards based on user's image */
    .modern-stat-card {
        border-radius: 8px;
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    .modern-stat-card .title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    .modern-stat-card .value {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 25px;
        line-height: 1;
        display: flex;
        align-items: center;
    }
    .modern-stat-card .icon {
        position: absolute;
        right: 20px;
        top: 30px;
        font-size: 3.5rem;
        opacity: 0.4;
    }
    .modern-stat-card .footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        color: white;
        opacity: 0.9;
        margin-top: auto;
    }
    .modern-stat-card .footer:hover {
        opacity: 1;
    }
    
    /* Colors matching the image exactly */
    .bg-card-purple { background-color: #7b4df2; }
    .bg-card-green { background-color: #4cd587; }
    .bg-card-yellow { background-color: #ffc107; }
    
    /* Chart card styling */
    .chart-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .chart-card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
</style>

<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                Dashboard Overview
            </h1>
            <span class="text-muted fs-7 fw-semibold mt-1">Monitor your WhatsApp campaigns and delivery metrics</span>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            
            <div class="col-md-4">
                <div class="modern-stat-card bg-card-purple">
                    <div class="title">Total Accounts</div>
                    <div class="value" id="stat-accounts">
                        <span class="spinner-border spinner-border-sm" style="width: 1.5rem; height: 1.5rem;"></span>
                    </div>
                    <div class="icon">
                        <i class="ki-outline ki-whatsapp"></i>
                    </div>
                    <a href="<?php echo e(route('whatsapp_accounts.index')); ?>" class="footer">
                        <span>View Details</span>
                        <i class="ki-outline ki-arrow-right fs-4 text-white"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="modern-stat-card bg-card-green">
                    <div class="title">Messages Sent</div>
                    <div class="value" id="stat-messages">
                        <span class="spinner-border spinner-border-sm" style="width: 1.5rem; height: 1.5rem;"></span>
                    </div>
                    <div class="icon">
                        <i class="ki-outline ki-directbox-default"></i>
                    </div>
                    <a href="<?php echo e(route('whatsapp_messages.index')); ?>" class="footer">
                        <span>View Details</span>
                        <i class="ki-outline ki-arrow-right fs-4 text-white"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="modern-stat-card bg-card-yellow">
                    <div class="title">Bulk Campaigns</div>
                    <div class="value" id="stat-campaigns">
                        <span class="spinner-border spinner-border-sm" style="width: 1.5rem; height: 1.5rem;"></span>
                    </div>
                    <div class="icon">
                        <i class="ki-outline ki-rocket"></i>
                    </div>
                    <a href="<?php echo e(route('admin.bulk_campaigns.index')); ?>" class="footer">
                        <span>View Details</span>
                        <i class="ki-outline ki-arrow-right fs-4 text-white"></i>
                    </a>
                </div>
            </div>

        </div>
        
        <!-- Chart Row -->
        <div class="row">
            <div class="col-12">
                <div class="chart-card">
                    <div class="chart-card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-dark">Delivery Performance (Last 7 Days)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div style="position: relative; height:350px; width:100%;">
                            <canvas id="messagesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end::Content-->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fetch stats via AJAX
    fetch("<?php echo e(route('dashboard.stats')); ?>")
        .then(response => response.json())
        .then(data => {
            // Update stats
            document.getElementById('stat-accounts').innerHTML = data.stats.accounts + '<span style="font-size: 1rem; margin-left: 10px; font-weight: normal; opacity: 0.8;">(' + data.stats.connected + ' Active)</span>';
            document.getElementById('stat-messages').innerText = new Intl.NumberFormat().format(data.stats.messages_sent);
            document.getElementById('stat-campaigns').innerText = data.stats.campaigns;

            // Render Chart
            const ctx = document.getElementById('messagesChart').getContext('2d');
            
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(13, 110, 253, 0.15)');
            gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.graph.labels,
                    datasets: [{
                        label: 'Messages Delivered',
                        data: data.graph.data,
                        backgroundColor: gradient,
                        borderColor: '#0d6efd',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0d6efd',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#fff',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.04)',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#64748b',
                                padding: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#64748b',
                                padding: 10
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        })
        .catch(error => console.error('Error fetching dashboard stats:', error));
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/dashboard.blade.php ENDPATH**/ ?>