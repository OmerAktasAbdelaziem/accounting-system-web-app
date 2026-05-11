# Role-Based Access Control (RBAC) Implementation Guide

## Overview

The RBAC system provides fine-grained permission control for the Aktaš System. It manages user access to features based on their role (Admin, Manager, User) and implements UI-level access control to show/hide features based on permissions.

## Architecture

### Permission Model

**Admin Role Permissions:**
- All permissions (40+ permissions)
- Full system access
- User and role management
- Audit trail access
- Settings management

**Manager Role Permissions:**
- Create/Edit Products (no delete)
- Create/Edit Employees (no delete)
- Create/Edit Sales
- Post/Edit Journal Entries
- View all reports
- Export reports
- View audit logs

**User Role Permissions:**
- View Products
- View Employees  
- Create/View Sales
- View Chart of Accounts
- View Reports

## Implementation

### 1. Basic Setup

Add the RBAC manager to your HTML file:

```html
<script src="/rbac-manager.js"></script>
```

Initialize RBAC in your page's DOMContentLoaded:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    if (!API_TOKEN) {
        window.location.href = '/login.html';
        return;
    }

    // Initialize RBAC
    initializeRBAC(API_TOKEN, API_BASE_URL)
        .then(() => {
            setLanguage(USER_LANG);
            loadData();
        })
        .catch(() => {
            window.location.href = '/login.html';
        });
});
```

### 2. Permission-Based UI Control

#### Method 1: Using data-rbac Attributes

The simplest way - automatically hide elements based on permission:

```html
<!-- Only visible to users with 'create-product' permission -->
<button class="btn btn-primary" data-rbac="create-product">
    <i class="bi bi-plus"></i> Add Product
</button>

<!-- Only visible to admins -->
<button class="btn btn-danger" data-rbac="manage-users">
    <i class="bi bi-gear"></i> Manage Users
</button>

<!-- Only visible to managers and admins -->
<button class="btn btn-info" data-rbac="manage-roles">
    <i class="bi bi-lock"></i> Manage Roles
</button>
```

#### Method 2: JavaScript Permission Checks

```javascript
// Check single permission
if (rbacManager.hasPermission('delete-product')) {
    // Show delete button
    document.getElementById('btn-delete').style.display = 'block';
}

// Check multiple permissions (ALL required)
if (rbacManager.hasAllPermissions(['create-product', 'edit-product'])) {
    // User can create AND edit products
}

// Check multiple permissions (ANY required)
if (rbacManager.hasAnyPermission(['create-sale', 'edit-sale'])) {
    // User can create OR edit sales
}

// Role checks
if (rbacManager.isAdmin()) {
    // Admin-only actions
}

if (rbacManager.isManager()) {
    // Manager-only actions
}
```

#### Method 3: Utility Functions

```javascript
// Quick permission check
if (canAccess('delete-employee')) {
    deleteButton.onclick = deleteEmployee;
}

// Get current role
const role = getCurrentRole();

// Check admin/manager status
if (isAdmin() || isManager()) {
    showAdvancedReports = true;
}
```

### 3. UI Element Control Functions

#### Showing/Hiding Elements

```javascript
// Show element if user has permission
rbacManager.showIfPermitted('#add-product-btn', 'create-product');

// Hide element if user doesn't have permission
rbacManager.hideIfNotPermitted('#delete-product-btn', 'delete-product');

// Show if any permission
rbacManager.showIfAnyPermitted('#sales-section', ['create-sale', 'edit-sale']);
```

#### Disabling Elements

```javascript
// Disable button if no permission
rbacManager.disableIfNotPermitted('#edit-employee-btn', 'edit-employee');
```

#### Filtering Data

```javascript
// Filter menu items based on permissions
const menuItems = [
    { name: 'Add Product', permission: 'create-product' },
    { name: 'Delete Product', permission: 'delete-product' },
    { name: 'Manage Users', permission: 'manage-users' }
];

