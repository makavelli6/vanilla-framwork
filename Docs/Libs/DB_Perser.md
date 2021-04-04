# DB_Perser

this  class  extend from  Dialect for Info click  below  

[here]: https://github.com/foo123/Dialect

Api refrence

```php
$perser = new DB_Perser();
```

Create Table 

```php
$sql = $perser->Create('new_table', array(
    'ifnotexists'=> true,
    'columns'=> array(
        array('column'=>'id','type'=>'bigint(20)','isnotnull'=>1,'auto_increment'=>1),
        array('column'=>'name','type'=>'tinytext','isnotnull'=>1, 'default_value'=>"''"),
        array('column'=>'categoryid','type'=>'bigint(20)','isnotnull'=>1,'default_value'=>0),
 array('column'=>'companyid','type'=>'bigint(20)','isnotnull'=>1,'default_value'=>0),
 array('column'=>'fields','type'=>'text','isnotnull'=>1,'default_value'=>"''"),
 array('column'=>'start', 'type'=>'datetime', 'isnotnull'=>1, 'default_value'=>"'0000-00-00 00:00:00'"),
 array('column'=>'end', 'type'=>'datetime','isnotnull'=>1,'default_value'=>"'0000-00-00 00:00:00'"),
 array('column'=>'status','type'=>'tinyint(8) unsigned','isnotnull'=>1, 'default_value'=>0),
 array('column'=>'extra', 'type'=>'text', 'isnotnull'=>1, 'default_value'=>"''")
    ),
   'table'=> array(
        array('collation'=>'utf8_general_ci')
    )
))->sql()
```

Veiw Table

```php
$sql = $perser->Create('new_view', array(
    'view'=> true,
    'ifnotexists'=> true,
    'columns'=> array('id', 'name'),
    'query'=> 'SELECT id, name FROM another_table'
))->sql()
```

