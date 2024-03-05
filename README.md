# LaratrustPermissionCreator

LaratrustPermissionCreator is a Laravel package that facilitates the management of roles and permissions in your application. It provides two artisan commands, `laratrust:from-config` and `laratrust:from-db`, to read and synchronize roles and permissions either from configuration files or directly from the database.

## Installation

Install the package using Composer:

```bash
composer require your-vendor/laratrust-permission-creator
```

Once installed, run the migrations to set up the necessary database tables:

```bash
php artisan migrate
```

## Usage

### Laratrust:from-config

The `laratrust:from-config` command reads roles and permissions from configuration files located in the `laratrust` folder at the root of your project. To create or update permissions, you can add new PHP files in the following format:

#### Example File: BasicRolePermissions.php

```php
return [
    "permissions" => [
        "tour_create" => [
            "display_name" => "ساخت تور"
        ],
        "tour_edit" => [
            "display_name" => "به روز رسانی تور"
        ],
    ],
    "roles" => [
        "basic" => [
            "display_name" => "پایه",
            "description" => "این رول را همه کاربران دارند"
        ],
        "admin" => [
            "display_name" => "ادمین",
            "description" => "مخصوص ادمین کل"
        ],
        "supervisor" => [
            "display_name" => "سرپرست",
            "description" => "سرپرست اصلی تور"
        ]
    ],
    "rolesPermissions" => [
        "admin" => [
            "tour_create",
            "tour_update",
            "tour_show",
            "tour_list",
            "tour_delete",
        ],
        "supervisor" => [
            "tour_create",
            "tour_update",
            "tour_show",
            "tour_list",
            "tour_delete",
        ]
    ]
];
```

To execute the command:

```bash
php artisan laratrust:from-config
```

This will create or update the roles and permissions based on the provided configuration files.

### Laratrust:from-db

The `laratrust:from-db` command reads roles and permissions directly from the database and updates the configuration files in the `laratrust` folder accordingly.

```bash
php artisan laratrust:from-db
```

This command ensures that your configuration files stay in sync with the current state of roles and permissions stored in the database.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE). Feel free to use, modify, and distribute as needed.
