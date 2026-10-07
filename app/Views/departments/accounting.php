<?= view('departments/department-workspace', ['department' => [
    'role' => 'Accounting',
    'dashboard' => 'accounting-dashboard.php',
    'description' => 'Review booking-volume and completion counts. Payment amounts are not recorded in this system, so this is an operational report rather than a financial ledger.',
    'modules' => [
        ['id' => 'bookingCount', 'title' => 'Bookings tracked', 'description' => 'All customer service requests currently in the booking workflow.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'completedCount', 'title' => 'Completed bookings', 'description' => 'Requests marked completed by an authorized operations role.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'accountingBoundary', 'title' => 'Accounting access', 'description' => 'Booking totals are visible here; customer contact details and status changes remain outside this department.', 'size' => 'span-4'],
    ],
]]) ?>
