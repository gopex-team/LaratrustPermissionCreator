<?php

namespace Gopex\LaratrustPermissionCreator\Facades;

class PermissionConfigs
{
    public $files = [];

    public function __construct()
    {
        $this->files[] = config_path() . '/../laratrust/RoleAndPermissions.php';
    }


    public function addFilePath($path){
        $this->files[] = $path;
    }
}
