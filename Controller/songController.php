<?php
if (isset($_POST['createSong'])) {
    // var_dump($_FILES);
    $title = $_POST['title'];
    $author = $_POST['author'];
    $duration = $_POST['duration'];
    $creator = $_SESSION['user']->getId();
    if (isset($_FILES['mp3file']) && $_FILES['mp3file']['size'] > 0) {
        $directory = 'upload/';
        $filename = $directory . basename($_FILES['mp3file']['name']);
        $mp3file = $_FILES['mp3file']['name'];
        move_uploaded_file($_FILES['mp3file']['tmp_name'], $filename);
        $getID3 = new getID3;
        $mp3song = $getID3->analyze($filename);
        $duration = $mp3song['playtime_seconds'];
    } else {
        $mp3file = "<no file>";
    }
    $data = [];
    $data['id'] = null;
    $data['title'] = $title;
    $data['author'] = $author;
    $data['duration'] = $duration;
    $data['user_id'] = $creator;
    $data['mp3_file'] = $mp3file;
    $newSong = new Song($data);
    $id = SongRepository::saveSong($newSong);
    $newSong->setId($id);
    if (isset($_SESSION['creandoLista'])) {
        // if (isset($_SESSION['songsIds'])) {
        //     $songsIds = $_SESSION['songsIds'];
        // } else {
        //     $songsIds = [];
        // }
        // $songsIds[] = $id;
        // $_SESSION['songsIds'] = $songsIds;
        $_SESSION['listaTemporal']->addSong($newSong);
        header('location: index.php?c=list');
    } else {
        if ($_POST['idPlaylist'] > 0) {
            $id_playlist = $_POST['idPlaylist'];
            PlaylistRepository::addSongToPlaylist($id, $id_playlist);
        }
    }
}

if (isset($_GET['createSong'])) {
    include('View/newSongView.phtml');
    die;
}

?>