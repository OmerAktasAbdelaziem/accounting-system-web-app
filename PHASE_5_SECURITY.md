# Phase 5: Security & Authorization Documentation

## Overview

Phase 5 implements comprehensive security features for the Aktaš System:
1. **Role-Based Authorization (RBAC)** - Policy-based permission checking
2. **Input Validation** - FormRequest classes for all API endpoints
3. **Rate Limiting** - Prevent brute force attacks and API abuse
4. **Token Expiration** - Time-limited API tokens for enhanced security
5. **Security Headers** - HTTP security headers to prevent common attacks

---

## 1. Role-Based Authorization (RBAC)

### Architecture

Authorization is implemented using Laravel Policies with a role-permission model:

```
User → Role → Permissions
       ↓
Policy (Check Permissions) → Allow/Deny
```

### Base Policy Class

All authorization policies extend `BasePolicy` which provides:
- `isAdmin($user)`: Check if user is admin
- `can($user, $permission)`: Check specific permission
- `allow()`: Return authorized response
- `deny($message)`: Return unauthorized response
- `viewAny($user)`: Everyone can list (override in policy)
- `view($user, $model)`: Everyone can view (override in policy)

**File:** `app/Policies/BasePolicy.php`

### Authorization Policies

#### ProductPolicy
Manages authorization for product operations:

```php
// Create products: requires 'create-product' permission or admin
public function create(User $user): Response

// Edit products: requires 'edit-product' permission or admin
public function update(User $user, Product $product): Response

// Delete products: requires 'delete-product' permission or admin
public function delete(User $user, Product $product): Response

// Permanent deletion: admin only
public function forceDelete(User $user, Product $product): Response
```

#### EmployeePolicy
Manages authorization for employee operations:

```php
// Create employees: requires 'create-employee' permission
public function create(User $user): Response

// Edit employees: requires 'edit-employee' permission
public function update(User $user, Employee $employee): Response

// Delete/terminate employees: requires 'delete-employee' permission
public function delete(User $user, Employee $employee): Response

// Manage commissions: requires 'manage-commission' permission
public function manageCommission(User $user, Employee $employee): Response

// Approve commissions: requires 'approve-commission' permission
public function approveCommission(User $user, Employee $employee): Response

// View employee salary: requires 'view-salary' or owns record
public function viewSalary(User $user, Employee $employee): Response
```

#### AccountingPolicy
Manages authorization for accounting operations:

```php
// Create chart of accounts: requires 'manage-accounts' permission
public function createAccount(User $user): Response

// Create journal entries: requires 'create-journal' permission
public function createJournal(User $user): Response

// Post journal entries: requires 'post-journal' permission
public function postJournal(User $user, JournalEntry $entry): Response

// View financial reports: requires 'view-reports' permission
public function viewReports(User $user): Response
```

### Using Policies in Controllers

```php
// In controller method
public function store(StoreProductRequest $request)
{
    // Authorize action
    $this->authorize('create', Product::class);
    
    // Or use policy directly
    if ($request->user()->cannot('create', Product::class)) {
        return response()->json(['success' => false], 403);
    }
    
    // Proceed with creation...
}

// For specific model instance
public function update(UpdateProductRequest $request, Product $product)
{
    $this->authorize('update', $product);
    
    // Proceed with update...
}
```

### Registering Policies

Add to `app/Providers/AuthServiceProvider.php`:

```php
protected $policies = [
    Product::class => ProductPolicy::class,
    Employee::class => EmployeePolicy::class,
    JournalEntry::class => AccountingPolicy::class,
];
```

---

## 2. Input Validation (FormRequest)

### Overview

FormRequest classes centralize input validation with:
- Reusable validation rules
- Custom error messages
- Pre-validated data passed to controllers

### FormRequest Classes

#### StoreProductRequest
Validation for creating products:

```php
// File: app/Http/Requests/StoreProductRequest.php

Rules:
- name: required, unique, max 255
- category_id: required, exists in categories
- sku: required, unique, max 50
- unit_price: required, numeric, min 0.01
- reorder_level: required, integer, min 1

Usage in controller:
public function store(StoreProductRequest $request)
{
    $validated = $request->validated(); // Pre-validated data
    Product::create($validated);
}
```

#### UpdateProductRequest
Similar to StoreProductRequest but allows partial updates:

```php
// Uses 'sometimes' rule for optional updates
- name: sometimes, unique (except current)
- category_id: sometimes, exists
```

#### StoreEmployeeRequest
Validation for creating employees:

```php
Rules:
- employee_code: required, unique
- name: required, max 255
- hire_date: required, date
- base_salary: required, numeric, min 0
- commission_rate: required, numeric, min 0, max 100
- commission_type: required, in:percentage,fixed
- department: required, in:sales,inventory,accounting,management,other
```

