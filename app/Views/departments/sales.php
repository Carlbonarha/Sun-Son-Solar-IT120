<?= view('departments/department-workspace', ['department' => [
    'role' => 'Sales',
    'dashboard' => 'sales-dashboard.php',
    'description' => 'Track incoming service requests as opportunities and search the pipeline. Dispatch owns booking status changes.',
    'modules' => [
        ['id' => 'salesPending', 'title' => 'Pending opportunities', 'description' => 'Requests waiting for operations review.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'salesTotal', 'title' => 'Total requests', 'description' => 'All customer requests in the shared booking workflow.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'bookingQueue', 'title' => 'Sales pipeline', 'description' => 'Search customer requests and review contact and service details.', 'size' => 'span-4'],
    ],
]]) ?>
