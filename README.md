# Ecommerce Project (Multi-Tenant)

This is a full-featured Ecommerce project built with Laravel, now enhanced with Single Database Multi-Tenant support.

## Features

### Core Modules
- Category, Product, Variant, Coupon, Order, Order Item, Cart, Cart Item
- Dashboard using Filament
  - Filament Resources, RelationManagers, and Widgets
  - All resources and widgets automatically filtered per tenant (multi-tenancy)
- Single Database Multi-Tenant
  - Each admin belongs to a specific tenant (store/branch)
  - All data (Orders, Products, Coupons, etc.) is scoped automatically per tenant
  - Tenant filtering handled via global scopes and HasTenants implementation in Admin model

### Authentication
- API authentication using Laravel Sanctum
  - Register, Login, Logout
  - Access and Refresh Tokens
  - Forget and Reset Password
- Multi-guard authentication
  - Separate tables for users (API login) and admins (dashboard login)
  - Admins automatically linked to their tenant

### API
- Categories, Products, and Orders APIs
- Cart API implemented using Repository Design Pattern
- Checkout function tested with Unit Tests
- MyFatoorah payment integration implemented using Factory Design Pattern
- APIs respect tenant scoping automatically when accessed via dashboard/admin

### Admin Panel
- Admins see only their tenant’s data
- Resources, tables, and widgets automatically scoped per tenant
- Global tenant filtering prevents access to other tenants’ data
- Admin can manage Products, Orders, Coupons, Categories based on tenant permissions

## Updates

- [Date] Added Single Database Multi-Tenant support  
- [Date] Implemented tenant filtering for all resources and widgets in Filament  
- [Date] Updated Admin model to implement HasTenants  
- [Date] Added global scopes for tenant data filtering  
- [Date] Seeders and factories updated for multi-tenant testing