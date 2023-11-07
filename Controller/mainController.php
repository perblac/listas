<?php
/* -------------------------------------------------------------------------- */
/*                               main controller                              */
/* -------------------------------------------------------------------------- */

//load models
require_once('Model/UserClass.php');
require_once('Model/UserRepository.php');
require_once('Model/SongClass.php');
require_once('Model/SongRepository.php');
require_once('Model/PlaylistClass.php');
require_once('Model/PlaylistRepository.php');
require_once('Model/ViewRepository.php');
// Requires https://github.com/JamesHeinrich/getID3 in getid3 directory
require_once('getid3/getid3.php');

//use models
session_start();

if (!empty($_GET['c'])) {
    require("Controller/".$_GET['c']."Controller.php");
}

if (!isset($_SESSION['user']) && !isset($_GET['registroFrm'])) {
    include('View/loginView.phtml');
    die;
}

//load views
include('View/mainView.phtml');

?>