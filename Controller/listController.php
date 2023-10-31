<?php
if(isset($_POST['newlist'])) {
    $name = $_POST['name'];
    $creator = $_SESSION['user']->getId();
    $songsIds = $_SESSION['songsIds'];
    $listId = PlaylistRepository::savePlaylist($name,$creator,$songsIds);
    unset($_SESSION['songsIds']);
    unset($_SESSION['creandoLista']);
}

if(isset($_GET['addSong'])) {
    include('View/addSongView.phtml');
    die;
}

if(isset($_POST['addThisSong'])) {
    $id_playlist = $_GET['list'];
    $songTitle = $_POST['songTitle'];
    $id_song = SongRepository::getIdByTitle($songTitle);
    PlaylistRepository::addSongToPlaylist($id_song, $id_playlist);
}

if (isset($_GET['createList'])) {
    if (!isset($_SESSION['creandoLista'])) $_SESSION['creandoLista'] = 1;
    include('View/newListView.phtml');
    die;
}

if (isset($_SESSION['creandoLista'])) {
    include('View/newListView.phtml');
    die;
}

if (isset($_GET['deleteList'])) {
    $list = PlaylistRepository::getListById($_GET['deleteList']);
    if ($_SESSION['user']->getId() == $list->getCreator()) {
        PlaylistRepository::deletePlaylist($_GET['deleteList']);
    }
}

if (isset($_POST['addSongToList'])) {
    $id_playlist = $_POST['idPlaylist'];
    $id_song = $_POST['idSong'];
    PlaylistRepository::addSongToPlaylist($id_song, $id_playlist);
}

if(isset($_GET['full'])) {
    include('View/fullView.phtml');
}

if (isset($_GET['discardNewPlaylist'])) {
    unset($_SESSION['creandoLista']);
    unset($_SESSION['songsIds']);
}

?>