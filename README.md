# Sun Son Solar

CodeIgniter 4 solar services and product catalog with role-based workspaces.

## Local setup

1. Point Apache at `public/` and configure the MySQL connection in `app/Config/Database.php`.
2. Create the configured database, then run `php spark migrate`.
3. Open `/`. Visitors can browse services and products and submit a free quote request at `/quote.php`. Customers can register and log in immediately. HR or Admin creates employee accounts and assigns their roles.

The `InitialUsers` migration provisions the initial administrator accounts on each database. Admins can create employee accounts through the Accounts view; HR can create them from the HR workspace. Both can approve or decline legacy pending employee registrations. Declining deletes that pending account and profile.

## Workspaces

Admin manages accounts and attendance. Dispatch manages bookings and is the only role that can publish or edit the shared services and products catalog. Technician handles technician-related bookings and check-ins. IT sees basic system status; HR sees the employee directory and attendance; Accounting sees booking totals; Marketing previews public catalog listings; Sales tracks incoming opportunities; Customer Service reviews customer requests. Customers book and track their own services.

Services and Products remain public. Catalog entries, descriptions, and optional photos are stored centrally so Dispatch changes appear to every visitor. Payment amounts are not currently stored; Accounting workspace figures are booking counts only.

Free quote requests are stored separately from service bookings in `quote_requests`. Requests submitted by signed-in customers retain their customer account association; public requests have no customer account. Optional JPG, PNG, WebP, or PDF attachments up to 5 MB are stored under `writable/uploads/quote-requests`, outside the public web directory.
