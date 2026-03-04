<?php

use Core\Attributes\Field;

class User extends Model
{
    protected static $tableName = 'users';

    #[Field(type: "string", required: true)]
    public string $name;

    #[Field(type: "string", unique: true)]
    public string $email;

    #[Field(type: "int", min: 18)]
    public int $age;
}

?>
