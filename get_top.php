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
        <fieldset>
        <div align = "right"><a href="index.php">RETURN</a></div>
        <table width="100%">
        <caption>The best players</caption>
         <tr>
            <td width="50%" align="center">Username:</td>
            <td width="50%" align="center">Rating:</td>
         </tr>
<?php
    $collection=$connection_to_db-> testsite -> top;
    $list_of_games=$collection->find();
    $key=array("rating"=>-1);
    $list_of_games -> sort($key);
    $list_of_games -> limit(100);
    while($item=$list_of_games->getNext()){
        echo '<tr>';
        echo '<td width="50%" align="center">'.$item["login"].'</td>';
        echo '<td width="50%" align="center">'.$item["rating"].'</td>';
        echo '</tr>';
    }
    if ($list_of_games===null){
        echo '<td width="70%" align="center">empty</td>';
        echo '<td width="30%" align="center">empty</td>';
    }
    $connection_to_db->close();
}
?>
          </table>
        </fieldset>
    </body>
</html>