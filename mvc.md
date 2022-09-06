### Controller

Controllers are the driving force of a Vannilla Framework. Segments of the URL are directly mapped to a class and function. ie

```php
class  Api extends Controller {	
	function __construct()
	{
		parent::__construct();        
	}
    function index(){
        echo('Welcome to the Api')
    }

    function route2($name){
        echo('Hi, my name is '.$name)
    }
    
}
```
will map to **example.com/api/** and **example.com/api/route2/param** respectivly


###  Views 
In Vanilla a view is simply a web page. They can contain everything a normal web page would include. Views are always loaded by Controllers. 
the view helper is used for

```php
//seting page title
$this->view->title = 'Login';
//loading custom java script 
$this->view->js =array('custom/signin.js', 'Routes/user-route.js'); 
//loading custom css
$this->view->css =array('custom/signin.css'); 
//rendering the actual view
$this->view->render('forms/signin'); 
//to send json  data
$this->view->Json($data);
```
the views are selected based on the file stuctures ie ***forms/signin*** will be found in 

```
└── App
    └── views
        └── forms
            ├── signin.php
```
The preferred way to access data in views is to name the data array and sent  it as shown
```php
$this->view->body=  "Hello World";
$this->view->detail = array('title'=>'My Title','descr'=>'Login Page',);
$this->view->render('main/view');
```
then in the view...
```php
//body can be accessed 
<?php echo $body; ?>

//heading can be accessed as an array
<?php 
echo ($detail->title.'=>'.$detail->descr);
?>
```


### Models
Models are classes that deal with data (usually from a database), a sample database is provided. For example:

```php
class Login_Model extends Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	public  function getArtist($creator_id){
		//perform task
	}
	
}

```
Working with mysql  has been made easyer by extening the pdo class to create the DB connection is made in  the  config file hence connection  is automatic

#### Database Helper 

the database  helper is accessed  using

```php
	$this->db
```
##### Select Fuction
the select methord is used when **selecting multiple** entries in the database. As shown below

```php
public function genreList(){
		return $this->db->select('SELECT genre_id , genre_name , created_on, popularity FROM genre');
	}
```

the select methord is used when **selecting a single** entry from the database. As shown below

```php
public function genreList(){
		return $this->db->select_one('SELECT genre_id , genre_name , created_on, popularity FROM genre');
	}
```

##### Insert Function

the insert methord is used when **inserting single** entries into the database.

**$this->db->insert(string $name,Array $array)**

> $name  - is the table name

> $array - is  an associative  array with keys corresponiding with the column names of the table

For example

```php
$this->db->insert('genre', array(
		'genre_name'=>$data['genre_name'],
		'created_on'=>date('Y-m-d H:i:s'),
		'popularity'=>$data['popularity'],
		'image'=>$data['image']
	));
```

##### Update Function

the update methord is used when **update single** entries in the database.

**$this->db->update(string $name,Array $array,string $where)**

> $name  - is the table name

> $array - is  an associative  array with keys corresponiding with the column names of the table

> $where - is  an string  whith an expression that acts as the selector eg 
```php
$where = "`genre_id`={$data['genre_id']}";
```

For example

```php
public function edit_genre($data){
	$postData = array('genre_name' => $data['genre_name']);
	$this->db->update('genre', $postData, "`genre_id`= {$data['genre_id']}");
}
```

##### Delete Function
the delete methord is used when **deleting a single** entry from the database.

**$this->db->delete(string $name,string $where)**

> $name  - is the table name

> $where - is  an string  whith an expression that acts as the selector

For example

```php
public function edit_genre($data){
	$postData = array('genre_name' => $data['genre_name']);
	$this->db->update('genre', $postData, "`genre_id`= {$data['genre_id']}");
}
```

Plugin($name) - Load a plugin
Helper($name) - Load a helper
redirect($location) - Redirect to a page without having to include the base URL. E.g.
$this->redirect('some_class/some_function');
ex. $this->redirect('some_class/some_function');

