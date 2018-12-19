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
    $filter = array("game_id" => $_COOKIE["user_status"]);
    $find_game = $collection->findOne($filter);
    if ($find_game["move"]===$user["login"]){
        if ($find_game["player_1"]===$find_game["move"]){
            $user_score= $find_game["score_1"] + $find_game["con_score"];
            $update_game = array( 
                "game_id" => $find_game["game_id"], 
                "game_status" => "waiting", 
                "player_1"=> $find_game["player_1"],
                "player_2"=> $find_game["player_2"],
                "move"=> $find_game["player_2"],
                "score_1"=> $user_score,
                "score_2"=> $find_game["score_2"],
                "con_score"=> 0
            );
            $option = array("upsert" => true);
            $collection ->  update($find_game, $update_game, $option); 
        }
        if ($find_game["player_2"]===$find_game["move"]){
            $user_score= $find_game["score_2"] + $find_game["con_score"]; 
            $update_game = array( 
                "game_id" => $find_game["game_id"], 
                "game_status" => "waiting", 
                "player_1"=> $find_game["player_1"],
                "player_2"=> $find_game["player_2"],
                "move"=> $find_game["player_1"],
                "score_1"=> $find_game["score_1"],
                "score_2"=> $user_score,
                "con_score"=> 0
            );
            $option = array("upsert" => true);
            $collection ->  update($find_game, $update_game, $option); 
        }
        $connection_to_db->close();
        header("Location:/testsite.local/current_game.php");
    }
}
?>