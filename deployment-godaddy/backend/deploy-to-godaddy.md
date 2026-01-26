# 🚀 GoDaddy Deployment Guide for Logistics Management System

## 📋 Pre-Deployment Checklist

### 1. **Backend (Laravel) Preparation**

#### Files to Upload:
- All files from `dakia_backend01/` folder
- **EXCEPT**: `node_modules/`, `.git/`, `storage/logs/*.log`

#### Required GoDaddy Features:
- ✅ PHP 8.2+ support
- ✅ MySQL database
- ✅ Composer support (or upload vendor folder)
- ✅ SSL certificate
- ✅ Domain/subdomain

### 2. **Frontend (Next.js) Preparation**

#### Build for Production:
```bash
cd dakia_app01
npm run build
```

#### Files to Upload:
- `.next/` folder (after build)
- `public/` folder
- `package.json`
- `next.config.js`
- `node_modules/` (or install on server)

## 🗄️ Database Setup on GoDaddy

### 1. **Create MySQL Database**
1. Login to GoDaddy cPanel
2. Go to "MySQL Databases"
3. Create new database: `logistics_db`
4. Create database user with full privileges
5. Note down: hostname, database name, username, password

### 2. **Update Laravel Environment**
Create `.env` file on server:
```env
APP_NAME="Logistics Management"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=logistics_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Add other required configurations
```

## 📁 File Structure on GoDaddy

### **Option 1: Subdomain Setup (Recommended)**
```
yourdomain.com/
├── api/ (Laravel Backend)
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── routes/
│   ├── vendor/
│   └── .env
└── app/ (Next.js Frontend)
    ├── .next/
    ├── public/
    ├── package.json
    └── node_modules/
```

### **Option 2: Separate Domains**
- **Backend**: `api.yourdomain.com`
- **Frontend**: `app.yourdomain.com`

## 🔧 Deployment Steps

### **Step 1: Upload Laravel Backend**
1. Compress `dakia_backend01/` folder
2. Upload to GoDaddy via cPanel File Manager
3. Extract in `public_html/api/` or subdomain folder
4. Set proper permissions (755 for folders, 644 for files)

### **Step 2: Configure Laravel**
```bash
# On server via SSH or cPanel Terminal
cd /path/to/your/laravel/app
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Step 3: Database Migration**
```bash
php artisan migrate --force
php artisan db:seed --class=LogisticsSeeder
```

### **Step 4: Upload Next.js Frontend**
1. Build frontend: `npm run build`
2. Upload `.next/`, `public/`, `package.json` to frontend folder
3. Install dependencies: `npm install --production`

### **Step 5: Configure Web Server**

#### For Apache (.htaccess in Laravel public folder):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### For Nginx:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/laravel/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🧪 Testing Your Deployment

### **1. Backend API Testing**
```bash
# Test countries endpoint
curl https://yourdomain.com/api/logistics/countries

# Test consignments endpoint
curl https://yourdomain.com/api/logistics/consignments

# Test statistics
curl https://yourdomain.com/api/logistics/consignments/statistics
```

### **2. Frontend Testing**
1. Visit: `https://yourdomain.com`
2. Check if all pages load correctly
3. Test API integration
4. Verify CRUD operations work

### **3. Database Testing**
```sql
-- Check if tables exist
SHOW TABLES;

-- Check sample data
SELECT * FROM countries;
SELECT * FROM carriers;
SELECT * FROM consignments;
```

## 🔒 Security Considerations

### **1. Laravel Security**
- Set `APP_DEBUG=false` in production
- Use strong `APP_KEY`
- Enable HTTPS
- Set proper file permissions

### **2. Database Security**
- Use strong database passwords
- Limit database user privileges
- Enable SSL for database connections

### **3. Server Security**
- Keep PHP and server software updated
- Use security headers
- Enable firewall rules

## 📊 Performance Optimization

### **1. Laravel Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **2. Next.js Optimization**
- Enable compression
- Use CDN for static assets
- Optimize images
- Enable caching headers

## 🚨 Troubleshooting

### **Common Issues:**

1. **500 Internal Server Error**
   - Check file permissions
   - Verify .env configuration
   - Check error logs

2. **Database Connection Error**
   - Verify database credentials
   - Check if database exists
   - Test connection

3. **API Not Working**
   - Check CORS settings
   - Verify routes are cached
   - Check middleware

4. **Frontend Not Loading**
   - Check if build files exist
   - Verify Node.js version
   - Check package.json

## 📞 Support Commands

### **Useful Commands for Debugging:**
```bash
# Check Laravel status
php artisan about

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check routes
php artisan route:list
```

## 🎯 Go-Live Checklist

- [ ] Backend deployed and accessible
- [ ] Database migrated and seeded
- [ ] Frontend built and deployed
- [ ] SSL certificate installed
- [ ] API endpoints tested
- [ ] Frontend-backend integration tested
- [ ] Performance optimized
- [ ] Security measures in place
- [ ] Backup strategy implemented
- [ ] Monitoring setup

---

**🎉 Your logistics management system is now live on GoDaddy!**
