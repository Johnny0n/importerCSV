<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;

class ImporterControllerColor extends BaseController
{

    public function importer()
    {

        $sohGB = file("./storage/sohGB.csv");
        $descGB = file("./storage/descGB.csv");






        $name = 'import_prod  '.Carbon::today()->format('d.m.Y').'.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=".$name,
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $csv_order_map = [
            "Style" => "id",
            "Item" => "item",
            "Carton_Qty" => 'Carton Qty',
            "Price" => 'Price',
            "Colour" => 'color',
            "Size" => 'size',
            "Fit" => "fit",
            "DescSmall" => 'description',
            "UK_SoH" => 'UK_SoH',
            "PL_SoH" => "PL_SoH",
            "Next_Delivery" => 'next_delivery',
            "Length" => 'length',
            "Width" => "width",
            "Height" => "height",
            "CC" => 'next_delivery',
            "Weight(Kg)" => 'Weight(Kg)',
            "EAN13" => "EAN13",
            "DUN14" => 'DUN14',
            "Commodity_Code" => "Commodity_Code",
            "Unit_Of_Sale" => "Unit_Of_Sale",
            "Image_Path" => "Image_Path",




            "ProductType" => 'ProductType',
            "Range" => 'Range',
            "Collection" => "Collection",
            "Product" => "product",
            "DescriptionBig" => "description",
            "Features" => "Features",
            "Brand" => "Brand",
        ];

//        $callback = function() use($csv_order_map,$sohGB,$descGB) {
//            $file = fopen('php://output', 'w');
            $arrayOfColors= array();
//            fputcsv($file, array_values($csv_order_map));
            for($i=1; $i<sizeof($sohGB);$i++){


                $simpleRow = explode(',',$sohGB[$i]);

                $row = [];
                $row['Style']  = $simpleRow[0];
                $row['Item']  = $simpleRow[1];
                $row['Carton_Qty']  = $simpleRow[2];
                $row['Price']  = $simpleRow[3];
                $row['Colour']  = $simpleRow[4];
                $row['Size']  = $simpleRow[5];

                !in_array($simpleRow[5], $arrayOfColors) ? array_push($arrayOfColors,$simpleRow[5]) : null;

                $row['Fit']  = $simpleRow[6];
                $row['DescSmall']  = $simpleRow[7];
                $row['UK_SoH']  = $simpleRow[8];
                $row['PL_SoH']  = $simpleRow[9];
                $row['Next_Delivery']  = $simpleRow[10];
                $row['Length']  = $simpleRow[11];
                $row['Width']  = $simpleRow[12];
                $row['Height']  = $simpleRow[13];
                $row['CC']  = $simpleRow[14];
                $row['Weight(Kg)']  = $simpleRow[15];
                $row['EAN13']  = $simpleRow[16];
                $row['DUN14']  = $simpleRow[17];
                $row['Commodity_Code']  = $simpleRow[18];
                $row['Unit_Of_Sale']  = $simpleRow[19];
                $row['Image_Path']  = $simpleRow[20];


                for ($j=1;$j<sizeof($descGB);$j++){
                    $simpleDescRow = explode(',',$descGB[$j]);
                    if($simpleDescRow[3]==$simpleRow[0]){
                        $row['ProductType']  = $simpleDescRow[0];
//                        !in_array($simpleDescRow[0], $arrayOfColors) ? array_push($arrayOfColors,$simpleDescRow[0]) : null;  //define your Dump filtr
                        $row['Range']  = $simpleDescRow[1];
                        $row['Collection']  = $simpleDescRow[2];
                        $row['Product']  = $simpleDescRow[4];
                        $row['DescriptionBig']  = $simpleDescRow[5];

                        $features='';
                        for ($k=6;$k<sizeof($simpleDescRow)-1;$k++){
                            $features=$features.$simpleDescRow[$k].',';
                        }
                        $row['Features']  = $features;
                        break;
                    }

                }
                $row['Brand']  = "PortWest";

//                fputcsv($file, $row);

            };
        return $arrayOfColors;
            fclose($file);
//        };



        return response()->stream($callback, 200, $headers);

    }}
