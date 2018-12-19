<?php
    $connection_to_db = new MongoClient();
    $collection = $connection_to_db -> testsite -> users;
    $filter = array("token" => $_COOKIE["token"]);
    $user = $collection->findOne($filter);
function check_winner($game){
    $connection_to_db = new MongoClient();
    $collection = $connection_to_db -> testsite -> users;
    $filter = array("token" => $_COOKIE["token"]);
    $user = $collection->findOne($filter);
    if ($game["score_1"]>10 || $game["score_2"]>10){
        $collection = $connection_to_db -> testsite -> top;
        $filter = array("login" => $user["login"]);
        $statistic = $collection->findOne($filter);
        if ($game["move"]!==$user["login"]){
            $new_rating=number_format((100*($statistic["games_won"]+1))/($statistic["games_played"]+1));
            $option = array("upsert" => true);
            $update_stats = array(
            "login" => $user["login"],
            "games_played" => $statistic["games_played"]+1,
            "games_won" => $statistic["games_won"]+1,
            "rating" => $new_rating
            );
            $collection -> update($statistic, $update_stats, $option);
            echo '<div align = "center">You won!</div>';
        }
         if ($game["move"]===$user["login"]){
            $new_rating=number_format((100*($statistic["games_won"]))/($statistic["games_played"]+1));
            $option = array("upsert" => true);
            $update_stats = array(
                "login" => $user["login"],
                "games_played" => $statistic["games_played"]+1,
                "games_won" => $statistic["games_won"],
                "rating" => $new_rating
            );
            $collection -> update($statistic, $update_stats, $option);
            echo '<div align = "center">You lose!</div>';
        }                     
        $connection_to_db->close();
        echo '<div align = "center"><a href="break_create.php">return</a></div>';
    }
}; 
if ($user === null){
    $connection_to_db->close();
    setcookie("token","",time()-3600);
    header("Location:/testsite.local/loginform.php");
}
else{
    $con_score = mt_rand(1, 6);
    $collection = $connection_to_db-> testsite -> games;
    $filter = array("game_id" => $_COOKIE["user_status"]);
    $find_game = $collection->findOne($filter);
    $summary_con_score=$find_game["con_score"]+$con_score;
    check_winner($find_game);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>SSN|idx</title>
	</head>
	<body>
        <fieldset>
        <table width="100%">
         <tr>
            <td width="33%" align="center"><?php echo $find_game["player_1"]?></td>
            <td width="33%" align="center"></td>
            <td width="33%" align="center"><?php echo $find_game["player_2"]?></td>
         </tr>
         <tr>
            <td width="33%" align="center"><?php echo $find_game["score_1"]; ?></td>
            <td width="33%" align="center"></td>
            <td width="33%" align="center"><?php echo $find_game["score_2"]; ?></td>
         </tr>
<?php
    if ($find_game["move"]===$user["login"]){
            echo '<tr>';
            echo '<td width="33%" align="center"></td>';
            echo '<td width="33%" align="center">'.$summary_con_score.'</td>';
            echo '<td width="33%" align="center"></td>';
            echo '</tr>';
            if ($con_score===1){
                if ($find_game["player_1"]===$find_game["move"]){                         
                    $update_game = array( 
                        "game_id" => $find_game["game_id"], 
                        "game_status" => "waiting", 
                        "player_1"=> $find_game["player_1"],
                        "player_2"=> $find_game["player_2"],
                        "move"=> $find_game["player_2"],
                        "score_1"=> $find_game["score_1"],
                        "score_2"=> $find_game["score_2"],
                        "con_score"=> 0
                    );
                    $option = array("upsert" => true);
                    $collection ->  update($find_game, $update_game, $option);                
                }                                                                              
                if ($find_game["player_2"]===$find_game["move"]){                             
                    $update_game = array( 
                        "game_id" => $find_game["game_id"], 
                        "game_status" => "waiting", 
                        "player_1"=> $find_game["player_1"],
                        "player_2"=> $find_game["player_2"],
                        "move"=> $find_game["player_1"],
                        "score_1"=> $find_game["score_1"],
                        "score_2"=> $find_game["score_2"],
                        "con_score"=> 0
                    );
                    $option = array("upsert" => true);
                    $collection ->  update($find_game, $update_game, $option); 
                }                                                                        
                $connection_to_db->close();                                              
                echo '<tr>';
                echo '<td width="33%" align="center"></td>';
                echo '<td width="33%" align="center">"move passed to another player"</td>';
                echo '<td width="33%" align="center"></td>';
                echo '</tr>';
            }
            else{
                $update_con_score= $find_game["con_score"] + $con_score;                   // 
                $update_field = array ('$set' => array("con_score" => $update_con_score));//
                $option = array("upsert" => true);                                       // Mongo: update score on con
                $collection -> update($find_game, $update_field, $option);              //
                $connection_to_db->close();                                            //
                echo '<tr>';
                echo '<td width="33%" align="center"></td>';
                echo '<td width="33%" align="center"><a href="current_game.php">continue</a></td>';
                echo '<td width="33%" align="center"></td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td width="33%" align="center"></td>';
                echo '<td width="33%" align="center"><a href="save_score.php">save</a></td>';
                echo '<td width="33%" align="center"></td>';
                echo '</tr>';
            }
    }
    else {
        echo '<tr>';
        echo '<td width="33%" align="center"></td>';
        echo '<td width="33%" align="center"> wait for your turn to make a move </td>';
        echo '<td width="33%" align="center"></td>';
        echo '</tr>';
    }
}
?>
         </table>
        </fieldset>
    </body>
</html>