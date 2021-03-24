<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use mysql_xdevapi\Exception;
use ZipArchive;
use GuzzleHttp\Client;


class ImporterControllerImg extends BaseController
{

    public function importer(){

        $sohGB = file("./storage/imgx.csv");

//        $image1 = "http://cdn.screenrant.com/wp-content/uploads/Darth-Vader-voiced-by-Arnold-Schwarzenegger.jpg";
//        $image2 = "http://cdn.screenrant.com/wp-content/uploads/Star-Wars-Logo-Art.jpg";
        $files = array();
        for($i=1; $i<sizeof($sohGB);$i++){
            $simpleRow = explode(',',$sohGB[$i]);

            if (sizeof($simpleRow)>1 && !in_array($simpleRow[1],$files)) array_push($files,$simpleRow[1]);

        }

        $tmpFile = tempnam('/tmp', '');
        $client = new Client();

        $zip = new ZipArchive;
       // $zip->open($tmpFile, ZipArchive::CREATE);

        foreach ($files as $file) {
            $imageUrl = preg_replace('/\s+/', '', $file);
            $rawImage = file_get_contents($imageUrl);
            $filename=explode("/",$imageUrl)[sizeof(explode("/",$imageUrl))-1];
            if($rawImage)
            {
                file_put_contents(storage_path("app/public/img/".$filename),$rawImage);
            }
           // $zip->addFromString(basename($file), $fileContent);
        }
        //$zip->close();

        return "Done";

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=file.zip');
        header('Content-Length: ' . filesize($tmpFile));
        readfile($tmpFile);

        unlink($tmpFile);
    }
}
