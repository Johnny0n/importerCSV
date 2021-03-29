<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;

class ImporterControllerLIKE extends BaseController
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
            "Style" => "VendorProductCode", //VendorProductCode
            "Item" => "item_code",
            "Carton_Qty" => 'Carton Qty',
            "Price" => 'PiecePrice',
            "Colour" => 'ColorName',
            "Size" => 'SizeCode',
            "Fit" => "fit",
            "DescSmall" => 'ProductName',
            "UK_SoH" => 'UK_SoH',
            "PL_SoH" => "PL_SoH",
            "Next_Delivery" => 'next_delivery',
            "Length" => 'ShippingLength',
            "Width" => "ShippingWidth",
            "Height" => "ShippingHeight",
            "CC" => 'CC',
            "Weight(Kg)" => 'ShippingWeight',
            "EAN13" => "EAN13",
            "DUN14" => 'DUN14',
            "Commodity_Code" => "Commodity_Code",
            "Unit_Of_Sale" => "Unit_Of_Sale",
            "Image_Path" => "LifestyleImage",
            "VendorSkuCode" => "VendorSkuCode",
            "SizeName" => "SizeName",


            "ProductType" => 'DNProductType',
            "Range" => 'Range',
            "Collection" => "Category1",
            "Product" => "Category2",
            "DescriptionBig" => "ProductDescription",
            "Features" => "Features",
            "Supplier" => "Supplier",
            "Brand" => "Brand",
            "ColorPaletteName" => "ColorPaletteName",
            "SizeTable" => "SizeTable",
        ];

        $portwest_like = file_get_contents(storage_path("app/public/X17_portwest.txt")); //import pliku
        $portwest_like = preg_replace('/\s+/', '', $portwest_like); //usunięcie /n new line
        $portwest_like=  explode(",",$portwest_like); //explode przecinka

        $callback = function() use($csv_order_map,$sohGB,$descGB,$portwest_like) {
            $file = fopen('php://output', 'w');

            fputcsv($file, array_values($csv_order_map));
            for($i=1; $i<sizeof($sohGB);$i++){
                $simpleRow = explode(',',$sohGB[$i]);
                $VendorSkuCode = $simpleRow[1].$simpleRow[4].$simpleRow[5];
                $Sizename = $simpleRow[5];
                if (in_array($simpleRow[0],$portwest_like)){ //wypakowywanie warotsci ze zgodnoscia pierwszy z tablicy po zgodnosci z portwest

                    $row = [];
                    $row['Style']  = $simpleRow[0];
                    $row['Item']  = $simpleRow[1];
                    $row['Carton_Qty']  = $simpleRow[2];
                    $row['Price']  = $simpleRow[3];
                    $row['Colour']  = $simpleRow[4];
                    $row['Size']  = $simpleRow[5];
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
                    $row['VendorSkuCode'] =  $VendorSkuCode;
                    $row['SizeName'] =  $Sizename;



                    for ($j=1;$j<sizeof($descGB);$j++){
                        $simpleDescRow = explode(',',$descGB[$j]);
                        if($simpleDescRow[3]==$simpleRow[0]){
                            $row['ProductType']  = $simpleDescRow[0];
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
                        };

                    }
                    $row['Supplier']  = "PortWest";
                    $row['Brand']  = "PortWest";
                    $row['ColorPaletteName']  = "color-values-rgb.csv";
                    $row['SizeTable']  = "example-size-chart.csv";

                    fputcsv($file, $row);
                }

            }
            fclose($file);
        };




        return response()->stream($callback, 200, $headers);

    }}
