# 🚀 Phase 3 Quick Start Guide
## Employee Management & Commission System

**Target Audience**: Developers, QA, System Administrators  
**Time to Setup**: 5 minutes  
**Last Updated**: April 23, 2026

---

## ⚡ 30-Second Overview

Phase 3 adds employee management, commission tracking, and advanced reporting to your Aktaš accounting system. Everything is bilingual (Arabic/English), fully integrated with Phases 1-2, and ready to use.

---

## 🎯 Step 1: System Is Already Running

Your Phase 3 system is already active and seeded with 10 employees. Just access the dashboard!

**Phase 1 Dashboard**: http://localhost:8000/dashboard.html  
**Phase 2 Dashboard**: http://localhost:8000/accounting-dashboard.html  
**Phase 3 Dashboard**: http://localhost:8000/employee-dashboard.html ← **NEW**

---

## 📊 Step 2: Access Employee Dashboard

1. Open browser and go to: **http://localhost:8000/employee-dashboard.html**
2. You'll see 5 main tabs:
   - **Dashboard**: KPIs and summary
   - **الموظفون (Employees)**: Employee list and management
   - **العمولات (Commissions)**: Commission tracking
   - **المبيعات (Sales)**: Sales recording and tracking
   - **التقارير (Reports)**: Generate financial reports

---

## 🎓 Step 3: Understand Key Concepts

### Employee Commission Structure
```
Base Salary (الراتب الأساسي) + Commission (العمولة) + Bonus - Deductions = Net Pay

Example:
- Ahmed Hassan: 8,000 SAR base + 5% commission = Variable total
- If sales = 100,000 SAR → Commission = 5,000 SAR → Total = 13,000 SAR
```

### Commission Types
1. **Percentage**: 3-5% of total sales (most employees)
2. **Fixed**: Amount per transaction (rarely used)

### Sample Employees Already Seeded
| Code | Name | Position | Commission | Department |
|------|------|----------|-----------|-----------|
| EMP-001 | Ahmed Hassan | Sales Manager | 5% | Sales |
| EMP-002 | Fatima Al-Rashid | Sales Executive | 3.5% | Sales |
| EMP-003 | Mohammed Ibrahim | Inventory Manager | 2% | Inventory |
| EMP-004 | Noor Al-Dosari | Junior Sales | 4% | Sales |
| EMP-005 | Sarah Al-Qahtani | Chief Accountant | None | Accounting |

---

## 🔄 Step 4: Common Tasks

### Task 1: Add New Employee
1. Go to **Employees** tab
2. Click **+ إضافة موظف**
3. Fill in form:
   - Name (English & Arabic)
   - Email
   - Position
   - Department
   - Hire Date
   - Base Salary
   - Commission Rate
4. Click **Save**

### Task 2: Record Sales
1. Go to **المبيعات (Sales)** tab
2. Click **+ تسجيل مبيعة**
3. Select:
   - Employee
   - Product
   - Quantity
   - Unit Price
   - Date
4. Click **Save**

### Task 3: Calculate Commissions
1. Go to **العمولات (Commissions)** tab
2. Select:
   - Employee
   - Month
   - Year
3. Click **تطبيق (Filter)**
4. System shows commission earned

### Task 4: Generate Payroll Report
1. Go to **التقارير (Reports)** tab
2. Under "Monthly Payroll":
   - Select month and year
   - Click **إنشاء التقرير**
3. See:
   - Base Salary + Commission - Deductions
   - Total net pay per employee
   - Department totals

---

## 📊 Step 5: API Endpoints Reference

### List Employees
```bash
GET http://localhost:8000/api/v1/employees?per_page=10
```

### Record Employee Sale
```bash
POST http://localhost:8000/api/v1/employees/1/sales
{
  "product_id": 1,
  "quantity": 100,
  "unit_price": 500,
  "sale_date": "2026-04-20"
}
```

### Calculate Commission
```bash
POST http://localhost:8000/api/v1/employees/1/commissions/calculate
{
  "month": 4,
  "year": 2026
}
```

### Generate Payroll Report
```bash
GET http://localhost:8000/api/v1/employees/reports/payroll?month=4&year=2026
```

### Financial Summary Report
```bash
GET http://localhost:8000/api/v1/reports/financial-summary?start_date=2026-01-01&end_date=2026-12-31
```

---

## 🧪 Step 6: Quick Test Scenario

**Objective**: Create sales and verify commission calculation

### Test Steps:
1. **Record 3 sales by Ahmed Hassan**
   - Sale 1: 50 units @ 1,000 SAR = 50,000 SAR
   - Sale 2: 30 units @ 1,000 SAR = 30,000 SAR
   - Sale 3: 20 units @ 1,000 SAR = 20,000 SAR
   - **Total**: 100,000 SAR

2. **Calculate Commission**
   - Ahmed's rate: 5%
   - Expected: 5,000 SAR
   - Formula: 100,000 × (5/100) = 5,000 ✓

