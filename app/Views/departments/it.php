<?= view('departments/department-workspace', ['department' => [
    'role' => 'IT',
    'dashboard' => 'it-dashboard.php',
    'description' => 'Monitor the application’s basic operational health and supported catalog data. Account administration remains exclusive to Admin.',
    'modules' => [
        ['id' => 'systemHealth', 'title' => 'System connection', 'description' => 'The application database connection used by shared workflows.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'accountCount', 'title' => 'Accounts in system', 'description' => 'A count only; IT cannot inspect or change account credentials.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'itBoundary', 'title' => 'IT access boundary', 'description' => 'This workspace is read-only. Admin retains account, role, and attendance administration.', 'size' => 'span-4'],
    ],
]]) ?>
