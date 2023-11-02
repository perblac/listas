<?php

if(isset($_POST['newlist'])) {
    $listId = PlaylistRepository::savePlaylist($_SESSION['listaTemporal']);
    unset($_SESSION['listaTemporal']);
    unset($_SESSION['creandoLista']);
}

if (isset($_POST['saveName'])) {    
    if (!isset($_SESSION['creandoLista'])) {
        $_SESSION['creandoLista'] = 1;
        $temp = [];
        $temp['id'] = 0;
        $temp['name'] = '';
        $temp['user_id'] = $_SESSION['user']->getId();
        $temp['deleted'] = 0;
        $_SESSION['listaTemporal'] = new Playlist($temp);
        $_SESSION['listaTemporal']->setName($_POST['name']);
    } else {
        $_SESSION['listaTemporal']->setName($_POST['name']);
    }
}

if (isset($_GET['discardNewPlaylist'])) {
    unset($_SESSION['creandoLista']);
    unset($_SESSION['listaTemporal']);
}

if(isset($_GET['unfavList'])) {
    PlaylistRepository::unfavPlaylist(PlaylistRepository::getListById($_GET['unfavList']), $_SESSION['user']);
}

if(isset($_GET['favList'])) {
    PlaylistRepository::favPlaylist(PlaylistRepository::getListById($_GET['favList']), $_SESSION['user']);
}

if(isset($_GET['addSong'])) {
    if ($_SESSION['user']->getId() == PlaylistRepository::getListById($_GET['list'])->getCreator()) {
        include('View/addSongView.phtml');
        die;
    } else {
        echo '<script>alert("Vd. no es el propietario de esta lista");</script>';
    }
}

if(isset($_GET['remove'])) {
    if ($_SESSION['user']->getId() == PlaylistRepository::getListById($_GET['list'])->getCreator()) {
        PlaylistRepository::removeSongFromPlaylist($_GET['remove'], $_GET['list']);
    } else {
        echo '<script>alert("Vd. no es el propietario de esta lista");</script>';
    }
}

if(isset($_POST['addThisSong'])) {
    $id_playlist = $_GET['list'];
    if (PlaylistRepository::getListById(($id_playlist))->getCreator() == $_SESSION['user']->getId()) {
        // $id_song = $_POST['idSecreta']; // <- usando datalist sin id y javascript
        /* ------------------ parseando string dividida con unicode ----------------- */
        $strigToParse = $_POST['idSong'];
        $unicodeChar = "\u{200B}";
        $position = strpos($strigToParse, $unicodeChar);
        if ($position !== false) {
            $title = substr($strigToParse, 0, $position - 2); // -2 porque lleva ' -'
            $author = substr($strigToParse, $position + strlen($unicodeChar) + 1); // +1 porque lleva ' '
            $id_song = SongRepository::getIdByTitleAndAuthor($title, $author);
        }
        /* ------------------------------- hasta aqui ------------------------------- */
        PlaylistRepository::addSongToPlaylist($id_song, $id_playlist);
    }
}

if (isset($_GET['deleteList'])) {
    $list = PlaylistRepository::getListById($_GET['deleteList']);
    if ($_SESSION['user']->getId() == $list->getCreator()) {
        PlaylistRepository::deletePlaylist($_GET['deleteList']);
    }
}

if (isset($_POST['addSongToList'])) {
    $id_playlist = $_POST['idPlaylist'];
    if ($id_playlist == 0 || $_SESSION['user']->getId() == PlaylistRepository::getListById($id_playlist)->getCreator()) {
        $id_song = $_POST['idSong'];
        if ($id_playlist == 0 ) {
            $_SESSION['listaTemporal']->addSong(SongRepository::getSongById($id_song));
        }
        else
        PlaylistRepository::addSongToPlaylist($id_song, $id_playlist);
    }
}

if (isset($_GET['createList'])) {
    include('View/newListView.phtml');
    die;
}

if (isset($_SESSION['creandoLista'])) {
    include('View/newListView.phtml');
    die;
}

// if (isset($_GET['explore'])) {
//     include('View/exploreView.phtml');
// }

if(isset($_GET['list'])) {
    include('View/fullView.phtml');
}

?>