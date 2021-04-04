<?php


function create_new_mig($base,$name){
    require_once $base.'/Core/libs/File.php';
    $fileName = '';
    $size = count(scandir($base.'/Migrations'))-2;

    if($size < 10 ){
        //
        $size =$size + 1;
        $fileName = 'm000'.$size.'_'.$name;
    }else if($size > 10 && $size < 100 ){
        $size =$size + 1;
        $fileName = 'm000'.$size.'_'.$name;
    }else if($size > 100 && $size < 1000 ){
        $size =$size + 1;
        $fileName = 'm000'.$size.'_'.$name; 
    }

    File::copy_file($base.'/Core/cli/temp/migration_temp_alter.php',$base.'/Migrations/',$fileName.'.php');
    File::replace_string_in_file($base.'/Migrations/'.$fileName.'.php','tempName',$fileName);
    //File::replace_string_in_file($base.'/Migrations/'.$fileName,'tempName',$fileName);
    


}