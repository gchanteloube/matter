MATTER FRAMEWORK
================
*Keep your mind*

Install
-------
- Just download source (soon on github!).
- Unzip in your web server
- Goto on localhost/matter/

Architecture
------------

                    (master app)              _______________________
                          |                  |                       |
                          |                  |                       |
    http://your-project/[app][.method] ====> |     MATTER KERNEL     | ===> Render 
                                             |                       |
                                             |_______________________|  
                                                        ^
                                                        |
                                                        |
                                                         ---> [template] -----> [load template app]
                                                                  |
         ___________________MVC____________________               |
        |/apps/[app]                               |       (load master app)
        |                                          |              |
        | ---> [controller[.method]] ----> [model] |              |
        |                |                         | <------------
        |                 ---> [view]              |
        |__________________________________________|


First step
----------
1. **Build your template**. Your templates are all in struct/templates/ directory.
A template it's a simple html file, with key tag {{app[.method]}} for load your apps in specific place. 
Your master app will loaded with {{current}} key tag, wherever you want.
2. **Build your app**. All details are below. Your app is declared in struct/apps.xml file.
3. **Develop your app**. Make your business code in your MVC app.

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
$this->js('header.js'); // Or $this->js('header.js[myMethod]');
```
*Your js file is autoloaded, with "init" method or specific method between hook. For not autoload your js, just add "~" 
character behind your file name.*

&nbsp;

>Define meta data *(page title, description, image, etc.)*:
```php
$this->title('MyTitle')->description('MyDescription')->image('myImage.png');
```
*Used for SEO and social posts*

Utils
-----
You can defined much utils class you want, in struct/dependency/utils/ directory. They are autoloaded and you can use 
directly, every where.
>Call your utils method
```php
$data = _u('method', $myData, $otherData, etc.);
```

Database
--------
TODO

i18n
----
TODO