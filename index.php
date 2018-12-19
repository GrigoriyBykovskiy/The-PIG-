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
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>SSN|idx</title>
	</head>
	<body>
    <form action="join_game.php" method="post" >
        <fieldset>
        <table width="100%">
         <tr>
            <td width="33%"></td>
            <td width="33%" align="center">
<?php
        echo "Welcome, ".$user["login"], "!";
?>          
            </td>
            <td width="33%" align="right"><a href="logout.php">EXIT</a></td>
         </tr>
         <tr>
            <td width="33%" align="center"></td>
            <td width="33%" align="center">Game pool:</td>
            <td width="33%" align="center"></td>
         </tr>
<?php
    $filter=array("player_2"=>"undefined");
    $collection=$connection_to_db-> testsite -> games;
    $list_of_games=$collection->find($filter);
    while($item=$list_of_games->getNext()){
        echo '<td width="33%" align="center"></td>';
        echo '<td width="33%" align="center"><button type="submit" name="game_id" value="'.$item["game_id"].'">'.$item["player_1"].'</button>';
        echo '<td width="33%" align="center"></td>';
    }
    if ($list_of_games===null){
        echo '<td width="33%" align="center"></td>';
        echo '<td width="33%" align="center">empty</td>';
        echo '<td width="33%" align="center"></td>';
    }
    $connection_to_db->close();
}
?>    
         <tr>
            <td width="33%" align="left"><a href="get_top.php">STATS</a></td>
            <td width="33%" align="center"></td>
            <td width="33%" align="right"><a href="create_game.php">NEW GAME</a></td>
         </tr>
          </table>
        </fieldset>
    </form>
    </body>
</html>