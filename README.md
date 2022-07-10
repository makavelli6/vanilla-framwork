# Vanilla-framwork
A simle MVC framework implimentation using php , no additonal 3rd party are used (Vanilla Php);


### Email Support 
- Configuration for email suport  is easy 



<details><summary>Configuration</summary>
<p>Navigate  to App > Config > app.php</p>

```php
   define('Host_', ''); 
    define('SMTP_Auth_', true);
    define('User_Name_', '');
    define('Password_', '');
    define('Port_', 100);
```

<p>Navigate  to App > Config > app.php</p>

```php
   function index(){
		$this->mail->sendEmail(
            'test_user@gmail.com', //message recipient
            'subject', // subject
            'message to be sent' // actual email
            );
        $this->mail->sendEmailHTML(
            'test_user@gmail.com', //message recipient
            'subject', // subject
            '<h1>Test</h1>' // actual email
            );
        $this->mail->sendEmailWithAttachment(
            'test_user@gmail.com', //message recipient
            'subject', // subject
            false,//has html content or true
            '<h1>Test</h1>', // email body or <h1>Test</h1>
            [ 'var/files/test.docs', 'var/files/atachment.docs']
            );
        //
	}
```

</p>
</details>