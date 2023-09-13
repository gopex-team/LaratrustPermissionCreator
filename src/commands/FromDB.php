<?php

namespace Gopex\LaratrustPermissionCreator\commands;

use App\Models\Permission;
use Gopex\LaratrustPermissionCreator\Facades\PermissionRoleFacade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FromDB extends Command
{
    protected $signature = "laratrust:from-db {path=laratrust/db_export.php}";
    protected $description = "load database permission to a file";


    public function handle()
    {
        // load data from database
        $DBData = PermissionRoleFacade::loadFromDataBase(false);

        $path = config_path() . '/../' . $this->argument("path");

        $value = var_export($DBData, true);
        $value = str_replace('array' , '' ,$value);
        $value = str_replace('(' , '[' ,$value);
        $value = str_replace(')' , ']' ,$value);

        file_put_contents($path, '<?php '.PHP_EOL.'return '.$value.';');


        echo "done";
    }
}
