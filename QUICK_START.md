# Aktaš System - Quick Start Guide

## System Overview
**Aktaš System** is a comprehensive accounting and inventory management solution for **Hamid Limited Company**, built with Laravel 12 and MySQL. The system provides double-entry bookkeeping, multi-warehouse management, and bilingual Arabic/English interface.

---

## 🚀 Starting the System

### Prerequisites
- XAMPP installed (PHP 8.2.12, MySQL 5.7+)
- Composer 2.9.7 installed locally at `D:\accounting system web app\composer.phar`
- Project at: `D:\accounting system web app\aktas-system`

### Step 1: Start MySQL
```powershell
Start-Process "C:\xampp\mysql\bin\mysqld.exe"
Start-Sleep -Seconds 3
```

### Step 2: Start Laravel Development Server
```powershell
cd "D:\accounting system web app\aktas-system"
C:\xampp\php\php.exe artisan serve --host=0.0.0.0 --port=8000
```
✅ Server will run at `http://localhost:8000`

### Step 3: Access the Dashboard
- **Phase 1 (Products & Inventory)**: `http://localhost:8000/dashboard.html`
- **Phase 2 (Accounting & Warehouse)**: `http://localhost:8000/accounting-dashboard.html`

---

## 📊 Dashboard Overview

### Phase 1 - Dashboard (dashboard.html)
**Features**: Product management, category management, inventory tracking

**Main Sections**:
1. **Dashboard** - Product stats, low stock alerts, inventory value
2. **Products** - CRUD operations for products, search, filtering
3. **Categories** - Manage product categories
4. **Inventory** - Track inventory movements and history

**API Endpoints Used**:
- `/api/v1/products` - Product management
- `/api/v1/categories` - Category management
- `/api/v1/inventory` - Inventory operations

---

### Phase 2 - Accounting Dashboard (accounting-dashboard.html)
**Features**: Ledger accounting, journal entries, warehouse management, transfers

**Main Sections**:
1. **Dashboard** - System KPIs (active accounts, journal entries, warehouses, transfers)
2. **Chart** - Browse chart of accounts, view hierarchical structure
3. **Journal** - Create/post/reverse journal entries with double-entry validation
4. **Warehouse** - Create warehouses, manage inventory
5. **Transfers** - Initiate and track warehouse transfers

**API Endpoints Used**:
- `/api/v1/accounting/chart-of-accounts` - Chart management
- `/api/v1/accounting/journal-entries` - Journal operations
- `/api/v1/accounting/trial-balance` - Reports
- `/api/v1/warehouses` - Warehouse operations
- `/api/v1/warehouses/transfer-*` - Transfer workflows

---

## 🔑 Login Credentials

### Admin Account
- **Email**: `admin@aktas-system.com`
- **Password**: `password`
- **Role**: Admin (Full system access)

### Available Roles
1. **Admin** - Full system access, all permissions
2. **Branch Manager** - Most operations except system settings
3. **Accountant** - Accounting operations, reporting, view-only inventory
4. **Cashier** - Limited inventory operations, sales
5. **View-Only** - Read-only access to all data

---

## 📚 Key Concepts

### Phase 1: Basic Accounting & Inventory
- **Products**: SKU, barcode, pricing, stock levels
- **Categories**: Product grouping with bilingual names
- **Inventory Movements**: Track all stock changes (incoming, outgoing, adjustments, transfers)
- **Audit Logging**: Complete history of all changes

### Phase 2: Advanced Ledger & Warehouse
- **Chart of Accounts**: Hierarchical structure (Assets, Liabilities, Equity, Revenue, Expenses)
- **Journal Entries**: Double-entry transactions (debit must equal credit)
- **Trial Balance**: Verification report that all entries are balanced
- **General Ledger**: Complete account transaction history with running balance
- **Warehouses**: Multiple storage locations with separate inventory tracking
- **Warehouse Transfers**: Movement of products between warehouses with status workflow

---

## 🛠️ Common Operations

### 1. Creating a Journal Entry

**Steps**:
1. Open Accounting Dashboard → Journal Tab
2. Click "+ إضافة قيد" (Add Entry)
3. Fill in:
   - **Date**: Transaction date
   - **Description**: Transaction details (Arabic + English)
   - **Items**: 
     - Select account
     - Enter debit amount (for left-side accounts)
     - Enter credit amount (for right-side accounts)
     - Must have at least 2 items with equal debit/credit
