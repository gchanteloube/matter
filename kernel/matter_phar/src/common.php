<?php

class Unicorn {
    private $action = null;
    private $args = null;

    public function __construct($listArgs) {
        $this->action = $listArgs[1];
        $this->args = array_slice($listArgs, 1, count($listArgs));
    }

    public function launch () {
        switch ($this->action) {
            case 'build:app':
                $appName = ucfirst($this->args[1]);

                // Build app directory
                echo "\nApp [" . $appName . "] building...\n";
                mkdir('Apps/' . $appName);

                // Declare app in app.xml
                $file = fopen('Noyau/Config/Apps.xml', "r+");
                fseek($file, -11, SEEK_END);
                $t = file_get_contents('TApps'); $t = str_replace('#ClassName#', $appName, $t);
                fwrite($file, $t);
                fclose($file);

                // Build controller and directory
                echo "+ Controller\n";
                mkdir('Apps/' . $appName . '/Controleurs');
                $file = fopen('Apps/' . $appName . '/Controleurs/' . $appName . 'Ctrl.php', 'a');
                $t = file_get_contents('TController'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);
                mkdir('Apps/' . $appName . '/Controleurs/Js');
                $file = fopen('Apps/' . $appName . '/Controleurs/Js/' . $appName . '.js', 'a');
                $t = file_get_contents('TControllerJs'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);

                // Build modele and directory
                echo "+ Modele\n";
                mkdir('Apps/' . $appName . '/Modeles');
                $file = fopen('Apps/' . $appName . '/Modeles/' . $appName . 'Mdl.php', 'a');
                $t = file_get_contents('TModele'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);

                // Build view and directory
                echo "+ View\n";
                mkdir('Apps/' . $appName . '/Vues');
                $file = fopen('Apps/' . $appName . '/Vues/' . $appName . 'Vue.php', 'a');
                $t = file_get_contents('TView'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);
                mkdir('Apps/' . $appName . '/Vues/Css');
                $file = fopen('Apps/' . $appName . '/Vues/Css/' . $appName . '.css', 'a');
                $t = file_get_contents('TViewCss'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);

                echo "=> Your app is done! (http://HOST/$appName)\n";
                break;
            case 'build:view':
                $appName = ucfirst($this->args[1]);
                $viewName = ucfirst($this->args[2]);
                echo "\nView [" . $viewName . "] building in App [" . $appName . "]...\n";
                $file = fopen('Apps/' . $appName . '/Vues/' . $viewName . 'Vue.php', 'a');
                $t = file_get_contents('TView'); $t = str_replace('#ClassName#', $viewName, $t);
                fputs($file, $t);
                fclose($file);
                echo "+ View\n";

                echo "=> Your view is done! Have to add it in your controller constructor for use it\n";
                break;
            case 'build:modele':
                $appName = ucfirst($this->args[1]);
                $modeleName = ucfirst($this->args[2]);
                echo "\nModele [" . $modeleName . "] building in App [" . $appName . "]...\n";
                $file = fopen('Apps/' . $appName . '/Modeles/' . $modeleName . 'Mdl.php', 'a');
                $t = file_get_contents('TView'); $t = str_replace('#ClassName#', $modeleName, $t);
                fputs($file, $t);
                fclose($file);
                echo "+ Modele\n";

                echo "=> Your modele is done! Have to add it in your controller constructor for use it\n";
                break;
        }
    }
}