const visibleItems = rbacManager.filterByPermission(
    menuItems, 
    'permission', 
    'any'  // or specify exact permission
);
```

## Practical Examples

### Example 1: Conditional Button Display in Products Page

```html
<!-- Products Management Page -->
<button class="btn btn-primary" id="btn-add-product" data-rbac="create-product">
    Add Product
</button>

<button class="btn btn-outline-primary" id="btn-edit-product" data-rbac="edit-product">
    Edit Product
</button>

<button class="btn btn-outline-danger" id="btn-delete-product" data-rbac="delete-product">
    Delete Product
</button>
```

JavaScript automatically hides these buttons if the user doesn't have permission.

### Example 2: Dynamic Menu Generation

```javascript
const dashboardMenu = [
    {
        title: 'Dashboard',
        icon: 'bi-graph-up',
        link: '/admin-dashboard.html',
        permission: null  // No permission needed
    },
    {
        title: 'Products',
        icon: 'bi-box',
        link: '/products-management.html',
        permission: 'view-products'
    },
    {
        title: 'Employees',
        icon: 'bi-people',
        link: '/employees-management.html',
        permission: 'view-employees'
    },
    {
        title: 'Accounting',
        icon: 'bi-cash-coin',
        link: '/accounting-management.html',
        permission: 'view-accounts'
    },
    {
        title: 'Reports',
        icon: 'bi-bar-chart',
        link: '/reports-management.html',
        permission: 'view-reports'
    },
    {
        title: 'Admin Settings',
        icon: 'bi-gear',
        link: '/admin-settings.html',
        permission: 'manage-users'  // Admin only
    }
];

// Filter and render menu
function renderMenu() {
    const visibleMenu = dashboardMenu.filter(item => {
        if (!item.permission) return true;  // Always show if no permission required
        return rbacManager.hasPermission(item.permission);
    });

    const menuHtml = visibleMenu.map(item => `
        <li><a href="${item.link}"><i class="bi ${item.icon}"></i> ${item.title}</a></li>
    `).join('');

    document.getElementById('menu-container').innerHTML = menuHtml;
}

// Call after RBAC is initialized
renderMenu();
```

### Example 3: API Request with Authorization

```javascript
// All API calls automatically use RBAC authorization headers
const headers = rbacManager.getAuthorizationHeader(API_TOKEN);

$.ajax({
    url: `${API_BASE_URL}/products`,
    type: 'POST',
    headers: headers,
    data: JSON.stringify(productData),
    success: function(response) {
        // Handle success
    }
});
```

### Example 4: Conditional Feature Availability

```javascript
function initializeReports() {
    if (rbacManager.hasPermission('view-reports')) {
        loadReportsData();
    } else {
        document.getElementById('reports-section').innerHTML = 
            '<p>You do not have permission to view reports.</p>';
    }
}

function enableExport() {
    if (rbacManager.hasPermission('export-reports')) {
        document.getElementById('export-button').style.display = 'block';
    }
}
```

## Integration with Existing Dashboards

### Step 1: Add Script Reference

In the `<head>` section of each HTML file:

```html
<script src="/rbac-manager.js"></script>
```

### Step 2: Update DOMContentLoaded

```javascript
document.addEventListener('DOMContentLoaded', function() {
    if (!API_TOKEN) {
        window.location.href = '/login.html';
        return;
    }

    // Initialize RBAC before loading any data
    initializeRBAC(API_TOKEN, API_BASE_URL)
        .then(() => {
            setLanguage(USER_LANG);
            loadData();
            setupEventListeners();
        })
        .catch(() => {
            window.location.href = '/login.html';
        });
});
```

### Step 3: Mark Elements with data-rbac

```html
<!-- Add Product Button -->
<button class="btn btn-primary-custom" id="btn-add-product" data-rbac="create-product">
    Add Product
</button>

<!-- Edit Product Button -->
<button class="btn btn-outline-primary" data-rbac="edit-product">
    Edit
</button>

<!-- Delete Product Button -->
<button class="btn btn-outline-danger" data-rbac="delete-product">
    Delete
