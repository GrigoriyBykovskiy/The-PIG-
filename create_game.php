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
    if (isset($_COOKIE["user_status"])){
        $filter=array("game_id"=> $_COOKIE["user_status"]);
        $find_game = $collection->findOne($filter);
        $connection_to_db->close();
        if ($find_game["player_2"]!=="undefined"){
            header("Location:/testsite.local/current_game.php");
        }
        else{
            echo "<p align='center'> Please, still waiting! </p>";
        }
    }
    else{
        $game_id=uniqid('', true);
        $game = array( 
            "game_id" => $game_id, 
            "game_status" => "waiting", 
            "player_1"=> $user["login"],
            "player_2"=> "undefined",
            "move"=> $user["login"],
            "score_1"=> 0,
            "score_2"=> 0,
            "con_score"=> 0
            );
            $collection->insert($game);
            $connection_to_db->close();
            setcookie ("user_status", $game_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="refresh" content="30;url="Location:/testsite.local/create_game.php"/>
		<title>SSN|create</title>
	</head>
	<body>
        <fieldset>
        <div align="center">
            <p align="center"> <?php echo "Waiting for opponent, ",$user["login"],"!";?> </p>
        <div align="left">
                <a href="break_create.php">break</a>
        </div>
        </fieldset>
    </body>
</html>