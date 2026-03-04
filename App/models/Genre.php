<?php

use Core\Attributes\Field;

class Genre extends Model
{
    protected static $tableName = 'genre';

    #[Field(type: "string", required: true)]
    public string $genre_name;

    #[Field(type: "string")]
    public string $created_on;

    #[Field(type: "int")]
    public int $popularity;

    #[Field(type: "string")]
    public string $image;
}
?>