</button>
```

## Permission Matrix

| Permission | Admin | Manager | User |
|------------|:-----:|:-------:|:----:|
| view-products | ✓ | ✓ | ✓ |
| create-product | ✓ | ✓ | ✗ |
| edit-product | ✓ | ✓ | ✗ |
| delete-product | ✓ | ✗ | ✗ |
| view-employees | ✓ | ✓ | ✓ |
| create-employee | ✓ | ✓ | ✗ |
| edit-employee | ✓ | ✓ | ✗ |
| delete-employee | ✓ | ✗ | ✗ |
| create-sale | ✓ | ✓ | ✓ |
| edit-sale | ✓ | ✓ | ✗ |
| delete-sale | ✓ | ✗ | ✗ |
| view-accounts | ✓ | ✓ | ✓ |
| post-journal | ✓ | ✓ | ✗ |
| edit-journal | ✓ | ✓ | ✗ |
| delete-journal | ✓ | ✗ | ✗ |
| view-reports | ✓ | ✓ | ✓ |
| export-reports | ✓ | ✓ | ✗ |
| manage-users | ✓ | ✗ | ✗ |
| manage-roles | ✓ | ✗ | ✗ |
| manage-settings | ✓ | ✗ | ✗ |
| view-audit-log | ✓ | ✓ | ✗ |

## Security Considerations

### Client-Side vs Server-Side

⚠️ **Important**: RBAC implemented on the client-side is for UX purposes only.

**For security:**
1. Always validate permissions on the backend
2. Check user role and permissions before processing any request
3. Backend should never trust client-side permission checks
4. API endpoints should independently verify user authorization

### Best Practices

1. **Always validate on backend**: Even if UI hides a button, validate on API side
2. **Use Bearer tokens**: All requests include Bearer token for authentication
3. **Log access attempts**: Track failed authorization attempts
4. **Refresh user data**: Periodically refresh user permissions from server
5. **Handle token expiry**: Redirect to login if token expires

## Extending RBAC

### Adding New Roles

```javascript
// In rbacManager.rolePermissions
rolePermissions: {
    'Admin': [...],
    'Manager': [...],
    'User': [...],
    'SuperManager': [  // New role
        // All manager permissions
        // Plus additional permissions
        'delete-sales',
        'manage-departments'
    ]
}
```

### Adding New Permissions

1. Add to appropriate roles in `rolePermissions`
2. Add data-rbac attribute to HTML elements
3. Add permission check in JavaScript

```javascript
// Add to Employee Delete handler
if (!rbacManager.hasPermission('delete-employee')) {
    showToast('Permission denied', 'error');
    return;
}
```

## Troubleshooting

### Elements Still Visible After Permission Check

- Ensure `data-rbac` attribute matches exact permission name
- Verify RBAC is initialized before DOM manipulation
- Check browser console for RBAC initialization errors

### Permission Denied But Should Exist

- Verify user role in database
- Check rolePermissions object has the permission
- Ensure backend API returns correct user role

### RBAC Not Initializing

- Verify API_TOKEN is set in localStorage
- Check API_BASE_URL is correct
- Look for AJAX errors in browser console
- Verify /api/v1/auth/me endpoint responds correctly

## Testing RBAC

### Test as Different Roles

1. Login as Admin - all features visible
2. Login as Manager - limited features visible
3. Login as User - minimal features visible

### Test Permission Checks

```javascript
// In browser console
rbacManager.hasPermission('create-product')  // true/false
rbacManager.getRole()  // "Admin", "Manager", or "User"
rbacManager.getAllPermissions()  // Array of all permissions
```

## Summary

The RBAC system provides:
- ✓ Role-based permission control
- ✓ UI-level access management
- ✓ Granular permission checking
- ✓ Easy integration with existing pages
- ✓ Extensible permission model
- ✓ Security-conscious design

For complete implementation, integrate rbac-manager.js into all dashboard pages and mark actions/elements with appropriate data-rbac attributes.
