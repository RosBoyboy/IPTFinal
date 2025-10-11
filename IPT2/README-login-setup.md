Setup notes for the login feature

What I changed:
- Added `app/Http/Controllers/Auth/LoginController.php` with `login` and `logout` methods.
- Registered `/api/login` and `/api/logout` routes in `routes/api.php`.
- Updated `app/Models/User.php` to include `username` in `$fillable`.
- Added migration `database/migrations/2025_10_10_000000_add_username_to_users_table.php` to add a unique `username` column.
- Created frontend components in `resources/js/components`: `Login.js`, `Dashboard.js`, `routing.js`, `login.css`.

Front-end notes (IPT2):
- Frontend Login uses `axios.get('/sanctum/csrf-cookie')` to initialize CSRF cookies, then posts to `/api/login`.
- Ensure axios sends cookies by default. In your app bootstrap (e.g., `resources/js/bootstrap.js`), set:

  import axios from 'axios';
  axios.defaults.withCredentials = true;

Back-end notes:
- The controller compares the session token to `request->_token` (keeps CSRF check server-side). If you prefer using Laravel's VerifyCsrfToken middleware for the API routes, move the routes to `routes/web.php` or configure middleware accordingly.

Run steps (Windows PowerShell):

1) Install dependencies (if not already):

```powershell
cd c:\3rd-FistSemester\IPT\IPT_dashboard\IPT2
composer install
npm install
```

2) Run migrations:

```powershell
php artisan migrate
```

3) Build assets (dev):

```powershell
npm run dev
```

4) Start dev server:

```powershell
php artisan serve
```

Serving images:
- Put your screenshots into `public/images/screenshots/` and rename them to `151843.png`, `152728.png`, `153006.png`, `201231.png` or update `Dashboard.js` image paths accordingly.

Testing login:
- Create a user in DB with a `username` field populated and a hashed password (bcrypt). Example via tinker:

```powershell
php artisan tinker
>>> \App\Models\User::create(['name' => 'Test', 'username' => 'testuser', 'email' => 'test@example.com', 'password' => bcrypt('secret')]);
```

Security notes:
- Using session token from the route body is a simple check; a more standard approach is to rely on Laravel's CSRF middleware for web routes and Sanctum for SPA authentication. If you want, I can convert the API endpoints to use Sanctum tokens or move login to `web.php` and use standard CSRF middleware.

If you'd like, I can also:
- Wire up the Login component into your app entry (e.g., `resources/js/app.js`) so routing mounts automatically.
- Create a view that mounts React and serves the SPA.
- Move CSRF handling to the middleware pipeline for stronger standard protection.
