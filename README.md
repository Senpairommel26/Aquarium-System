# Aquarium Monitoring System

Simple PHP + MySQL project to collect aquarium sensor readings, auto-generate alerts, and display a dashboard.

## Files

- [index.html](index.html) — Landing page (link to dashboard).
- [dashboard.php](dashboard.php) — Main dashboard showing latest readings and recent alerts.
- [save_data.php](save_data.php) — POST endpoint to insert sensor data and create alerts.
- [db_config.php](db_config.php) — Database connection configuration used by PHP scripts.
- [aquarium_system.sql](aquarium_system.sql) — SQL schema to create the database and tables.

## Requirements

- PHP (7.4+) with mysqli enabled
- MySQL / MariaDB
- A local webserver (XAMPP, WAMP, MAMP) or similar

## Setup

1. Place the project folder in your web root (for XAMPP on Windows: `C:\xampp\htdocs\aquarium-system`).
2. Import the database schema:

   - Using phpMyAdmin: import the file [aquarium_system.sql](aquarium_system.sql).
   - Or from command line:

   ```bash
   mysql -u root -p < aquarium_system.sql
   ```

3. Verify `db_config.php` has correct DB credentials for your environment. Defaults assume XAMPP (`user=root`, no password, `localhost`).
4. Start your webserver and open the site: http://localhost/aquarium-system/

## Usage

- Click **GET STARTED** on [index.html](index.html) to open the dashboard.
- The dashboard displays the latest reading from `sensorreadings` and recent alerts from `alerts`.
- Use the test form on the dashboard to POST sample readings to [save_data.php](save_data.php). The form sends `temperature`, `ph_level`, and `turbidity` via POST.

## API / Endpoint

- `POST save_data.php` — Accepts `application/x-www-form-urlencoded` parameters:
  - `temperature` (float)
  - `ph_level` (float)
  - `turbidity` (float)

On insert, the script writes to `sensorreadings` and inserts alert rows into `alerts` if values are out-of-range.

## Database schema (summary)

- `sensorreadings` — columns: `reading_id` (PK), `timestamp`, `temperature`, `ph_level`, `turbidity`.
- `alerts` — columns: `alert_id` (PK), `timestamp`, `parameter_type`, `recorded_value`, `alert_message`.

## Notes & Suggestions

- Security: this project uses mysqli and simple input casting — consider adding CSRF protection, input validation, and prepared statements (already used for inserts). Restrict access to database credentials.
- Deployment: ensure appropriate PHP version, configure file permissions, and set a database password for production.
- Improvements: add authentication, AJAX refresh for live updates, historical charts, and pagination for alerts.

## Troubleshooting

- If you see `Connection failed` from `db_config.php`, confirm MySQL is running and credentials are correct.
- If inserts fail, check PHP error logs and enable display_errors in development.

## License

This repository contains example code. Use and modify freely.