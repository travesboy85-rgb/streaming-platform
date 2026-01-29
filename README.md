# Streaming Platform Backend System

A secure and scalable backend system for a streaming platform built with **Laravel 12**, featuring user management, content management, and streaming-related APIs.

## 🎯 Project Overview
This backend system provides core services for a streaming platform including user authentication, video content management, subscription plans, and API endpoints for client applications.

## 🚀 Features

### ✅ User Management
- User registration & authentication
- Role-based permissions (Admin, User, Creator)
- Secure token-based authentication (Laravel Sanctum)
- User profile management

### ✅ Content Management
- Video CRUD operations (Create, Read, Update, Delete)
- Category organization
- Premium content flagging
- Views tracking & analytics
- Thumbnail & metadata support

### ✅ Streaming APIs
- RESTful API endpoints
- Video streaming endpoints
- Subscription plans management
- Paginated content delivery

### ✅ Security
- Input validation & sanitization
- Protected routes with middleware
- SQL injection prevention
- CORS configuration

## 🛠️ Technology Stack
- **Framework:** Laravel 12
- **PHP:** 8.2.12
- **Database:** SQLite (development)
- **Authentication:** Laravel Sanctum
- **Permissions:** Spatie Laravel Permission
- **API:** RESTful JSON API

## 🔧 Installation

### Prerequisites
- PHP 8.2+
- Composer

### Setup Instructions
1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd streaming-platform
2. **Intall dependencies**
   composer install
3. **Configure environment**
   cp .env.example .env
php artisan key:generate
4. **Configure Database**
   DB_CONNECTION=sqlite
5. **Run migrations & seeders**
   php artisan migrate
   php artisan db:seed
6. **Start development server**
   php artisan serve
7.  **Acess API**
   Base URL: http://localhost:8000/api/v1/
   Test endpoint: http://localhost:8000/api/v1/test
8. **Database Schema**
   users                # User accounts & authentication
videos               # Video content with metadata
categories           # Content categorization
subscription_plans   # Subscription tiers
watch_histories      # User viewing history (schema ready)
personal_access_tokens # API authentication
permissions/roles    # Role-based access control
9. **Authentication**
All protected endpoints require Bearer token in Authorization header.
 **Get Authentication Token**
 curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@streaming.com","password":"password123"}'
 **Use Token**
  curl -X GET http://localhost:8000/api/v1/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
 **Sample User (for testing)** 
    Email	            Password	Role	       Purpose
admin@streaming.com	   password123	Admin	     Full system access
user@streaming.com	   password123	User	     Regular viewer
creator@streaming.com  password123	Creator	     Content uploader
 **API Documentation**
 Base URL
 http://localhost:8000/api/v1/
 **Public Endpoints**
 GET /test - API status check

POST /register - User registration

POST /login - User login

GET /categories - List all categories

GET /categories/{id} - Get category with videos

GET /videos - List videos (paginated)

GET /videos/{id} - Get video details

GET /plans - List subscription plans
 **Protected Endpoints (Require Auth)**
 GET /user - Current user profile

POST /logout - Logout (invalidate token)

POST /videos - Create new video

PUT /videos/{id} - Update video

DELETE /videos/{id} - Delete video

  **Testing the API**
  Using PowerShell (Windows)
  # Test API status
Invoke-RestMethod -Method GET -Uri http://localhost:8000/api/v1/test

# Login
$response = Invoke-RestMethod -Method POST -Uri http://localhost:8000/api/v1/login -ContentType "application/json" -Body '{"email":"user@streaming.com","password":"password123"}'
$token = $response.token

# Access protected route
Invoke-RestMethod -Method GET -Uri http://localhost:8000/api/v1/user -Headers @{"Authorization" = "Bearer $token"}

 **Using curl**
 # Test registration
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

**Project Structure** 
streaming-platform/
├── app/
│   ├── Http/Controllers/API/    # API Controllers
│   ├── Models/                  # Database Models
│   └── Providers/
├── database/
│   ├── migrations/              # Database schema
│   └── seeders/                 # Sample data
├── routes/
│   └── api.php                  # API routes
└── tests/                       # Test cases

 **Future Enhancements**
Watch history tracking implementation

User subscription management

Payment integration

Video recommendation engine

Advanced search filters

**Development Timeline**
Project Start: January 5, 2026

Project Due: January 13, 2026

Framework: Laravel 12

Status: Complete and ready for submission

 **License**
This project is for educational purposes as part of a backend development assignment.

 **Acknowledgments**
Laravel Framework

Laravel Sanctum for API authentication

Spatie for permission management