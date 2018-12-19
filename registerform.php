<?php
include "lib/lib_hash.php";
if (isset($_POST["login"])&&isset($_POST["password"])&&isset($_POST["repeat_password"])){
    if ($_POST["password"]==$_POST["repeat_password"]) {
       $connection_to_db = new MongoClient();
       $collection= $connection_to_db-> testsite-> users;
       $filter=array("login"=> $_POST["login"]);
       $user = $collection->findOne($filter);
       /*Check that login not used*/
       if ($user===null){
            $token= create_token();
            $client = array( 
            "login" => $_POST["login"], 
            "password" => create_password_hash ($_POST["password"]), 
            "token"=> $token
            );
            $collection->insert($client);
            $player = array(
            "login" => $_POST["login"],
            "games_played" => 0,
            "games_won" => 0,
            "rating" => 0
            );
            $collection= $connection_to_db-> testsite-> top;
            $collection->insert($player);
            $connection_to_db->close();
            setcookie ("token", $token);
            header('Location:/testsite.local/index.php');
        }
        else{
            $connection_to_db->close();
            echo "User with the same login already exists!";
        }
    }
    else{
        echo "Check fields password and repeat password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>SSN|reg</title>
	</head>
	<body>
		<form action="registerform.php" method="post" >
            <fieldset>
                <legend>Registration</legend>
                <div align="center">
                    <?php
                        /*Use regular expressions for validate(not use _\. first symbol must be a char*/
                    ?>
                <p><input id="login" name="login" type="text" placeholder="username" maxlength="10" pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{3,}$" required></p>
                    </div>
                <div align="center">
                    <?php
                        /*Use regular expressions for validate(not use _\. && use lowercase and uppercase letters, numbers)*/
                    ?>
                <p><input id="password" name="password" type="password" placeholder="password" maxlength="15" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{4,}$" required ></p>
                    </div>
                <div align="center">
                <p><input id="repeat_password" name="repeat_password" type="password" placeholder="repeat password" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{4,}$" required ></p>
                    </div>
                <div align="center">
                    <button type="submit">OK</button>
                </div>
            </fieldset>
        </form>
	</body>
</html>