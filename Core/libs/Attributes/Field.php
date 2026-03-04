<?php
namespace Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public string $type;
    public bool $required;
    public bool $unique;
    public ?int $min;
    public ?int $max;

    public function __construct(
        string $type = 'string',
        bool $required = false,
        bool $unique = false,
        ?int $min = null,
        ?int $max = null
    ) {
        $this->type = $type;
        $this->required = $required;
        $this->unique = $unique;
        $this->min = $min;
        $this->max = $max;
    }
}
?>
