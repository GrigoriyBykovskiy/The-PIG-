<?php
    $connection_to_db = new MongoClient();
    $collection= $connection_to_db-> testsite -> users;
    $filter=array("token"=> $_COOKIE["token"]);
    $user = $collection->findOne($filter);
    if ($user===null){
        $connection_to_db->close();
        setcookie("token","",time()-3600);
        header("Location:/testsite.local/loginform.php");
    }
    else{
        $collection= $connection_to_db-> testsite -> games;
        $filter=array("game_id"=> $_COOKIE["user_status"]);
        $find_game = $collection->findOne($filter);
        if ($find_game!==null){
            $for_delete = array("game_id" =>  $_COOKIE["user_status"]);
            $collection -> remove($for_delete);
        }
        $connection_to_db->close();
        setcookie("user_status","",time()-3600);
        header("Location:/testsite.local/index.php");
    }
?>