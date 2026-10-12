<?= view('departments/department-workspace', ['department' => [
    'role' => 'Accounting',
    'dashboard' => 'accounting-dashboard.php',
    'description' => 'Finance and billing data will appear here when the system includes a financial ledger. Customer records and service operations remain restricted.',
    'modules' => [
        ['id' => 'accountingBoundary', 'title' => 'Financial records', 'description' => 'No billing or payment records are currently stored in the system. Accounting does not receive access to unrelated customer service data.', 'size' => 'span-4'],
    ],
]]) ?>
