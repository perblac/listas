<?php
if (isset($_POST['createSong'])) {
    if (isset($_FILES['mp3file']) && $_FILES['mp3file']['size'] > 0) {
        $files = $_FILES;
    } else {
        $files = null;
    }
    $newSong = SongRepository::addSong($_POST,$files);
    $id = $newSong->saveSong();
    $newSong->setId($id);
    if (isset($_SESSION['creandoLista'])) {
        $_SESSION['listaTemporal']->addSong($newSong);
        header('location: index.php?c=list');
    } else {
        if ($_POST['idPlaylist'] > 0) {
            $id_playlist = $_POST['idPlaylist'];
            PlaylistRepository::addSongToPlaylist($id, $id_playlist);
        }
    }
}

if (isset($_POST['editSong'])) {
    if (isset($_FILES['mp3file']) && $_FILES['mp3file']['size'] > 0) {
        $files = $_FILES;
    } else {
        $files = null;
    }
    $song = SongRepository::getSongById($_POST['id']);
    $song->setTitle($_POST['title']);
    $song->setAuthor($_POST['author']);
    $song->setDuration($_POST['duration']);
    SongRepository::editSong($song,$files);
}

if (isset($_GET['editSongs']) && $_SESSION['user']->getRol() > 1) {
    include ('View/editSongsView.phtml');
    die;
}

?>