## How To Use

Create a new HTML file and name it *layout.html* and add:

```html
<!DOCTYPE html>
<html>
	<head>
		<title>{% yield title %}</title>
        <meta charset="utf-8">
	</head>
	<body>
    {% yield content %}
    </body>
</html>
```

This is the layout we will use for this example.

Now create the *index.html* file and add:

```twig
{% extends layout.html %}

{% block title %}Home Page{% endblock %}

{% block content %}
<h1>Home</h1>
<p>Welcome to the home page!</p>
{% endblock %}
```

Now give it a try, navigate to the *index.php* file and you should see the output, pretty awesome right?

But what if we want to use variables in our template files? Easy, change the template code in the *index.php* file to:

```php
$this->tempate->view('about.html', [
    'title' => 'Home Page',
    'colors' => ['red','blue','green']
]);
```

And then we can use it as so:

```twig
{% extends layout.html %}

{% block title %}{{ $title }}{% endblock %}

{% block content %}
<h1>Home</h1>
<p>Welcome to the home page, list of colors:</p>
<ul>
    {% foreach($colors as $color): %}
    <li>{{ $color }}</li>
    {% endforeach; %}
</ul>
{% endblock %}
```

What if we want to secure our output? Instead of:

```twig
{{ $output }}
```

Do:

```twig
{{{ $output }}}
```

This will escape the output using the [htmlspecialchars ](https://www.php.net/manual/en/function.htmlspecialchars.php)function.

Extend blocks:

```twig
{% block content %}
@parent
<p>Extends content block!</p>
{% endblock %}
```

Include additional template files:

```twig
{% include forms.html %}
```

If we want to remove all the compiled files we can either delete all the files in the cache directory or execute the following code:

```php
$this->tempate->clearCache();
```

## Additional Settings

Remember to update the *$cache_enabled* and *$cache_path* variables, the caching is currently disabled for development purposes, you can enable this when your code is production ready.

```php
$this->tempate->cache_enabled = false;
$this->tempate->cache_path = '/yourpath/';

```
