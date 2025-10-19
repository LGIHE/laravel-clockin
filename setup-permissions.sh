#!/bin/bash

# Permission System Setup Script
# This script sets up the permission system for the application

echo "=========================================="
echo "Permission System Setup"
echo "=========================================="
echo ""

# Step 1: Run migrations
echo "Step 1: Running migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo "✓ Migrations completed successfully"
else
    echo "✗ Migration failed"
    exit 1
fi
echo ""

# Step 2: Seed permissions
echo "Step 2: Seeding permissions..."
php artisan db:seed --class=PermissionSeeder --force
if [ $? -eq 0 ]; then
    echo "✓ Permissions seeded successfully"
else
    echo "✗ Permission seeding failed"
    exit 1
fi
echo ""

# Step 3: Assign default permissions to existing roles
echo "Step 3: Assigning default permissions to existing roles..."
php artisan db:seed --class=DefaultRolePermissionsSeeder --force
if [ $? -eq 0 ]; then
    echo "✓ Default permissions assigned successfully"
else
    echo "✗ Default permission assignment failed"
    exit 1
fi
echo ""

# Step 4: Clear cache
echo "Step 4: Clearing application cache..."
php artisan optimize:clear
if [ $? -eq 0 ]; then
    echo "✓ Cache cleared successfully"
else
    echo "✗ Cache clearing failed"
    exit 1
fi
echo ""

echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Navigate to: Admin Dashboard → Roles & Permissions"
echo "2. Create custom roles as needed"
echo "3. Assign permissions to roles"
echo "4. Assign users to roles"
echo ""
echo "Documentation:"
echo "- README_PERMISSIONS.md - System overview"
echo "- IMPLEMENTATION_GUIDE.md - Implementation guide"
echo "- EXAMPLE_MIGRATION.md - Code examples"
echo "- PERMISSION_SYSTEM_SUMMARY.md - Quick summary"
echo ""
