<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;

class ImporterControllerEXPLODE extends BaseController
{

    public function importer()
    {
        $explode = file_get_contents(storage_path("app/public/files_why.txt")); //import pliku
        $explode=  explode(",",$explode); //explode przecinka

        return $explode;
    }
}
