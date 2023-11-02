<?php
/* -------------------------------------------------------------------------- */
/*                              control principal                             */
/* -------------------------------------------------------------------------- */

//cargar modelos
require_once('Model/UserClass.php');
require_once('Model/UserRepository.php');
require_once('Model/SongClass.php');
require_once('Model/SongRepository.php');
require_once('Model/PlaylistClass.php');
require_once('Model/PlaylistRepository.php');
require_once('Model/ViewRepository.php');

require_once('getid3/getid3.php');

session_start();

if (!empty($_GET['c'])) {
    // if ($_GET['c'] = 'user') {
    //     require('Controller/userController.php');
    // }
    // if ($_GET['c'] = 'list') {
    //     require('Controller/listController.php');
    // }
    // if ($_GET['c'] = 'song') {
    //     require('Controller/songController.php');
    // }
    // if ($_GET['c'] = 'search') {
    //     require('Controller/searchController.php');
    // }
    require("Controller/".$_GET['c']."Controller.php");
    if (isset($_SESSION['creandoLista'])) {
        include('View/newListView.phtml');
        die;
    }
} else {
    require('Controller/userController.php');
}


//usar modelos


//cargar vistas
include('View/mainView.phtml');

?>