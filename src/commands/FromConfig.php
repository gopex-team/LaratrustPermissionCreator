<?php

namespace Gopex\LaratrustPermissionCreator\commands;

use App\Models\Permission;
use Gopex\LaratrustPermissionCreator\Facades\PermissionRoleFacade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FromConfig extends Command
{
    protected $signature = "laratrust:from-config";
    protected $description = "create or update all role and permission from config file";


    private function getNeedUpdateItem($array1, $array2, $keys=['display_name', 'description']){
        $ret = [];
        foreach ($array1 as $index => $value) {
            $isDifferent = false;

            foreach ($keys as $key){
                try {
                    $one = is_array($value[$key]) ? $value[$key][0] : $value[$key];
                    $two = is_array($array2[$index][$key]) ? $array2[$index][$key][0] : $array2[$index][$key];
                    $isDifferent = $isDifferent || $one != $two;
                }catch (\Exception $e){}
            }

            if ($isDifferent) $ret[$index] = $value;
        }

        return $ret;
    }

    private function isTwoArrayIsEqual($one, $two){
        $one = array_unique($one);
        $two = array_unique($two);

        return count(array_diff($one , $two)) + count(array_diff($two , $one)) != 0;
    }

    private function getNeedUpdate($one, $two){
        $keys = array_unique([...array_keys($one), ...array_keys($two)]);
        $ret = [];

        foreach ($keys as $key){
            if (array_key_exists($key , $one)){
                if (array_key_exists($key , $two)){
                    if ($this->isTwoArrayIsEqual($one[$key] , $two[$key])){
                        $ret[$key] = $two[$key];
                    }
                }else{
                    $ret[$key] = [];
                }
            }else{
                $ret[$key] = $two[$key];
            }
        }

        return $ret;
    }


    private function selectLast($a){
        if(is_array($a)){
            return $a[count($a) - 1];
        }
        return $a;
    }

    private function makeForCreate($array){
        $ret = [];
        $time = now()->toAtomString();
        $display_name = $this->selectLast($value['display_name'] ?? '');
        $description = $this->selectLast($value['description'] ?? '');


        foreach ($array as $key => $value){
            $ret[] = [
              "name" => $key,
              "display_name" => $display_name,
              'description' => $description,
              'created_at' => $time,
              'updated_at' => $time,
            ];
        }

        return $ret;
    }

    private function replaceArrayKeyWithId($array, $ids){
        $ret = [];
        foreach ($array as $key => $value){
            if (array_key_exists($key , $ids)){
                $ret[$ids[$key]['id']] = $value;
            }
        }

        return $ret;
    }

    private function replaceArrayValuesWithId($array, $ids){
        $ret = [];
        foreach ($array as $key => $value){
            $ret[$key] = [];
            foreach ($value as $v){
                if (array_key_exists($v , $ids)){
                    $ret[$key][] = $ids[$v]['id'];
                }
            }
        }

        return $ret;
    }

    private function ConvertToTuple($array){
        $ret = [];
        foreach ($array as $key => $value){
            foreach (array_unique($value) as $v){
                $ret[] = [
                    config('laratrust.foreign_keys.role') => $key,
                    config('laratrust.foreign_keys.permission') => $v
                ];
            }
        }

        return $ret;
    }

    public function handle()
    {
        // load all data from files
        $fileData = PermissionRoleFacade::loadFromFiles();
        $fileData = PermissionRoleFacade::optimizeData($fileData);

        // load data from database
        $DBData = PermissionRoleFacade::loadFromDataBase();

        // give new entities
        $newPermissions = array_diff_key($fileData['permissions'] , $DBData['permissions']);
        $newRole = array_diff_key($fileData['roles'] , $DBData['roles']);

        // give updated entities
        $updateRole = $this->getNeedUpdateItem($fileData['roles'] , $DBData['roles']);
        $updatePermission = $this->getNeedUpdateItem($fileData['permissions'] , $DBData['permissions']);

        // give delete entities
        $deletePermissions = array_diff_key($DBData['permissions'] , $fileData['permissions']);
        $deletePermissions = array_map(function ($v){return $v['id'];}, array_values($deletePermissions));

        $deleteRole = array_diff_key($DBData['roles'] , $fileData['roles']);
        $deleteRole = array_map(function ($v){return $v['id'];}, array_values($deleteRole));

        // give update role permissions
        $updatedRoleAndPermissions = $this->getNeedUpdate($DBData['rolesPermissions'] , $fileData['rolesPermissions']);



        // Do Insert
        DB::table(config('laratrust.tables.permissions'))->insert($this->makeForCreate($newPermissions));
        DB::table(config('laratrust.tables.roles'))->insert($this->makeForCreate($newRole));

        // Do Update
        DB::table(config('laratrust.tables.permissions'))
            ->upsert($this->makeForCreate($updatePermission),
                ['name'],
                ['display_name' , 'description']);

        DB::table(config('laratrust.tables.roles'))
            ->upsert($this->makeForCreate($updateRole),
                ['name'],
                ['display_name' , 'description']);

        // load again database
        $DBData = PermissionRoleFacade::loadFromDataBase();

        $updatedRoleAndPermissionsIds = $this->replaceArrayValuesWithId(
            $this->replaceArrayKeyWithId($updatedRoleAndPermissions , $DBData['roles']),
            $DBData['permissions']);


        //Delete roles
        DB::table(config('laratrust.tables.permission_role'))
            ->whereIn(config('laratrust.foreign_keys.role'), array_keys($updatedRoleAndPermissionsIds))
            ->delete();

        DB::table(config('laratrust.tables.permission_role'))
            ->insert($this->ConvertToTuple($updatedRoleAndPermissionsIds));

        DB::table(config('laratrust.tables.permissions'))->whereIn('id' , $deletePermissions)->delete();
        DB::table(config('laratrust.tables.roles'))->whereIn('id' , $deleteRole)->delete();

        echo "Permission Count\n";
        echo "created : " . count($newPermissions);
        echo "\nupdated : " . count($updatePermission);
        echo "\ndeleted : " . count($deletePermissions);

        echo "\n\nRole Count\n";
        echo "created : " . count($newRole);
        echo "\nupdated : " . count($updateRole);
        echo "\ndeleted : " . count($deleteRole);

        echo "\n\nChanged Role's permissions : " . count($updatedRoleAndPermissions);

        echo "\ndone\n";
    }
}
