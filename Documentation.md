# Vanilla-framwork
Vanila architecture bares striking resemblance to Codeigniter. However, being a lightweight, it does not include a lot of the functionality that comes with Codeigniter.

 
## Quick Start
 Requiresments:

<p>Needs the following to run:</p>
<ul>
<li>PHP 7.2 and up</li>
<li>Composer</li>
<li>Database: MySQL</li>
<li>Web Server: Apache (Mod Rewrite, Override)</li>
</ul>

### Installing Vanilla-Fremwork

<p>can be installed by:</p>

<!-- ```bash
$ composer create-project adigah/asmvc project-folder-name -s beta

```
<p>Or</p> -->

```console
$ git clone https://github.com/makavelli6/vanilla-framwork.git

```
Change to the repository directory

```console
$ cd vanilla-framwork
```
Run composer to install any PHP dependencies

```console
$ composer install
```


Displaying Hello World

Goto index.php located in below:

```
└── App
    └── controllers
        ├── index.php
```
Add  or midify the following methord:

```php
function hello(){
	echo "Hello World ";
}

```

Now serve the app by running:

```shell
$ php vanilla init server

```
You should be greeted by display like this while you're accessing **localhost:8000/index/hello**  url:

### URL Structure

By default, URLs in Vanilla are designed to be search-engine and human friendly.The routing in the system is dirrectly dependent of the controllers, Vanilla uses a segment-based approach:

**example.com/class/function/param** By default index.php is hidden in the URL. This is done using the .htaccess file in the root directory.



