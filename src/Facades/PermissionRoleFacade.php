<?php

namespace Gopex\LaratrustPermissionCreator\Facades;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PermissionRoleFacade
{
    public static function loadFromFiles(){
        $configs = resolve("laratrust_permissions_config");

        $filePaths = $configs->files;
        $ret = [];
        while ($filePaths){
            $path = array_shift($filePaths);
            $data = require($path);
            if (is_array($data['fromFile'] ?? '')){
                array_push($filePaths, ...$data['fromFile']);
            }
            $ret = array_merge_recursive($ret , $data);
        }

        if (! isset($ret['roles'])) $ret['roles'] = [];
        if (! isset($ret['permissions'])) $ret['permissions'] = [];
        if (! isset($ret['rolesPermissions'])) $ret['rolesPermissions'] = [];

        return $ret;
    }

    private static function convertStringsToArray($array){
        foreach ($array as $key => $item){
            if (! is_array($item)){
                $array[$item] = [];
                unset($array[$key]);
            }
        }

        return $array;
    }

    private static function getMultidimensionalValue($array){
        $ret = [];
        foreach ($array as $item){
            array_push($ret , ...$item);
        }

        return $ret;
    }

    public static function optimizeData($data){
        $roles = array_keys($data['rolesPermissions']);
        $permissionsInRoles = self::getMultidimensionalValue($data['rolesPermissions']);

        array_push($data['roles'], ...array_diff($roles , array_keys($data['roles'])));
        array_push($data['permissions'], ...array_diff( $permissionsInRoles , array_keys($data['permissions'])));

        $data['roles'] = self::convertStringsToArray($data['roles']);
        $data['permissions'] = self::convertStringsToArray($data['permissions']);
        return $data;
    }

    public static function loadFromDataBase($withId = true){
        $roles = DB::table(config('laratrust.tables.roles'))->get(['id', 'name' , 'display_name' , 'description']);
        $permissions = DB::table(config('laratrust.tables.permissions'))->get(['id', 'name' , 'display_name' , 'description']);


        /** @var Collection $rolesPermissionsRaw */
        $rolesPermissionsRaw = DB::table(config('laratrust.tables.permission_role'))->get()->map(function ($item) use ($permissions , $roles){
            return[
                "role" => $roles->firstWhere("id" , $item->role_id)->name,
                "permission" => $permissions->firstWhere("id" , $item->permission_id)->name
            ];
        });

        $rolesPermissions = [];

        foreach ($rolesPermissionsRaw as $item){
            if ( ! isset( $rolesPermissions[$item['role']]))
                $rolesPermissions[$item['role']] = [];

            $rolesPermissions[$item['role']][] = $item['permission'];
        }

        if ($withId)
            return [
            "permissions" => $permissions->mapWithKeys(function ($item , $key)
            {
                return [$item->name => ['display_name' => $item->display_name, 'description' => $item->description , 'id' => $item->id]];
            })->toArray(),
            "roles" => $roles->mapWithKeys(function ($item , $key)
            {
                return [$item->name => ['display_name' => $item->display_name, 'description' => $item->description, 'id' => $item->id]];
            })->toArray(),
            "rolesPermissions" => $rolesPermissions
        ];
        else
            return [
                "permissions" => $permissions->mapWithKeys(function ($item , $key)
                {
                    return [$item->name => ['display_name' => $item->display_name, 'description' => $item->description]];
                })->toArray(),
                "roles" => $roles->mapWithKeys(function ($item , $key)
                {
                    return [$item->name => ['display_name' => $item->display_name, 'description' => $item->description]];
                })->toArray(),
                "rolesPermissions" => $rolesPermissions
            ];
    }
}
