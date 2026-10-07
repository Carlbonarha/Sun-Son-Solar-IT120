<?= view('departments/department-workspace', ['department' => [
    'role' => 'Marketing',
    'dashboard' => 'marketing-dashboard.php',
    'description' => 'Review the live public catalog and keep campaign content aligned with the products and services customers can see.',
    'modules' => [
        ['id' => 'catalogCount', 'title' => 'Live catalog listings', 'description' => 'Products and services currently published to the public catalog.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'catalogBreakdown', 'title' => 'Catalog breakdown', 'description' => 'Published product and service listing counts.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'catalogLinks', 'title' => 'Preview public pages', 'description' => 'Open the customer-facing pages. Catalog edits are exclusive to Dispatch.', 'size' => 'span-4'],
    ],
]]) ?>
