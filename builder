
<?php
require_once 'server.php';
require_once ROOT.'/Core/cli/MigrationHundler.builder.php';
require_once ROOT.'/Core/cli/ConfigHundler.builder.php';


if($argc == 1){
    echo "Welcome  to the builder cli tool";
    echo "\n";
    echo "use --help to get available parameters";
}
initConfig(ROOT,$argc ,$argv);
initMigration(ROOT,$argc ,$argv);

?>