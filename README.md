# Sun Son Solar

CodeIgniter 4 solar services and product catalog with role-based workspaces.

## Local setup

1. Point Apache at `public/` and configure the MySQL connection in `app/Config/Database.php`.
2. Create the configured database, then run `php spark migrate`.
3. Open `/login.php`. Customers can register; Admin creates staff accounts and assigns departments.

## Workspaces

Admin manages accounts and attendance. Dispatch manages bookings and is the only role that can publish or edit the shared services and products catalog. Technician handles technician-related bookings and check-ins. IT sees basic system status; HR sees the employee directory and attendance; Accounting sees booking totals; Marketing previews public catalog listings; Sales tracks incoming opportunities; Customer Service reviews customer requests. Customers book and track their own services.

Services and Products remain public. Catalog entries, descriptions, and optional photos are stored centrally so Dispatch changes appear to every visitor. Payment amounts are not currently stored; Accounting workspace figures are booking counts only.
