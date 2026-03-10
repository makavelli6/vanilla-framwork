<?php
namespace Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public string $type;
    public bool $required;
    public bool $unique;
    public bool $nullable;
    public mixed $default;
    public ?int $min;
    public ?int $max;

    public function __construct(
        string $type = 'string',
        bool $required = false,
        bool $unique = false,
        bool $nullable = true,
        mixed $default = null,
        ?int $min = null,
        ?int $max = null
    ) {
        $this->type = $type;
        $this->required = $required;
        $this->unique = $unique;
        $this->nullable = $nullable;
        $this->default = $default;
        $this->min = $min;
        $this->max = $max;
    }
}
?>
