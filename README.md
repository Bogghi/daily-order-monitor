# Iliad Order Management System

A full-stack web application for managing orders and products with user authentication and a modern Vue.js interface.

## 🏗️ Architecture

The application follows a microservices architecture with clear separation of concerns:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Vue.js SPA    │────│   Nginx Proxy   │────│   PHP-FPM API   │
│   (Frontend)    │    │   (Web Server)  │    │   (Backend)     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │                       │
                                │              ┌─────────────────┐
                                │              │   MariaDB       │
                                │              │   (Database)    │
                                │              └─────────────────┘
                                │
                        ┌─────────────────┐
                        │   Docker        │
                        │   (Container)   │
                        └─────────────────┘
```

### Components

- **Frontend**: Vue.js 3 SPA with Composition API
- **Backend**: PHP 8+ with Slim Framework
- **Database**: MariaDB with foreign key relationships
- **Web Server**: Nginx with PHP-FPM
- **Container**: Docker with docker-compose

## 🛠️ Tech Stack

### Frontend

- **Vue.js 3** - Progressive JavaScript framework
- **PrimeVue** - UI component library
- **Pinia** - State management
- **Vite** - Build tool and dev server
- **Vue Router** - Client-side routing
- **Iconify** - Icon system

### Backend

- **PHP 8+** - Server-side language
- **Slim Framework 4** - Micro framework for APIs
- **Composer** - Dependency manager
- **JWT** - Token-based authentication
- **SHA-256** - Password hashing

### Database

- **MariaDB** - Relational database
- **Foreign Keys** - Data integrity
- **Soft Deletes** - Data preservation

### Infrastructure

- **Docker** - Containerization
- **Nginx** - Web server and reverse proxy
- **PHP-FPM** - FastCGI Process Manager

## 📊 Database Schema

```sql
┌─────────────┐    ┌─────────────────┐    ┌─────────────┐
│   users     │    │   order_items   │    │  products   │
├─────────────┤    ├─────────────────┤    ├─────────────┤
│ user_id     │    │ order_item_id   │    │ product_id  │
│ username    │    │ order_id        │────│ name        │
│ password    │    │ product_id      │    │ price       │
│ created_at  │    │ quantity        │    │ deleted     │
└─────────────┘    └─────────────────┘    └─────────────┘
       │                    │
       │            ┌─────────────┐
       │            │   orders    │
       │            ├─────────────┤
       └────────────│ order_id    │
                    │ name        │
                    │ description │
                    │ order_date  │
                    │ value       │
                    │ deleted     │
                    └─────────────┘
```

## 🚀 Quick Setup

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd iliad-test
   ```

2. **Run the installation script**

   ```bash
   chmod +x install.sh
   ./install.sh
   ```

   This script will:

   - Install PHP dependencies with Composer
   - Set up environment configuration
   - Install Node.js dependencies
   - Build the frontend application
   - Start Docker containers
   - Initialize the database

3. **Access the application**
   - Open your browser and navigate to: http://localhost:9000/login

## 📖 Manual Setup

If you prefer to set up manually:

### Backend Setup

```bash
cd api
composer install
cp env-example.php env.php
# Edit env.php with your database credentials
```

### Frontend Setup

```bash
cd front-end
npm install
npm run build
```

### Docker Setup

```bash
cp docker-compose-template.yml docker-compose.yml
docker compose up -d --build
```

### Database Initialization

```bash
./init-db.sh
```

## 🎯 Usage Guide

### Authentication

1. **Registration**: Create a new account at `/register`
2. **Login**: Access the system at `/login`
3. **Auto-login**: JWT tokens are stored locally for seamless experience

### Product Management

- View all products in the system
- Add new products with name and price
- Delete products (soft delete)

### Order Management

#### Creating Orders

1. Click the "+" button in the orders table header
2. Fill in order name and description
3. Select products by clicking on them
4. Adjust quantities using the quantity controls
5. Review the total amount
6. Click "Salva ordine" to create

#### Editing Orders

1. Click on any order row in the table
2. Modify order details and products
3. Click "Aggiorna ordine" to save changes
4. Click "Elimina ordine" to delete (soft delete)

#### Filtering Orders

- **Global Search**: Use the search box to filter by any field
- **Date Filter**: Use the date picker to filter by specific dates
- **Clear Filters**: Clear the date picker to show all orders

### Features

#### Order Features

- ✅ Create new orders with multiple products
- ✅ Edit existing orders (name, description, products)
- ✅ Delete orders (soft delete - preserves data)
- ✅ Real-time total calculation
- ✅ Order items management with quantities

#### Search & Filter

- ✅ Global text search across all order fields
- ✅ Date-specific filtering with calendar picker
- ✅ Clear filters functionality

#### UI/UX Features

- ✅ Responsive design
- ✅ Loading states and error handling
- ✅ Modal-based editing interface
- ✅ Intuitive product selection
- ✅ Real-time price calculations

## 🔧 Development

### Local Development

```bash
# Frontend development server
cd front-end
npm run dev

# Backend is served through Docker containers
# Database is accessible at localhost:3306
```

### Building for Production

```bash
cd front-end
npm run build
```

## 🐳 Docker Services

The application runs on the following Docker services:

- **nginx**: Web server (port 9000 → 80)
- **php**: PHP-FPM backend
- **mariadb**: Database server (port 3306)

### Service Management

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs -f [service-name]

# Restart a service
docker compose restart [service-name]
```

## 🔒 Security Features

- **JWT Authentication**: Secure token-based authentication
- **Password Hashing**: SHA-256 encrypted passwords
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Protection**: Parameterized queries
- **Soft Deletes**: Data preservation for audit trails

## 📱 API Endpoints

### Authentication

- `POST /API/v1/login` - User login
- `POST /API/v1/register` - User registration

### Orders

- `GET /API/v1/orders` - List all orders (non-deleted)
- `POST /API/v1/orders/add` - Create new order
- `POST /API/v1/orders/{id}/update` - Update order
- `POST /API/v1/orders/{id}/delete` - Delete order (soft)

### Products

- `GET /API/v1/products` - List all products
- `POST /API/v1/products/add` - Create new product
- `POST /API/v1/products/{id}/delete` - Delete product

## 🎨 UI Components

The application uses PrimeVue components for a consistent and professional interface:

- **DataTable**: Advanced table with filtering and sorting
- **Calendar**: Date picker for filtering
- **Button**: Various button styles and states
- **InputText**: Form inputs with validation
- **BottomSheet**: Modal interface for order management

## 🚨 Troubleshooting

### Common Issues

**Database Connection Error**

```bash
# Ensure MariaDB container is running
docker compose ps

# Check database logs
docker compose logs mariadb

# Restart database service
docker compose restart mariadb
```
