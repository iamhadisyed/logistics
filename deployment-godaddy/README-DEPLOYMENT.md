# 🚀 GoDaddy Deployment Guide (No Composer/Node.js)

## ✅ Current Status

### Backend (Laravel) - ✅ READY
- ✅ All files including `vendor/` folder
- ✅ Production `.env` template created
- ✅ Database migrations ready
- ✅ `.htaccess` configured

### Frontend (Next.js) - ⚠️ NEEDS ATTENTION
- ⚠️ Frontend folder is currently **EMPTY**
- ⚠️ **Important**: GoDaddy doesn't support Node.js runtime
- ⚠️ Need to convert to static export OR deploy separately

## 📋 Frontend Options for GoDaddy

### **Option 1: Static Export (Recommended for GoDaddy)**
Since GoDaddy doesn't have Node.js, you need to:

1. **Configure Next.js for Static Export:**
   - Update `next.config.ts` to add `output: 'export'`
   - Build static files: `npm run build`
   - Upload the `out/` folder to GoDaddy

2. **Steps:**
   ```bash
   # In dakia_app01 folder
   # Update next.config.ts (add output: 'export')
   npm run build
   # Upload the 'out' folder contents to GoDaddy
   ```

### **Option 2: Deploy Frontend Separately (Recommended)**
- Deploy Next.js to **Vercel** (free) or **Netlify**
- Connect to your Laravel API on GoDaddy
- Update API endpoints in frontend config

### **Option 3: Use GoDaddy Web Hosting Plus** 
- If you upgrade to hosting that supports Node.js
- Then you can run Next.js normally

## 📦 What's Included in This Package

### Backend (`backend/` folder)
✅ Complete Laravel application
✅ All dependencies in `vendor/` folder (no Composer needed)
✅ Database migrations
✅ Seeders
✅ API routes configured
✅ Production `.env` template

### Frontend (`frontend/` folder)
⚠️ Currently empty - needs static export or separate deployment

## 🔧 Deployment Steps

### Step 1: Upload Backend
1. Compress `backend/` folder
2. Upload to GoDaddy via cPanel File Manager
3. Extract to your domain root or subdomain

### Step 2: Configure Backend
1. Update `.env` file with:
   - Database credentials
   - App URL
   - Generate APP_KEY (use: https://laravel-key-generator.com)
2. Set permissions: `755` for folders, `644` for files

### Step 3: Setup Database
1. Create MySQL database in GoDaddy cPanel
2. Import SQL from `database-setup.sql`
3. Import sample data from `sample-data.sql`

### Step 4: Test Backend API
```
https://yourdomain.com/api/logistics/countries
https://yourdomain.com/api/logistics/consignments
```

### Step 5: Deploy Frontend
Choose one:
- **Option A**: Export as static and upload to GoDaddy
- **Option B**: Deploy to Vercel/Netlify and connect to GoDaddy API

## 📝 About Your ERP Project

You mentioned you have another project called "erp" in the same GoDaddy account. 

**I can help you:**
- Integrate ERP with this logistics system
- Create shared APIs between projects
- Configure routing for multiple projects
- Set up shared database or separate databases

**To help with ERP, I would need:**
- Access to ERP project files, OR
- Information about what ERP system it uses (Laravel? Other?)
- Your goals for integration

## 🎯 Next Steps

1. **Immediate**: Upload backend to GoDaddy and test API
2. **Frontend**: Choose deployment option (static export OR Vercel)
3. **ERP Integration**: Share ERP details if you want integration help

## ✅ Checklist Before Uploading

- [ ] Backend folder ready (vendor included)
- [ ] .env file configured with database credentials
- [ ] Database created on GoDaddy
- [ ] Frontend exported as static OR deployed separately
- [ ] API endpoints tested
- [ ] SSL certificate installed (recommended)

---

**Need help with any step? Let me know!**


