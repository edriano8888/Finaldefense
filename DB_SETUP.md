## Database Setup (Armful Media – Single Company)

### Option A: phpMyAdmin (recommended)
1. Open: http://localhost/phpmyadmin
2. Click **Import**
3. Choose file: `armful_db_schema.sql` (in project root)
4. Click **Go**

This will:
- Create database `armful_db` (if missing)
- Create all required tables (if missing)
- Insert default subscription plans
- Insert default users:
  - `admin@armfulmedia.com` / `admin123`
  - `manager@armfulmedia.com` / `manager123`

### Option B: MySQL CLI
From a terminal:
`mysql -u root -p < armful_db_schema.sql`

### App config
Database config uses `.env`:
- `DB_HOSTNAME`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_NAME`
