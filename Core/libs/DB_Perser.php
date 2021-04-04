<?php

require_once __DIR__.'/DB/Dailect.php';

class DB_Perser extends Dialect{
    public function __construct() {
        parent::__construct('mysql');
    }


}




?>