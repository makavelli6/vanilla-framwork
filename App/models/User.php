<?php
use Core\Attributes\Field;

class User extends Model
{
    #[Field(type: 'string', required: true, unique: true)]
    public $username;

    #[Field(type: 'string', required: false, nullable: true)]
    public $email;
}
