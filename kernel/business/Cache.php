<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cache
 *
 * @author guich
 */
class Cache {

    private static $mPathCache;
    private static $mDuree;
    private $mCtrl;

    public function Cache() {
        $this->mDuree = time() - (3600*2); // 2h de cache
        $this->mPathCache = '../Cache';
    }

    public function estEnCache($_page) {
        $page = $this->mPathCache . '/' . $_page;
        if (file_exists($page) && filemtime($page) > $this->mDuree) {
            return true;
        } else {
            return false;
        }
    }

    public function metEnCache($_action, $_page) {
        $path = $this->mPathCache . '/' . $_action;
        file_put_contents($path, $_page);
    }

    public function getPage($_action) {
        $path = $this->mPathCache . '/' . $_action;
        $contenu = $this->parsePage($path);
        
        return $contenu;
    }
    
    public function parsePage ($path) {
        $_contenu = file_get_contents($path);
        if (is_array($this->mCtrl)) {
            foreach ($this->mCtrl as $app) {
                if ($app["Position"] == 1) {
                    require_once '../Apps/' . $app["Nom"] . '/Controleurs/' . $app["Controleur"] . '.php';
                    $cssInclude .= $this->recupFichierCSS($app["Nom"]);
                    $jsInclude .= $this->recupFichierJS($app["Nom"]);
                    $ctrlApp = new $app["Controleur"]();
                    $retour = Dispatcher::Forward($ctrlApp);

                    $_contenu = str_replace('#{' . $app["Position"] . '}', $retour, $_contenu);
                }
            }
            $_contenu = str_replace('#{Js}', '<script type="text/javascript">' . $jsInclude . '</script>', $_contenu);
            $_contenu = str_replace('#{CSS}', '<style type="text/css">' . $cssInclude . '</style>', $_contenu);
        }
        
        return $_contenu;
    }
    
    public function setMCtrl($mCtrl) {
        $this->mCtrl = $mCtrl;
    }    

    public function recupFichierCSS($nomApp) {
        $txt = '';
        $dossier = '../Apps/' . $nomApp . '/Vues/Css/';
        $flux = opendir($dossier);
        if ($flux) {
            while (false != ($fichier = readdir($flux))) {
                $extension = explode('.', $fichier);
                if ($fichier != '.' && $fichier != '..' && $extension[1] == 'css') {
                    $txt .= Utils::compressCss('../Apps/' . $nomApp . '/Vues/Css/' . $fichier);
                }
            }
        }

        return $txt;
    }

    public function recupFichierJS($nomApp) {
        $txt = '';
        $dossier = '../Apps/' . $nomApp . '/Controleurs/Js/';
        $flux = opendir($dossier);
        if ($flux) {
            while (false != ($fichier = readdir($flux))) {
                $extension = explode('.', $fichier);
                if ($fichier != '.' && $fichier != '..' && $extension[1] == 'js') {
                    $txt .= Utils::compressJS('../Apps/' . $nomApp . '/Controleurs/Js/' . $fichier);
                }
            }
        }

        return $txt;
    }
}

?>