#### RecordSaleRequest
Validation for employee sales:

```php
Rules:
- product_id: required, exists in products
- quantity: required, integer, min 1
- unit_price: required, numeric
- sale_date: required, date, before_or_equal:today
```

### Using in Controllers

```php
// Simple usage - validation happens automatically
public function store(StoreProductRequest $request)
{
    $data = $request->validated();
    return Product::create($data);
}

// With error handling
public function store(StoreProductRequest $request)
{
    try {
        $data = $request->validated();
        $product = Product::create($data);
        return response()->json(['success' => true, 'data' => $product]);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

### Validation Responses

Invalid requests return HTTP 422 with errors:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["Product name is required"],
        "email": ["A product with this name already exists"]
    }
}
```

---

## 3. Rate Limiting

### Overview

Rate limiting prevents:
- Brute force login attacks
- API abuse and DoS
- Resource exhaustion

### RateLimitMiddleware

**File:** `app/Http/Middleware/RateLimitMiddleware.php`

Rates:
- **Login**: 5 attempts per 60 seconds (per IP)
- **API**: 60 requests per 60 seconds (per authenticated user)
- **Signup**: 3 attempts per 3600 seconds (per IP)

### Implementation

Store rate limit data in cache with keys like:
```
rate_limit:{type}:{identifier}
```

Where identifier is:
- `user_{id}` for authenticated users
- `ip_{address}` for unauthenticated requests

### Using Rate Limiting

```php
// In routes/api.php
Route::post('login', [AuthController::class, 'login'])
    ->middleware('rate-limit:login')
    ->name('auth.login');

// Apply to all protected routes
Route::prefix('v1')->middleware(['check-api-token', 'rate-limit:api'])->group(function () {
    // Protected routes...
});
```

### Rate Limit Response

When rate limit exceeded, returns HTTP 429:

```json
{
    "success": false,
    "message": "Too many requests. Please try again in 60 seconds."
}
```

### Configuration

Modify limits in `RateLimitMiddleware.php`:

```php
protected function getMaxAttempts(string $limit): int
{
    $limits = [
        'login' => 5,      // Attempts
        'signup' => 3,     // Attempts
        'api' => 60,       // Requests
    ];
    return $limits[$limit] ?? 60;
}

protected function getDecaySeconds(string $limit): int
{
    $decays = [
        'login' => 60,     // Seconds
        'signup' => 3600,  // Seconds
        'api' => 60,       // Seconds
    ];
    return $decays[$limit] ?? 60;
}
```

---

## 4. Token Expiration

### Overview

API tokens expire after 30 days, requiring users to login again for security.

### Database Changes

**Migration:** `database/migrations/2024_04_23_000012_add_api_token_expiration_to_users_table.php`

Adds to users table:
- `api_token_expires_at` (timestamp, nullable)
- Index on `api_token_expires_at` for fast expiration checks

### User Model Changes

Updated `app/Models/User.php`:

```php
// Add to fillable
protected $fillable = [
    // ... existing fields
    'api_token_expires_at',
];

// Add to casts
protected function casts(): array
{
    return [
        // ... existing casts
        'api_token_expires_at' => 'datetime',
    ];
}
```

### Login - Set Expiration

In `AuthController::login()`:

```php
$user->update([
    'api_token' => $token,
    'api_token_expires_at' => now()->addDays(30), // Expires in 30 days
    'last_login' => now(),
]);
```

### Middleware - Check Expiration

In `CheckApiToken::handle()`:

```php
// Check if token has expired
if ($user->api_token_expires_at && now()->isAfter($user->api_token_expires_at)) {
    return response()->json([
        'success' => false,
        'message' => 'Unauthenticated - Token has expired. Please login again.',
    ], 401);
}
```

### Token Refresh - Extend Expiration

In `AuthController::refresh()`:

```php
$user->update([
    'api_token' => $newToken,
    'api_token_expires_at' => now()->addDays(30), // Extend to 30 days
]);
```

### Configuration

To change expiration time, edit the authentication controller:

```php
// In login() and refresh()
'api_token_expires_at' => now()->addDays(30)  // Change 30 to desired days

// Or use addHours(), addMinutes() for shorter durations
'api_token_expires_at' => now()->addHours(1)
```

---

## 5. Security Headers

### Overview

HTTP security headers prevent common attacks:
- MIME type sniffing
- Clickjacking (frame embedding)
- XSS attacks
- Content sniffing

### SecurityHeadersMiddleware

**File:** `app/Http/Middleware/SecurityHeadersMiddleware.php`

Headers added to all responses:

| Header | Value | Purpose |
|--------|-------|---------|
| `X-Content-Type-Options` | `nosniff` | Prevent MIME sniffing |
| `X-Frame-Options` | `SAMEORIGIN` | Prevent clickjacking |
| `X-XSS-Protection` | `1; mode=block` | XSS protection |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Control referrer info |
| `Cache-Control` | `no-cache, no-store, must-revalidate` | Disable caching |
| `Strict-Transport-Security` | `max-age=31536000` | Force HTTPS (production only) |

