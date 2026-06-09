# Laratrust Permission Creator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gopex/laratrust-permission-creator.svg?style=flat-square)](https://packagist.org/packages/gopex/laratrust-permission-creator)
[![Total Downloads](https://img.shields.io/packagist/dt/gopex/laratrust-permission-creator.svg?style=flat-square)](https://packagist.org/packages/gopex/laratrust-permission-creator)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Manage your Laratrust roles and permissions using a **Code-First (Configuration-Driven)** approach. Define your ACL structure in clean PHP files, keep them version-controlled via Git, and seamlessly synchronize them with your database in both directions.


## Why Use This Package?

In large-scale applications, managing roles and permissions through database seeders or UI dashboards can become messy and hard to track across different environments (local, staging, production). 

**Laratrust Permission Creator** solves this by allowing you to:
* 🛠️ **Declare Everything in Code:** Define roles, permissions, and their assignments in a simple PHP array.
* 🌿 **Git Version Control:** Track every change, addition, or deletion in your permission schema via Git.
* 🧩 **Modular Files:** Split your permissions into separate files (e.g., by module or feature) to avoid massive, unreadable configuration files.
* 🔄 **Two-Way Sync:** Push configuration changes to the database OR export an existing database schema back into code.


## Installation

You can install the package via Composer:

```bash
composer require gopex/laratrust-permission-creator

```

After installing the package, run the publish command to automatically generate the initial `laratrust` directory and the template configuration file in your project's root:

```bash
php artisan vendor:publish --tag="laratrust-permissions-file"

```

This will create the following file in your project:

* `/laratrust/RoleAndPermissions.php`


## Configuration Structure

The generated `laratrust/RoleAndPermissions.php` file returns an array with 4 main keys. Let's break down each section with clear examples:

### 1. Splitting Files (`fromFile`)

If your application is large, don't put everything in one file. You can split your configurations into multiple separate files and link them here. The paths must be relative to your project's root directory.

```php
"fromFile" => [
    "laratrust/BasicRolePermissions.php",
    "laratrust/TraderRequestPermissions.php",
],

```

### 2. Defining Permissions (`permissions`)

You can define permissions in two ways:

* **Simple (String):** Just the permission name. `display_name` and `description` will be empy.
* **Advanced (Array):** Provide a custom `display_name` and `description`.

```php
"permissions" => [
    // 1. Simple Format (No extra details needed)
    "edit_posts", 

    // 2. Advanced Format (With custom display name and description)
    "delete_users" => [
        "display_name" => "Delete Registered Users", // Optional
        "description"  => "Allows a user to permanently delete accounts" // Optional
    ]
],

```

### 3. Defining Roles (`roles`)

Just like permissions, roles can be defined as a simple string or a detailed array:

```php
"roles" => [
    // 1. Simple Format
    "moderator",

    // 2. Advanced Format
    "super_admin" => [
        "display_name" => "Super Administrator", // Optional
        "description"  => "Has full control over the entire system" // Optional
    ]
],

```

### 4. Binding Roles to Permissions (`rolesPermissions`)

This is where you connect your roles to their respective permissions.

```php
"rolesPermissions" => [
    // Bind existing permissions to an existing role
    "super_admin" => [
        "edit_posts",
        "delete_users",
    ],

    // Smart Behavior Example:
    "support_agent" => [
        "edit_posts",
        "view_logs" // This permission will be auto-created even if missing in the 'permissions' array!
    ]
]

```

### 💡 Key Rules & Smart Behaviors (How it works under the hood)

To avoid breaking your application, the package follows these strict yet flexible rules during synchronization:

1. **Auto-Creation:** If you add a role or permission inside the `rolesPermissions` relation array, but forgot to declare it under the main `"roles"` or `"permissions"` tags, **the package will safely create it anyway**.
2. **Updates:** If you change the `display_name` or `description` of a role/permission in the file, running `php artisan laratrust:from-config` will instantly update those values in the database.
3. **No Redundancy Required (Independent Definition):** You do not *have* to link every single permission or role to each other. 
   * If you define a permission under the `"permissions"` key but leave it out of `"rolesPermissions"`, it will still be created/updated in the database.
   * **Similarly for roles:** If you define a role under the `"roles"` key but do not assign any permissions to it inside `"rolesPermissions"`, it will still be successfully created/updated in your database for future use or manual assignment.


## Usage & Artisan Commands

This package provides two simple yet powerful commands to handle two-way synchronization.

### 1. Synchronize Config to Database

When you add, modify, or remove roles and permissions inside your PHP files, run the following command to detect and apply changes directly to your database:

```bash
php artisan laratrust:from-config

```

*Automatically handles insertions, updates, and schema integrity.*

### 2. Export Database to Config (Reverse Sync)

If you are introducing this package into an existing project that already has roles and permissions populated in the database, you can update or generate your configuration file based on your current database state by running:

```bash
php artisan laratrust:from-db

```

*Perfect for legacy projects or migrating to a code-first approach without losing existing data.*

---

## Contributing

Contributions are welcome! Please feel free to submit Pull Requests or open Issues for bugs and feature requests.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
