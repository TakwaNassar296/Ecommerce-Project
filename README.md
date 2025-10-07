# Ecommerce Project (Multi-Tenant)

Project Overview:  
A full-featured Ecommerce project built with Laravel, enhanced with Single Database Multi-Tenant support.  
The system includes an admin dashboard, API for users, real-time notifications, and email notifications, with automatic tenant data isolation to ensure security and proper access control.

---

## Features

### Core Modules
- Categories, Products, Variants: Manage product catalog with multiple variations.  
- Coupons: Create and apply discount codes scoped to each tenant.  
- Orders & Order Items: Track and manage customer orders and their items.  
- Cart & Cart Items: Implement shopping cart using Repository Design Pattern.  
- Multi-Tenant Support:  
- Admins and users are linked to specific tenants (stores/branches).  
- Automatic data filtering per tenant using Global Scopes and HasTenants trait.  
- Users can belong to multiple tenants without duplication.  

---

### Authentication
- API Authentication (Laravel Sanctum): Register, login, logout, access and refresh tokens.  
- Multi-Guard Authentication: Separate admin and API user tables.  
- Password Management: Forgot and reset password functionality.  
- Tenant Linking: Users added via admin panel are automatically linked to the tenant if already existing.  

---

### API
- RESTful APIs for categories, products, and orders.  
- Cart API implemented with Repository Design Pattern.  
- Checkout & Payment Integration: MyFatoorah integration implemented using Factory Design Pattern.  
- Tenant Scoping: All API endpoints automatically respect tenant isolation.  

---

### Admin Panel (Filament)
- Scoped Data: Admins can only access their tenant's data.  
- Resources, Tables & Widgets: Automatically filtered per tenant.  
- User Management:  
- Link users to multiple tenants without creating duplicates.  
- Redirect to existing user edit page if the email already exists.  

---

### Real-Time & Notifications
- Pusher: Real-time order notifications via private channels (private-order.{customer_id}).  
- Events & Listeners: OrderCreatedEvent triggers:  
- Real-time broadcast to the customer.  
- Email notification sent automatically.  
- Jobs: Emails are queued using Laravel Jobs to ensure background processing.  

---

### Reporting & Commands
- DailyOrderReport Command:  
- Generates daily report of orders for each tenant.  
- Sends email notification to the tenant’s admin.  
- Command can be triggered manually or scheduled.  

---

### Notes
- All features are designed for single database multi-tenancy, ensuring secure and isolated data per tenant.  
- Project demonstrates modern Laravel practices including Jobs, Events, Listeners, Repository & Factory Patterns, and Filament admin panel customization.  
- Ideal for showcasing backend development skills, multi-tenancy architecture, and API design.