4. Click "Save" (entry starts in **draft** status)
5. Review entry
6. Click "Post Entry" to finalize (validates debit=credit)

**Via API**:
```bash
curl -X POST http://localhost:8000/api/v1/accounting/journal-entries \
  -H "Content-Type: application/json" \
  -d '{
    "date": "2024-04-23",
    "description": "Cash sale",
    "items": [
      {"account_id": 2, "debit": 1000, "credit": 0},
      {"account_id": 13, "debit": 0, "credit": 1000}
    ]
  }'
```

---

### 2. Creating a Warehouse Transfer

**Steps**:
1. Open Accounting Dashboard → Transfers Tab
2. Click "+ إضافة نقل" (Add Transfer)
3. Fill in:
   - **Product**: Select product to transfer
   - **From Warehouse**: Source warehouse
   - **To Warehouse**: Destination warehouse
   - **Quantity**: Amount to transfer
   - **Date**: Transfer date
4. Click "Create Transfer"
5. Transfer starts in **pending** status (inventory reserved)
6. Update status: Pending → In Transit → Received

**Via API**:
```bash
curl -X POST http://localhost:8000/api/v1/warehouses/transfer \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "from_warehouse_id": 1,
    "to_warehouse_id": 2,
    "quantity": 100,
    "transfer_date": "2024-04-23"
  }'
```

---

### 3. Viewing Reports

**Trial Balance**:
- Shows all accounts with debit/credit columns
- Verifies total debits = total credits
- Accessible via: Journal Tab → "Trial Balance" button

**General Ledger**:
- Complete transaction history for specific account
- Shows running balance for each transaction
- Accessible via: Chart Tab → Select account → "View Ledger"

---

## 📡 API Quick Reference

### Authentication
Currently all endpoints are public. Add auth middleware for production:
```php
Route::middleware('auth:sanctum')->group(function() {
    // Protected routes
});
```

### Common Patterns

#### GET - Retrieve List (Paginated)
```bash
GET /api/v1/accounting/journal-entries?per_page=20&status=posted&start_date=2024-01-01
```

#### GET - Single Resource
```bash
GET /api/v1/accounting/chart-of-accounts/1
```

#### POST - Create Resource
```bash
POST /api/v1/accounting/chart-of-accounts
Content-Type: application/json

{
  "account_code": "1040",
  "account_name": "Equipment",
  "account_name_ar": "المعدات",
  "account_type": "asset"
}
```

#### PUT - Update Resource
```bash
PUT /api/v1/accounting/chart-of-accounts/1
Content-Type: application/json

{
  "is_active": false
}
```

#### POST - Action Endpoints
```bash
# Post journal entry (debit=credit validation)
POST /api/v1/accounting/journal-entries/1/post

# Reverse journal entry
POST /api/v1/accounting/journal-entries/1/reverse

# Complete warehouse transfer
POST /api/v1/warehouses/transfers/1/complete

# Reject warehouse transfer
POST /api/v1/warehouses/transfers/1/reject
```

---

## 📋 Database & File Structure

### Key Files
```
aktas-system/
├── app/Models/                    # Eloquent Models
│   ├── Product.php               # Phase 1
│   ├── Category.php              # Phase 1
│   ├── InventoryMovement.php     # Phase 1
│   ├── ChartOfAccount.php        # Phase 2 ✨
│   ├── JournalEntry.php          # Phase 2 ✨
│   ├── Warehouse.php             # Phase 2 ✨
│   └── WarehouseTransfer.php     # Phase 2 ✨
├── app/Http/Controllers/Api/      # API Controllers
│   ├── ProductController.php      # Phase 1
│   ├── ChartOfAccountController   # Phase 2 ✨
│   ├── JournalEntryController     # Phase 2 ✨
│   └── WarehouseController        # Phase 2 ✨
├── database/migrations/           # Database Schema
│   ├── 2024_04_23_000001-006      # Phase 1 tables
│   └── 2024_04_23_000007-009      # Phase 2 tables ✨
├── database/seeders/
│   ├── RolePermissionSeeder.php   # Phase 1
│   └── ChartOfAccountsSeeder.php  # Phase 2 ✨
├── routes/api.php                 # API Routes (updated for Phase 2)
└── public/
    ├── dashboard.html             # Phase 1 Frontend
    └── accounting-dashboard.html  # Phase 2 Frontend ✨
```

