<?= view('departments/department-workspace', ['department' => [
    'role' => 'IT',
    'dashboard' => 'it-dashboard.php',
    'description' => 'Monitor system availability and support employee account access without receiving customer, quote, or service records or changing business roles.',
    'modules' => [
        ['id' => 'systemHealth', 'title' => 'System connection', 'description' => 'The application database connection used by shared workflows.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'accountCount', 'title' => 'Accounts in system', 'description' => 'A count only; IT cannot inspect or change account credentials.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'itUsers', 'title' => 'System user access', 'description' => 'View usernames, departments, and access status. IT may activate or deactivate approved accounts but cannot change roles or manage business data.', 'size' => 'span-4'],
    ],
]]) ?>
