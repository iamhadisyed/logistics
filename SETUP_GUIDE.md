# Daakia Dual-Project Setup Guide

## 🚀 Quick Start

### 1. Environment Setup

#### Frontend (Next.js) - `dakia_app01/.env.local`
```bash
# API Configuration
NEXT_PUBLIC_API_URL=http://localhost:8000
API_URL=http://localhost:8000

# NextAuth Configuration
NEXTAUTH_URL=http://localhost:3000
NEXTAUTH_SECRET=your-secret-key-here

# Database Configuration
DATABASE_URL="file:./dev.db"

# App Configuration
NEXT_PUBLIC_APP_URL=http://localhost:3000
```

#### Backend (Laravel) - `dakia_backend01/.env`
```bash
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:your-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### 2. Installation Steps

#### Backend Setup
```bash
cd dakia_backend01

# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Create database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Create a test user
php artisan tinker
# In tinker: User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')])

# Start server
php artisan serve
```

#### Frontend Setup
```bash
cd dakia_app01

# Install dependencies
npm install

# Generate Prisma client
npx prisma generate

# Run migrations
npm run migrate

# Build icons
npm run build:icons

# Start development server
npm run dev
```

### 3. API Integration

The frontend is already configured to use API calls. To enable them:

1. **Uncomment API calls** in the page files (remove the `/* */` comments)
2. **Remove server action imports** as mentioned in the comments
3. **Update environment variables** as shown above

### 4. Authentication Flow

#### Laravel Sanctum + NextAuth.js Integration

1. **Login Flow:**
   - Frontend sends credentials to `/api/login`
   - Laravel validates and returns user + token
   - NextAuth.js stores the session

2. **Protected Routes:**
   - Frontend includes token in Authorization header
   - Laravel validates token via Sanctum middleware

### 5. Available API Endpoints

#### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout (protected)

#### User Management
- `GET /api/apps/user-list` - Get all users
- `GET /api/apps/permissions` - Get permissions
- `GET /api/apps/roles` - Get roles

#### Ecommerce
- `GET /api/apps/ecommerce` - Get ecommerce data

#### Invoices
- `GET /api/apps/invoice` - Get invoice data

#### Academy
- `GET /api/apps/academy` - Get academy data

#### Pages
- `GET /api/pages/faq` - Get FAQ data
- `GET /api/pages/pricing` - Get pricing data
- `GET /api/pages/profile` - Get profile data
- `GET /api/pages/widget-examples` - Get statistics data

### 6. Development Workflow

#### Using the Batch Script
```bash
# Run the provided batch script
start-projects.bat
```

#### Manual Start
```bash
# Terminal 1 - Backend
cd dakia_backend01
php artisan serve

# Terminal 2 - Frontend
cd dakia_app01
npm run dev
```

### 7. Database Considerations

#### Option 1: Separate Databases (Current)
- **Frontend**: SQLite via Prisma
- **Backend**: SQLite via Laravel
- **Pros**: Independent development
- **Cons**: Data synchronization issues

#### Option 2: Unified Database (Recommended)
- **Both**: Use Laravel's database
- **Frontend**: Remove Prisma, use Laravel API
- **Pros**: Single source of truth
- **Cons**: More API calls

### 8. Troubleshooting

#### Common Issues

1. **CORS Errors:**
   - Add CORS middleware to Laravel
   - Configure `SANCTUM_STATEFUL_DOMAINS`

2. **Authentication Issues:**
   - Check NextAuth.js configuration
   - Verify Laravel Sanctum setup

3. **API Connection:**
   - Ensure both servers are running
   - Check environment variables
   - Verify API endpoints

#### Useful Commands

```bash
# Laravel
php artisan route:list
php artisan migrate:fresh
php artisan db:seed

# Next.js
npm run build
npm run lint
npx prisma studio
```

### 9. Production Deployment

#### Environment Variables
- Update URLs to production domains
- Use strong secrets for NextAuth
- Configure proper database connections

#### Security
- Enable HTTPS
- Configure CORS properly
- Use environment-specific configurations

## 🎯 Next Steps

1. **Test the API integration** by uncommenting API calls in frontend
2. **Add more endpoints** as needed
3. **Implement proper error handling**
4. **Add data validation** on both sides
5. **Set up proper authentication flow**

## 📚 Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [NextAuth.js Documentation](https://next-auth.js.org/)
- [Prisma Documentation](https://www.prisma.io/docs/)
- [Material-UI Documentation](https://mui.com/) 