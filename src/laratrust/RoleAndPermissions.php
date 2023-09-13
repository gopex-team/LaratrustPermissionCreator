<?php

return [
    "fromFile" => [
        /**
         * if you have other file contains permissions ,roles data you can add path of file from root to this array;
         */
        // "other_folder_from_source_path/myPermissionsRole.php"
    ],


    "permissions" => [
        /**
         * [permission name],
         *  or
         *  [permission name] => [
         *  "display_name" => [display name] =====> it's not required
         *  "description" => [description] =====> it's not required
         * ]
         */

        "test_permission_without_description_and_name",

        "test_permission" => [
            "display_name" => "Test Permission", // optional
            "description" => "Test Permission Description" // optional
        ]
    ],

    "roles" => [
        /**
         * [role name],
         *  or
         *  [role name] => [
         *  "display_name" => [display name] =====> it's not required
         *  "description" => [description] =====> it's not required
         * ]
         */

        "test_role_without_description_and_name",

        "test_role" => [
            "display_name" => "Test Role", // optional
            "description" => "Test Role Description" // optional
        ]
    ],

    "rolesPermissions" => [
        "other_role_not_exists_in_roles_tag" =>[
            "test_permission",
        ],

        "test_role" => [
            "test_permission",
            "another_permission_not_exists_in_permissions" // if you need display name of description you must add it to permission array
        ]
    ]
];
