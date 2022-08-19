<?php

class Uploader 
{
   public $fileExtentions = [];
   private $maximumsize = 0;
   private $dir = '';
   private $result = [];

   private $fileName ;
   private $tempName ;
   private $fileSize ;
   private $fileError ;
   private $fileType ;
   private $fileExt ;

   public function __construct( $dir = '') {
       //remove back slash
       $this->dir = $dir;
   }
   public function SetMax($max)
   {
       $this->maximumsize = $max;
   }



   public function Hundle($file)
   {
       $this->fileName = $file['name'];
       $this->tempName = $file['tmp_name'];
       $this->fileSize = $file['size'];
       $this->fileError = $file['error'];
       $this->fileType = $file['type'];
       $temp = explode('.', $fileName);
       $file->fileExt = strtolower(end($temp));

       if(count($this->fileExtentions) > 0){
           if(!in_array($this->fileExt, $this->fileExtentions)){
            $this->result['error'] = 'Formart Not Supported';
            return $this->result;
           }
       }
       if($this->fileError != 0){
           $this->result['error'] = 'Error during Upload';
           return $this->result;
       }

       if($this->fileSize != 0){
        $this->result['error'] = 'File is too Big';
        return $this->result;
       }

       if(!isset($this->result['error'])){
           $fileNameNew = uniqid('', true).''.$this->fileExt;
           $fileDest = STORE.$dir.$fileNameNew;
           move_uploaded_file($this->tempName, $fileDest);
           $this->result['dir'] = $fileDest;
           $this->result['success'] = true;

       }

   }


}
