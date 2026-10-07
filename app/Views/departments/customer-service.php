<?= view('departments/department-workspace', ['department' => [
    'role' => 'Customer Service',
    'dashboard' => 'customer-service-dashboard.php',
    'description' => 'Review customer service requests and contact details to help customers. Dispatch retains exclusive control of booking status.',
    'modules' => [
        ['id' => 'serviceRequests', 'title' => 'Customer requests', 'description' => 'Requests currently recorded in the shared booking system.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'servicePending', 'title' => 'Awaiting operations', 'description' => 'Requests still marked pending.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'bookingQueue', 'title' => 'Customer support queue', 'description' => 'Search request details and contact information. Status changes are Dispatch-only.', 'size' => 'span-4'],
    ],
]]) ?>
