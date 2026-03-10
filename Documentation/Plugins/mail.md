# Vanilla-framwork
A simle MVC framework implimentation using php , no additonal 3rd party are used (Vanilla Php);


### Email Support 
<details><summary>Configuration for email suport  is easy </summary>
<p>Navigate  to App > Config > app.php</p>

```php
   define('Host_', ''); 
    define('SMTP_Auth_', true);
    define('User_Name_', '');
    define('Password_', '');
    define('Port_', 100);
```

<p>Within your controllers methord  use any of the ezamples</p>

```php
//example 1
    $this->mail->sendEmail(
        'test_user@gmail.com', //message recipient
        'subject', // subject
        'message to be sent' // actual email
        );
//example 2
    $this->mail->sendEmailHTML(
        'test_user@gmail.com', //message recipient
        'subject', // subject
        '<h1>Test</h1>' // actual email
    );
//example 3
    $this->mail->sendEmailWithAttachment(
        'test_user@gmail.com', //message recipient
        'subject', // subject
        false,//has html content or true
        'Test', // email body or <h1>Test</h1>
        [ 'var/files/test.docs', 'var/files/atachment.docs'] , //array of  files
    );

```

</p>
</details>