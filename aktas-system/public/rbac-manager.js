/**
 * Role-Based Access Control (RBAC) Utility
 * Implements permission checking and UI element control based on user role
 */

class RBACManager {
    constructor() {
        this.currentUser = null;
        this.userPermissions = [];
        this.rolePermissions = {
            'Admin': [
                'create-product', 'edit-product', 'delete-product', 'view-products',
                'create-employee', 'edit-employee', 'delete-employee', 'view-employees',
                'create-sale', 'edit-sale', 'delete-sale', 'view-sales',
                'post-journal', 'edit-journal', 'delete-journal', 'view-accounts',
                'view-reports', 'export-reports',
                'manage-users', 'manage-roles',
                'view-audit-log', 'manage-settings'
            ],
            'Manager': [
                'create-product', 'edit-product', 'view-products',
                'create-employee', 'edit-employee', 'view-employees',
                'create-sale', 'edit-sale', 'view-sales',
                'post-journal', 'edit-journal', 'view-accounts',
                'view-reports', 'export-reports',
                'view-audit-log'
            ],
            'User': [
                'view-products',
                'view-employees',
                'create-sale', 'view-sales',
                'view-accounts',
                'view-reports'
            ]
        };
    }

    /**
     * Initialize RBAC with current user data
     */
    async initialize(apiToken, apiBaseUrl) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${apiBaseUrl}/auth/me`,
                type: 'GET',
                headers: { 'Authorization': `Bearer ${apiToken}` },
                success: (response) => {
                    this.currentUser = response.data;
                    this.setUserPermissions();
                    resolve(this.currentUser);
                },
                error: (error) => {
                    console.error('Failed to load user data:', error);
                    reject(error);
                }
            });
        });
    }

    /**
     * Set permissions based on user role
     */
    setUserPermissions() {
        const role = this.currentUser.role || 'User';
        this.userPermissions = this.rolePermissions[role] || [];
    }

    /**
     * Check if user has a specific permission
     */
    hasPermission(permission) {
        return this.userPermissions.includes(permission);
    }

    /**
     * Check if user has all required permissions
     */
    hasAllPermissions(permissions) {
        return permissions.every(p => this.userPermissions.includes(p));
    }

    /**
     * Check if user has any of the required permissions
     */
    hasAnyPermission(permissions) {
        return permissions.some(p => this.userPermissions.includes(p));
    }

    /**
     * Get user role
     */
    getRole() {
        return this.currentUser?.role || 'User';
    }

    /**
     * Check if user is admin
     */
    isAdmin() {
        return this.getRole() === 'Admin';
    }

    /**
     * Check if user is manager
     */
    isManager() {
        return this.getRole() === 'Manager';
    }

    /**
     * Show element if user has permission
     */
    showIfPermitted(selector, permission) {
        const element = document.querySelector(selector);
        if (element) {
            element.style.display = this.hasPermission(permission) ? 'block' : 'none';
        }
    }

    /**
     * Hide element if user doesn't have permission
     */
    hideIfNotPermitted(selector, permission) {
        const element = document.querySelector(selector);
        if (element) {
            element.style.display = !this.hasPermission(permission) ? 'none' : 'block';
        }
    }

    /**
     * Show element if user has any permission
     */
    showIfAnyPermitted(selector, permissions) {
        const element = document.querySelector(selector);
        if (element) {
            element.style.display = this.hasAnyPermission(permissions) ? 'block' : 'none';
        }
    }

    /**
     * Disable button if user doesn't have permission
     */
    disableIfNotPermitted(selector, permission) {
        const element = document.querySelector(selector);
        if (element) {
            element.disabled = !this.hasPermission(permission);
            if (!this.hasPermission(permission)) {
                element.style.opacity = '0.5';
                element.style.cursor = 'not-allowed';
            }
        }
    }

    /**
     * Apply role-based styling to element
     */
    applyRoleBasedStyling(selector, role, styles) {
        if (this.getRole() === role) {
            const element = document.querySelector(selector);
            if (element) {
                Object.assign(element.style, styles);
            }
        }
    }

    /**
     * Get all permissions for current user
     */
    getAllPermissions() {
        return this.userPermissions;
    }

    /**
     * Get user information
     */
    getUserInfo() {
        return this.currentUser;
    }

    /**
     * Check permission and show error message if denied
     */
    checkPermissionWithMessage(permission) {
        if (!this.hasPermission(permission)) {
            console.warn(`Permission denied: ${permission}`);
            return false;
        }
        return true;
    }

    /**
     * Filter items based on permissions
     */
    filterByPermission(items, permissionKey, permission) {
        return items.filter(item => {
            if (item[permissionKey]) {
                return this.hasPermission(item[permissionKey]);
            }
            return true;
        });
    }

    /**
     * Build authorization data
     */
    getAuthorizationHeader(apiToken) {
        return {
            'Authorization': `Bearer ${apiToken}`,
            'Content-Type': 'application/json'
        };
    }

    /**
     * Apply UI restrictions based on role
     */
    applyUIRestrictions() {
        const role = this.getRole();

        // Product Management Restrictions
        if (!this.hasPermission('create-product')) {
            this.hideIfNotPermitted('[data-rbac="add-product"]', 'create-product');
        }
        if (!this.hasPermission('edit-product')) {
            this.disableIfNotPermitted('[data-rbac="edit-product"]', 'edit-product');
        }
        if (!this.hasPermission('delete-product')) {
            this.hideIfNotPermitted('[data-rbac="delete-product"]', 'delete-product');
        }

        // Employee Management Restrictions
        if (!this.hasPermission('create-employee')) {
            this.hideIfNotPermitted('[data-rbac="add-employee"]', 'create-employee');
        }
        if (!this.hasPermission('edit-employee')) {
            this.disableIfNotPermitted('[data-rbac="edit-employee"]', 'edit-employee');
        }
        if (!this.hasPermission('delete-employee')) {
            this.hideIfNotPermitted('[data-rbac="delete-employee"]', 'delete-employee');
        }

        // Accounting Restrictions
        if (!this.hasPermission('post-journal')) {
            this.hideIfNotPermitted('[data-rbac="post-journal"]', 'post-journal');
        }

        // Reports Restrictions
        if (!this.hasPermission('view-reports')) {
            this.hideIfNotPermitted('[data-rbac="view-reports"]', 'view-reports');
        }
        if (!this.hasPermission('export-reports')) {
            this.hideIfNotPermitted('[data-rbac="export-reports"]', 'export-reports');
        }

        // Admin-only Sections
        if (!this.isAdmin()) {
            this.hideIfNotPermitted('[data-rbac="admin-only"]', 'manage-users');
        }

        // Manager-only Sections
        if (!this.isManager() && !this.isAdmin()) {
            this.hideIfNotPermitted('[data-rbac="manager-only"]', 'manage-roles');
        }
    }

    /**
     * Apply element visibility based on data-rbac attribute
     */
    applyDataRBACAttributes() {
        document.querySelectorAll('[data-rbac]').forEach(element => {
            const permission = element.getAttribute('data-rbac');
            if (permission && !this.hasPermission(permission)) {
                element.style.display = 'none';
            }
        });
    }
}

// Global instance
let rbacManager = new RBACManager();

/**
 * Initialize RBAC in document ready
 */
function initializeRBAC(apiToken, apiBaseUrl) {
    return rbacManager.initialize(apiToken, apiBaseUrl)
        .then(() => {
            rbacManager.applyUIRestrictions();
            rbacManager.applyDataRBACAttributes();
            console.log('RBAC initialized for role:', rbacManager.getRole());
            return rbacManager;
        })
        .catch(error => {
            console.error('Failed to initialize RBAC:', error);
            throw error;
        });
}

/**
 * Quick permission check function
 */
function canAccess(permission) {
    return rbacManager.hasPermission(permission);
}

/**
 * Get current user role
 */
function getCurrentRole() {
    return rbacManager.getRole();
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    return rbacManager.isAdmin();
}

/**
 * Check if current user is manager
 */
function isManager() {
    return rbacManager.isManager();
}
