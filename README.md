MATTER FRAMEWORK
================

Install
-------
- Just download source (soon on github!).
- Unzip in your web server
- Goto on localhost/matter/

Structure
---------
There are only 3 directories
- apps *(your all apps)*
- conf *(your web site configuration)*
- struct *(your web site structure, like templates, dependency, etc.)*

Build an app
------------
Matter use independent app. An app is like a module, autonomous. It may be your
footer, payment gateway or welcome container. Each app is based on MVC pattern.

For create an app, use matter.phar
```shell
$> php matter.phar app:create Welcome
```

Your app directory is created, with 4 subdirectories:
- assets *(all app resources)*
- controller *(app controllers)*
- model *(app models)*
- view *(app views)*

By default, this files (and class name), were created with the same name that your 
app. But you are free for the naming.

Controller
----------
Controller is the entry door for your app. You can call it with specific method 
*(section: Call your app)*, but without method it's "_default()" method which will 
called.

**Below, some frequently usages:**

>Use your model:
```php
$mdl = $this->mdl('MyModel');
$mdl->YourMethod();
```

&nbsp;

>Use your view:
```php
return $this->view('MyView');
```

&nbsp;

>Add data to your view:
```php
$this->view('MyView')->data(array('MyData' => $myData));
```

&nbsp;

>Return a json view:
```php
$this->view('MyView')->json();
```

&nbsp;

>You can pipe these methods:
```php
$this->view('MyView')->data(array('MyData' => $myData))->json();
```

&nbsp;

View
----
View is the html render of your app. You have to define "_default" method for 
return the html.

**Below, some frequently usages:**

>Add html:
```php
$this->html('
    My first app :)
');
```

&nbsp;

>Use data from controller:
```php
$this->html('
    There is a data from controller: ' . $this->d('MyData') . '
');
```

&nbsp;

>Add css or js resources:
```php
$this->css('header.css');
$this->js('header.js');
```
*Your js file is autoloaded, with "init" method or specific method between hook, as 
js('header.js\[myMethod\]). For not autoload your js, just add "~" character behind 
your file name.*

&nbsp;

>Define meta data *(page title, description, image, etc.)*:
```php
$this->title('MyTitle')->description('MyDescription')->image('myImage.png');
```
*Used for referencement and social posts*
