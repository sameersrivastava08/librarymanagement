# Simple Library Management System (PHP + MySQL)

Setup:
1. Create a MySQL database, e.g. `library_db`.
2. Import database schema:
   mysql -u root -p library_db < database.sql
3. Update DB credentials in config.php.
4. Place files in your web server (e.g., /var/www/html/library-management-system).
5. Access via browser: http://localhost/library-management-system/index.php

Default accounts created by database.sql:
- admin / admin123  (role = admin)
- user / user123    (role = user)

Notes:
- Admin can access Maintenance (Add/Update Book, Add/Update Member), Reports, Transactions.
- User has access to Reports and Transactions only.
- Passwords are hashed using password_hash.
- UI kept simple using normal CSS (styles.css).