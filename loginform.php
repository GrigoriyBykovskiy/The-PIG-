<?php
include "lib/lib_hash.php";
if (!isset($_COOKIE["token"])) {
    if (isset($_POST["login"])&&isset($_POST["password"])){
        $connection_to_db = new MongoClient();
        $collection= $connection_to_db-> testsite -> users;
        $filter=array("login"=> $_POST["login"],"password"=> create_password_hash($_POST["password"]));
        $user = $collection->findOne($filter);
        if ($user!==null){
            $token=create_token();
            $update_field = array ('$set' => array("token" =>  $token));
            $option = array("upsert" => true);
            $collection -> update($user, $update_field, $option);
            $connection_to_db->close();
            setcookie ("token", $token);
            header('Location:/testsite.local/index.php');
        }
        else {
            $connection_to_db->close();
            echo "Access denied! Check your login/password.";
        }
    }
}
else{
    header('Location:/testsite.local/index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>SSN|gate</title>
	</head>
	<body>
		<form method="post" >
            <fieldset>
                <legend>Authorization</legend>
                <div align="center">
                <p><input id="login" name="login" type="text" placeholder="username" maxlength="10" pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{3,}$" required></p>
                    </div>
                <div align="center">
                <p><input id="password" name="password" type="password" placeholder="password" maxlength="15" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{4,}$" required></p>
                    </div>
                <div align="center">
                    <button type="submit">login</button>
                </div>
                <div align="left">
                    <a href="registerform.php">Registration</a>
                </div>
            </fieldset>
        </form>
	</body>
</html>