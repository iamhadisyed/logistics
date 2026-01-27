# Migration Checklist for `c:\logistics`

The following actions are required to fully migrate the working environment to the new `c:\logistics` directory:

1.  **Update Start Script**:
    - [x] Edit `c:\logistics\start-dev-servers.bat` to point to the new paths (`c:\logistics\...`). *I will do this for you.*

2.  **Environment Variables**:
    - [x] Verified `dakia_app01/.env.local` (Relative paths used, looks good).
    - [x] Verified `dakia_backend01/.env` (Generic settings, looks good).

3.  **Dependencies & Caches** (Recommended steps for you to run):
    - [ ] Open a terminal in `c:\logistics\dakia_backend01` and run:
        ```bash
        php artisan config:clear
        php artisan cache:clear
        ```
    - [ ] If you encounter issues, delete `node_modules` in `dakia_app01` and run `npm install` again.
    - [ ] If you moved the folder while servers were running, ensure all `node` and `php` processes are killed before starting again.

4.  **IDE / Editor**:
    - [ ] If you have a VS Code workspace file, open it and update any folders to point to `c:\logistics`.
