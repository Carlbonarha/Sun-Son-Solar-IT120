<?= view('departments/department-workspace', ['department' => [
    'role' => 'Sales',
    'dashboard' => 'sales-dashboard.php',
    'description' => 'Review customer inquiries and free quote requests, follow up with prospective customers, and track the sales pipeline.',
    'modules' => [
        ['id' => 'salesPending', 'title' => 'Pending opportunities', 'description' => 'Free quote requests awaiting follow-up.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'salesTotal', 'title' => 'Total requests', 'description' => 'All free quote requests submitted.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'bookingQueue', 'title' => 'Sales pipeline', 'description' => 'Search customer requests and review contact and service details.', 'size' => 'span-4'],
        ['id' => 'quoteQueue', 'title' => 'Free quote requests', 'description' => 'Review project details and update quote follow-up status.', 'size' => 'span-4'],
    ],
]]) ?>
