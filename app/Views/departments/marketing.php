<?= view('departments/department-workspace', ['department' => [
    'role' => 'Marketing',
    'dashboard' => 'marketing-dashboard.php',
    'description' => 'Manage the public catalog and moderate customer stories before publication.',
    'modules' => [
        ['id' => 'catalogCount', 'title' => 'Live catalog listings', 'description' => 'Products and services currently published to the public catalog.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'catalogBreakdown', 'title' => 'Catalog breakdown', 'description' => 'Published product and service listing counts.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'catalogLinks', 'title' => 'Manage website catalog', 'description' => 'Review and update the public service and product listings.', 'size' => 'span-4'],
        ['id' => 'customerReviews', 'title' => 'Customer stories', 'description' => 'Review submissions and publish only customer-approved stories.', 'size' => 'span-4'],
    ],
]]) ?>