### Database Tables
**Phase 1**: users, roles, permissions, products, categories, inventory_movements
**Phase 2**: chart_of_accounts, journal_entries, journal_entry_items, warehouses, warehouse_inventory, warehouse_transfers

---

## 🐛 Troubleshooting

### Issue: "Connection refused" error
**Solution**: Ensure MySQL is running
```powershell
Start-Process "C:\xampp\mysql\bin\mysqld.exe"
Start-Sleep -Seconds 3
```

### Issue: "Class not found" error
**Solution**: Regenerate Composer autoload
```powershell
cd "D:\accounting system web app\aktas-system"
C:\xampp\php\php.exe -d memory_limit=-1 ..\composer.phar dump-autoload
```

### Issue: "Table doesn't exist" error
**Solution**: Run migrations
```powershell
C:\xampp\php\php.exe artisan migrate
```

### Issue: Frontend not loading
**Ensure**: 
1. Development server running on port 8000
2. Browser cache cleared
3. Check browser console for AJAX errors

---

## 📝 Development Notes

### Bilingual Support
- All user-facing strings in Arabic (اللغة العربية) with English fallbacks
- Database fields with `_ar` suffix for Arabic translations
- RTL (right-to-left) layout via Bootstrap 5 RTL CSS

### Accounting Logic
- **Assets & Expenses**: Increase with debit (left)
- **Liabilities, Equity & Revenue**: Increase with credit (right)
- **Balance Calculation**: Automatic based on account type
- **Entry Validation**: Cannot post if debit ≠ credit

### Warehouse Operations
- **Pending**: Transfer initiated, inventory reserved
- **In Transit**: Transfer in progress
- **Received**: Transfer completed, inventory moved
- **Rejected**: Transfer canceled, inventory released

---

## 🔐 Security Considerations

### For Production:
1. **Add Authentication**: Use Laravel Sanctum tokens
2. **Add Authorization**: Implement role-based permissions
3. **Validate Input**: Add comprehensive request validation
4. **Enable HTTPS**: Use SSL certificates
5. **Rate Limiting**: Prevent API abuse
6. **Audit Trail**: Log all accounting transactions
7. **Database Backups**: Regular automatic backups for financial data

### Current Status:
- ⚠️ All endpoints currently public (no auth required)
- ✅ RBAC framework ready (from Phase 1)
- ✅ Audit logging in place
- ⚠️ No input validation (add in Phase 3)

---

## 📞 Support

### Getting Help
1. Check PHASE_2_README.md for detailed documentation
2. Review conversation summary for implementation details
3. Check Laravel documentation: https://laravel.com
4. MySQL documentation: https://dev.mysql.com/doc

### Useful Commands
```powershell
# View all migrations
artisan migrate:status

# Create new migration
artisan make:migration create_table_name

# Create new model
artisan make:model ModelName

# Create new controller
artisan make:controller Api/ControllerName --api

# Clear cache
artisan cache:clear
artisan config:clear
artisan route:cache

# Database operations
artisan tinker                    # Interactive shell
artisan db:seed --class=Seeder   # Run specific seeder
artisan migrate:rollback         # Undo migrations
```

---

## ✅ Quick Verification Checklist

Before considering the system ready:

- [ ] MySQL server running
- [ ] Development server running on port 8000
- [ ] Dashboard.html loads without errors
- [ ] Accounting-dashboard.html loads without errors
- [ ] Can view chart of accounts (15+ accounts visible)
- [ ] Can create and post a journal entry
- [ ] Can create warehouses
- [ ] Can initiate a warehouse transfer
- [ ] Trial balance report generates
- [ ] All text appears in Arabic with English option
- [ ] API endpoints respond with JSON

---

## 📅 Phase 3 Roadmap

Planned for next phase:
1. **Employee Management**: Employee profiles, roles, commission tracking
2. **Commission System**: Automatic commission calculations based on sales
3. **Advanced Reporting**: Drill-down capability, custom reports
4. **Authentication**: Login system with role-based access
5. **Validation**: Comprehensive input validation
6. **Error Handling**: Proper error responses and logging
7. **Testing**: Unit and integration tests
8. **Deployment**: Production-ready configuration

---

**Last Updated**: April 23, 2024  
**System**: Aktaš System (نظام أكتاش)  
**Version**: Phase 2 - Complete & Operational