### Implementation

Middleware automatically applied to all responses:

```php
// In bootstrap/app.php
$middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
```

---

## Testing Phase 5

### 1. Test Authorization

```bash
# Test product creation with different roles
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Product",
    "category_id": 1,
    "sku": "TEST-001",
    "unit_price": 99.99,
    "reorder_level": 10
  }'

# Should succeed for admin/manager, fail for user
```

### 2. Test Input Validation

```bash
# Missing required field
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": 1,
    "sku": "TEST-001"
  }'

# Returns 422 with errors
```

### 3. Test Rate Limiting

```bash
# Try to login 6+ times rapidly
for i in {1..10}; do
  curl -X POST http://localhost:8000/api/v1/auth/login \
    -H "Content-Type: application/json" \
    -d '{
      "email": "admin@hamid.com",
      "password": "admin123456"
    }'
  echo "Attempt $i"
done

# 6th attempt returns 429 Too Many Requests
```

### 4. Test Token Expiration

```bash
# Login to get token
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@hamid.com",
    "password": "admin123456"
  }'

# Use token immediately - works
curl -H "Authorization: Bearer {token}" \
  http://localhost:8000/api/v1/auth/me

# Wait for expiration (30 days) or manually update database
UPDATE users SET api_token_expires_at = NOW() - INTERVAL 1 DAY;

# Token usage returns 401 - token expired
```

### 5. Test Security Headers

```bash
# Check response headers
curl -I http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}"

# Verify headers present:
# - X-Content-Type-Options: nosniff
# - X-Frame-Options: SAMEORIGIN
# - X-XSS-Protection: 1; mode=block
```

---

## Running Migrations

Execute database migrations to create token expiration support:

```bash
# Run all migrations
php artisan migrate

# Or specific migration
php artisan migrate --path=database/migrations/2024_04_23_000012_add_api_token_expiration_to_users_table.php
```

---

## Security Checklist

- ✅ All API routes protected with token authentication
- ✅ Authorization policies enforce role-based permissions
- ✅ Input validation prevents malicious data
- ✅ Rate limiting prevents brute force attacks
- ✅ Tokens expire after 30 days
- ✅ Security headers prevent common attacks
- ✅ Passwords hashed with bcrypt
- ✅ API tokens are 80 characters (cryptographically secure)
- ✅ Tokens stored with unique constraint
- ✅ Middleware runs on all protected routes

---

## API Endpoints Summary

### Authentication (Public - No Token Required)
- `POST /api/v1/auth/login` - Login (5 attempts/min rate limit)

### Authentication (Protected - Token Required)
- `GET /api/v1/auth/me` - Get current user
- `POST /api/v1/auth/logout` - Logout (revoke token)
- `POST /api/v1/auth/refresh` - Refresh token (extends expiration)
- `POST /api/v1/auth/change-password` - Change password

### Products (Protected)
- `GET /api/v1/products` - List products
- `POST /api/v1/products` - Create product (requires 'create-product' permission)
- `GET /api/v1/products/{id}` - View product
- `PUT /api/v1/products/{id}` - Update product (requires 'edit-product' permission)
- `DELETE /api/v1/products/{id}` - Delete product (requires 'delete-product' permission)

### Employees (Protected)
- `GET /api/v1/employees` - List employees
- `POST /api/v1/employees` - Create employee (requires 'create-employee' permission)
- `GET /api/v1/employees/{id}` - View employee
- `PUT /api/v1/employees/{id}` - Update employee (requires 'edit-employee' permission)
- `DELETE /api/v1/employees/{id}` - Delete employee (requires 'delete-employee' permission)

All endpoints protected with Bearer token authentication and rate limiting.

---

## Troubleshooting

### Token Expired Error
- Login again to get new token
- Or call `POST /api/v1/auth/refresh` to extend current token

### Authorization Denied
- Check user's role and assigned permissions
- Verify policy allows the action
- Admin role bypasses most permission checks

### Rate Limit Exceeded
- Wait for the time period to expire
- Login rate limit: 1 minute
- API rate limit: 1 minute
- Check X-RateLimit-* headers in response

### Validation Failed (422 Error)
- Check error messages for specific field issues
- Ensure required fields present
- Verify unique constraints (email, sku, etc.)
- Check field formats (dates, numbers, etc.)

---

## Next Steps

- Implement authorization policies for remaining resources
- Add audit logging for authorization decisions
- Implement API versioning strategy
- Add CORS configuration for frontend integration
- Create API documentation (OpenAPI/Swagger)
- Implement additional security measures (CSRF, CORS, etc.)
