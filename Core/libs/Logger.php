
<?php

class Log{
    public static function Error($str)
    {
        echo "<span style='color: red;'>$str</span><br>";
    }
    public static function Success($str)
    {
        echo "<span style='color: greenyellow; background-color: black;'>$str</span><br>";
    }
    public static function Warning($str)
    {
        echo "<span style='color: gold;'>$str</span><br>";
    }
    
    public static function Info($str)
    {
        echo "<span style='color: cyan; background-color: black;'>$str</span><br>";
    }
}
?>
