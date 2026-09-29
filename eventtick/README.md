# EventTick - Event Ticketing Reservation System (prototype)

CC 207 midterm project. PHP (mysqli) + MySQL, three-tier setup like sa proposal.

## Paano i-run
1. Copy the folder sa `htdocs/eventtick` (XAMPP) or sa web root mo.
2. I-import yung `database/schema.sql` sa phpMyAdmin. Gagawa na siya ng `eventtick` database, tables, at sample events.
3. Check `config/db.php` kung tama yung DB user/password mo.
4. Open `http://localhost/eventtick/setup.php` once para magawa yung demo accounts, then delete na yung setup.php.
5. Open `http://localhost/eventtick/`

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@eventtick.test | admin123 |
| Customer | juan@example.com | juan1234 |

## Modules
1. Login / Register / Logout - `login.php`, `register.php`, `logout.php`
2. Event browsing (dashboard) - `dashboard.php`
3. Event details + ticket selection - `event.php`
4. Reservation / checkout - `checkout.php`
5. Payment (simulated lang, wala pang totoong gateway) - `payment.php`
6. E-ticket - `ticket.php`
7. My reservations - `my_reservations.php`
8. Admin (sales overview + manage events) - `admin/index.php`, `admin/events.php`

## Notes / TODO
- Payment is simulated palang, dapat palitan yung success logic sa payment.php kapag may totoong gateway na (paymongo etc).
- QR code sa ticket.php ay placeholder lang, need palitan ng actual QR generator library.
- Walang seat selection kasi wala namang seats table sa ERD, per ticket type lang muna binibili.
- Baka gusto niyo palitan yung mysqli queries with prepared statements para mas safe sa SQL injection, sa prototype lang kasi to.
