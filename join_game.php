<?php
    $connection_to_db = new MongoClient();
    $collection= $connection_to_db -> testsite -> users;
    $filter=array("token" => $_COOKIE["token"]);
    $user = $collection->findOne($filter);
if ($user===null){
    $connection_to_db->close();
    setcookie("token","",time()-3600);
    header("Location:/testsite.local/loginform.php");
}
else{
    $collection=$connection_to_db-> testsite -> games;
    $filter=array("game_id" => $_POST["game_id"]);
    $find_game=$collection->findOne($filter);
    $update_field = array ('$set' => array("player_2" =>  $user["login"]));
    $option = array("upsert" => true);
    $collection -> update($find_game, $update_field, $option);
    $connection_to_db->close();
    setcookie("user_status", $find_game["game_id"]);
    header("Location:/testsite.local/current_game.php");
}
?>