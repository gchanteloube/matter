MATTER FRAMEWORK
================
*Keep your mind*

&nbsp;

Install
-------
- Just download source (soon on github!).
- Unzip in your web server
- Goto on localhost/matter/

&nbsp;

Architecture
------------

                    (master app)              _______________________
                          |                  |                       |
                          |                  |                       |
    http://your-project/[app][.method] ====> |     MATTER KERNEL     | ====> Render 
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


&nbsp;

First step
----------
1. **Build your template**. Your templates are all in struct/templates/ directory.
A template it's a simple html file, with key tag {{app[.method]}} for load your apps in specific place. 
Your master app will loaded with {{current}} key tag, wherever you want.
2. **Build your app**. All details are below. Your app is declared in struct/apps.xml file.
3. **Develop your app**. Make your business code in your MVC app.

&nbsp;

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

&nbsp;

Controller
----------
Controller is the entry door for your app. You can call it with specific method 
*(section: Call your app)*, but without method it's "_default()" method which will 
called.

&nbsp;

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

&nbsp;

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

&nbsp;

Model
----
Model is the keeper of your data. Mostly, is the way to your database. You can collect all relevant data  to your 
controller. Your query and all your parameters are protected by your SGBD method. Transaction concept is used. For use 
a specific database, you just have to declare it in conf/database.ini file. You can use a postgreSQL or mySQL database, 
just change "type_db" in this file.
>Declare a database:
```php
[database_alias]
type_db = "postgres"
host_db = "8.8.8.8"
port_db = "5432"
name_db = "my_database"
user_db = "my_user"
passwd_db = "my_password"
```
*You can print database error with turn on "db_error" in conf/environment.ini file.*

&nbsp;

**Below, some frequently usages:**

>Get a database connection:
```php
$db = $this->db('database_alias');
```

&nbsp;

>Build a query (with parameters):
```php
$db = $this->db('database_alias');
$db->query('Select * from table where field1 = @1 and field2 = @2', $val1, $val2);
```

&nbsp;

>Execute your query:
```php
$db = $this->db('database_alias');
$db->query('Select * from table where field1 = @1 and field2 = @2', $val1, $val2);
$db->execute();
```

&nbsp;

>Multi queries:
```php
$db = $this->db('database_alias');
$db->query('Update table1 set [...]');
$db->query('Update table2 set [...]');
$db->execute();
```
*All query are in specific transaction SQL. If one of them crash, all query will be cancel. If all queries passed, 
execute() method return an array for each query.*

&nbsp;

>You can pipe these methods:
```php
$db = $this->db('database_alias')->query('Select * from table')->execute();
```

&nbsp;

Utils
-----
You can defined much utils class you want, in struct/dependency/utils/ directory. They are autoloaded and you can use 
directly, every where.
>Call your utils method
```php
$data = _u('method', $myData, $otherData, etc.);
```

&nbsp;

i18n
----
TODO