3. **Generate Payroll Report**
   - Base: 8,000 SAR
   - Commission: 5,000 SAR
   - Net: 13,000 SAR

---

## 📁 Database Reference

### New Tables (Phase 3)
```
employees              ← Employee profiles
employee_commissions  ← Commission records
employee_deductions   ← Salary deductions
employee_sales        ← Individual sales records
```

### Query Examples

**List all active employees in sales**:
```sql
SELECT * FROM employees 
WHERE department = 'sales' AND is_active = true;
```

**Get commissions for April 2026**:
```sql
SELECT * FROM employee_commissions 
WHERE month = 4 AND year = 2026;
```

**Calculate total sales by employee**:
```sql
SELECT employee_id, SUM(total_amount) as total_sales 
FROM employee_sales 
WHERE MONTH(sale_date) = 4 AND YEAR(sale_date) = 2026
GROUP BY employee_id;
```

---

## 🔧 Troubleshooting

### Problem: Dashboard doesn't load
**Solution**: Ensure server running
```bash
# Check if Laravel running
ps aux | grep "artisan serve"

# If not, start it
php artisan serve --host=0.0.0.0 --port=8000
```

### Problem: Can't create employee
**Solution**: Check database connection
```bash
# Test database
php artisan tinker
> DB::connection()->getPDO();
> Employee::count()
```

### Problem: Sales not calculating commission
**Solution**: Verify sales recorded correctly
```bash
# In tinker
Employee::find(1)->sales()->count()
Employee::find(1)->calculateSalesForPeriod(4, 2026)
```

---

## 📚 Related Documentation

For complete information, see:
- **PHASE_3_README.md** - Complete technical reference
- **DOCUMENTATION_INDEX.md** - Navigation guide
- **QUICK_START.md** - General system startup

---

## 🎯 Next Actions

### For Development Team
1. ✅ Review Phase 3 code in `app/Models/` and `app/Http/Controllers/`
2. ✅ Run tests on all 20+ API endpoints
3. ✅ Verify commission calculations
4. ✅ Test dashboard UI functionality

### For QA Team
1. ✅ Test employee CRUD operations
2. ✅ Test commission calculations
3. ✅ Generate and verify reports
4. ✅ Check bilingual support

### For Database Team
1. ✅ Verify new tables created
2. ✅ Check indexes are created
3. ✅ Verify foreign keys working
4. ✅ Backup database

---

## 📊 Key Metrics at a Glance

| Metric | Value |
|--------|-------|
| Total Employees | 10 (pre-seeded) |
| API Endpoints | 20+ |
| Reports Available | 9 |
| Languages | 2 (Arabic + English) |
| Database Tables | 4 new |
| Migration Files | 1 |

---

## ⚠️ Important Notes

1. **Soft Deletes**: All Phase 3 records support soft delete (historical data preserved)
2. **Timestamps**: All records have created_at and updated_at
3. **Bilingual**: All fields have _ar suffix for Arabic
4. **Real-time**: Dashboard updates via AJAX without page refresh
5. **No Auth Required** (For testing): Add authentication in Phase 4

---

## 🎓 Understanding Commission Math

### Scenario A: Senior Manager (5% commission)
```
Sales this month: 200,000 SAR
Commission = 200,000 × 0.05 = 10,000 SAR
Base salary = 8,000 SAR
Total = 18,000 SAR
```

### Scenario B: Junior Staff (4% commission)
```
Sales this month: 75,000 SAR
Commission = 75,000 × 0.04 = 3,000 SAR
Base salary = 3,500 SAR
Total = 6,500 SAR
```

### Scenario C: Non-sales Staff (no commission)
```
Sales: N/A
Commission = 0 SAR
Base salary = 6,000 SAR (Accountant)
Total = 6,000 SAR
```

---

## 🔒 Security Reminder

**Phase 3 Current State**: No authentication required (development mode)

**Before Production**, add:
- JWT tokens via Sanctum
- Role-based access control
- Input validation
- Rate limiting
- HTTPS

See PHASE_3_README.md Security section for details.

---

## 💡 Pro Tips

1. **Use Date Filters**: Most reports accept start_date and end_date
2. **Drill Down**: Click on account codes in reports for transaction details
3. **Bilingual**: Switch between Arabic/English in dashboard settings
4. **Bulk Operations**: Upcoming API batch endpoints for multiple operations
5. **Export**: Reports can be exported to CSV (future feature)

---

**Quick Links**:
- 🏠 Phase 1: http://localhost:8000/dashboard.html
- 📊 Phase 2: http://localhost:8000/accounting-dashboard.html
- 👥 Phase 3: http://localhost:8000/employee-dashboard.html
- 📖 Docs: PHASE_3_README.md

**Status**: ✅ READY TO USE

Last Updated: April 23, 2026  
Version: 3.0.0
