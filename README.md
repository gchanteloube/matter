MATTER FRAMEWORK
================
*Keep your mind*

&nbsp;

Install
-------
- Just download the sources (soon on github!).
- Unzip them in your web server
- Go on localhost/matter/ and check if it's working


&nbsp;

Architecture
------------

                    (master app)              _______________________
                          |                  |                       |
                          |                  |                       |
    https://your-project/[app][.method] ===> |     MATTER KERNEL     | ===> Render 
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
1. **Build your template**. All your templates are inside the struct/templates/ directory.
A template is a simple html file, with a key tag {{app[.method]}} loading your apps at a specific place. 
Your master app will be loaded with your {{current}} key tag, wherever you want.
2. **Build your app**. All details are below. Your app always has to be declared in the struct/apps.xml file.
3. **Develop your app**. Make your business code in your MVC app.

&nbsp;

Build an app using matter.phar
-----------------------------
Matter uses an autonomous app system. It may be your
footer, payment gateway or a welcome container. Each app is based on a MVC pattern. 
Matter easily helps you performing actions on these apps, like creating, adding and so on. 
Use it! This is fast and reliable!

&nbsp;

**Here are some common usages:**

>App creation (or other), use matter.phar
```shell
$> php matter.phar build:app appName
```
>Add a new view to an existing app
```shell
$> php matter.phar build:view appName viewName
```
>Add a new model to an existing app
```shell
$> php matter.phar build:model appName viewName
```
>Add a new controller to an existing app
```shell
$> php matter.phar build:controller appName viewName
```

&nbsp;

Your app is created and generate the MVC pattern, including 4 subdirectories:
- assets *(all app resources)*
- controller *(app controllers)*
- model *(app models)*
- view *(app views)*

&nbsp;

By default, these files (and class name) are generated using the name you used when you created the app. Feel free to rename them. 
Your app is declared in apps.xml file, with a default set of parameters:
- &lt;template&gt;master&lt;/template&gt; *Template used*
- &lt;name&gt;starter&lt;/name&gt; *App name*
- &lt;controller&gt;MyController&lt;/controller> *Controller to call*
- [&lt;title&gt;Your page title&lt;/title&gt;] *Page title (optional)*
- [&lt;description&gt;Your page description&lt;/description&gt;] *Page description (optional)*
- [&lt;favicon&gt;favicon.ico&lt;/favicon&gt;] *Page favicon (optional)*
*Feel free to update these data to better link them to your project.*

&nbsp;

Controller
----------
The controller is the entry door of your app. You can call it using a specific method 
*(section: Call your app)*. Without specifying a method, the "_default()" method will be called.

&nbsp;

**Here are some common usages:**

>Call a model:
```php
$mdl = $this->mdl('MyModel');
$mdl->YourMethod();
```

&nbsp;

>Call a view:
```php
return $this->view('MyView');
```

&nbsp;

>Add data to a view:
```php
$this->view('MyView')->data(array('MyData' => $myData));
```

&nbsp;

>Return a json view (updating a specific div for example):
```php
$this->view('MyView')->json();
```

&nbsp;

>You can pipe these methods:
```php
$this->view('MyView')->data(array('MyData' => $myData))->json();
```

&nbsp;

>For call code previous before controller insertion, redefined _before() method:
```php
public function _before () {
    // This code will be called before controller insertion
}
```
*Same way with _after()*

&nbsp;


View
----
The view is the html render of your app. You have to define a "_default" method to be able to return the html.

&nbsp;

**Here are some common usages**

>Add html:
```php
$this->html('
    My first app :)
');
```

&nbsp;

>Internationalization:
```php
$this->html('
    <label>' . $this->i('welcome', $firstname) . '</label>
');
```
*For more details, read i18n section*

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
*Your js file is autoloaded (supposing a prototype pattern), using an "init" method. If you don\'t want to autoload your js files, just add a "~" 
character before your file name.*

&nbsp;

>Define meta data *(page title, description, image, etc.)*:
```php
$this->title('MyTitle')->description('MyDescription')->image('myImage.png');
```
*Used for SEO and social posts*

&nbsp;

Model
----
The model is what you use to catch data and perform some specific operation in order to not overcharge your controller. 
It will often be the place where you will perform DB requests for example, or launch a bash script. 
You can then return all relevant data to your controller. 
Your queries and all your parameters are protected by your DB methods. The concept of Transaction is used. 
To setup links and further perform calls with your Databases, you just have to declare it in conf/database.ini file. 
We setup postgreSQL or mySQL database connections and are working to setup NoSQL conn too. 
To specify the database type, just update "type_db" in this file.
>Declare a database:
```php
[database_alias]
type_db = "postgres" // type of db
host_db = "8.8.8.8" // host
port_db = "5432" // port to listen to
name_db = "my_database" // name of the db
user_db = "my_user" // username
passwd_db = "my_password" // password
```
*You can print out database error by turning on "db_error" in your conf/environment.ini file.*

&nbsp;

**Here are some common usages:**

>Initialize the database connection:
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
*All query are in specific transaction SQL. If one of them crash, all query will be cancelled. 
If all queries are successful, the execute() method returns an array for each query.*

&nbsp;

>You can pipe these methods:
```php
$db = $this->db('database_alias')->query('Select * from table')->execute();
```

&nbsp;

Call your app
-------------
Now you can call your beautiful app :) 

As a reminder, a page of Matter has one master app, named {{current}} in your 
template, and other apps (for example header, menu, connection, etc.) that are loaded too in your template with {{app[.method]}}. 
To perform a call using a method of your app, just use a one path url pattern : 

&nbsp;

>Path url to call an app and its specific method:
```php
https://your-project/[app][.method]
```
*Then, all linked app will be loaded, and your master app will be called through _default() method in your controller, 
or parameter method*

&nbsp;

Used this way, the whole template will be loaded. 
If you only need to load your master app (without all template apps) in case of a JSON return or an Ajax call, you can use the S/ prefix just before the app name:

&nbsp;

>Call-alone app (Ajax, JSON, etc.):
```php
https://your-project/S/[app][.method]
```

&nbsp;

Utils
-----
You can defined as many Utils classes as you want.
There are located in the struct/dependency/utils/ directory. 
They are autoloaded and you can use them directly, every where.
>Call to your utils method
```php
$data = _u('method', $myData, $otherData, etc.);
```

&nbsp;

i18n
----
You can internationalize your web site. In each app, you have a assets/i18n directory including your locale files. 
You first have to define your locale file.
Use it by changing your url (https://your-project/fr_FR) and all your page will be translated.

&nbsp;

>Define a "fr_FR" locale:
```php
welcome = "Bienvenue sur mon site web"
birthday = "Vous êtes né le @1"
```
*Path : app/myapp/assets/i18n/fr_FR.ini*

&nbsp;

>Use your "fr_FR" locale:
```php
$this->html('
    <h1>' . $this->i('welcome') . '</h1>
    ' . $this->i('birthday', $date) . '
');
```
*https://your-project/fr_FR/welcome*

