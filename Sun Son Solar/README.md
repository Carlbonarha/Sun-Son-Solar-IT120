# 🌞 Sun Son Solar - Web System

Web-based management system for Sun Son Solar, built with HTML, CSS, JavaScript, and SQLite.

---

<<<<<<< HEAD
##  Project Files Structure
=======
## Project Files Structure
>>>>>>> 16ab10e5a84020dfb520efb9a9c0562f4987ea67

```
sun-son-solar/
├── index.html (or login.html)      # Login page - start here
├── register.html                    # User registration page
├── dashboard.html                   # Main dashboard after login
├── styles.css                       # Shared CSS styling
├── script.js                        # Shared JavaScript functions
├── sun_son_solar_setup.sql          # Database schema (SQLite)
└── README.md                        # This file
```

---

##  Getting Started

### Step 1: Set Up Database in VS Code

1. **Install SQLite Extension**
   - Open VS Code
   - Go to Extensions (Ctrl+Shift+X)
   - Search for "SQLite" 
   - Install "SQLite" by alexcvzz

2. **Create Database File**
   - Create a new file: `sun_son_solar.db` in your project folder
   - Right-click the `.db` file → "Open Database"
   - The database will appear in the SQLite Explorer panel

3. **Run Setup Script**
   - Open the database in SQLite Explorer
   - Copy all content from `sun_son_solar_setup.sql`
   - Right-click the database → "Run Query"
   - Paste and execute the SQL script

4. **Verify Tables**
   - Expand the database in SQLite Explorer
   - You should see these tables:
     - users
     - customers
     - employees
     - products
     - services
     - technician_checkin

---

##  Default Login Credentials

The system comes with 2 pre-configured admin accounts:

| Username | Password | Role |
|----------|----------|------|
| Kitty Kat16 | K@tSunShine16 | Admin |
<<<<<<< HEAD
| admin | admin 123 | Admin |
=======
| admin | admin 123 | Admin |


>>>>>>> 16ab10e5a84020dfb520efb9a9c0562f4987ea67
