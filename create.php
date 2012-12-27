<?php

include("config.php");
include("functions.php");


if(isset($_POST['create'])) {

    $error = "";

    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    if(count(explode("@", $email)) != 2 && !empty($email)) {
        $error = "email not valid"; // meh. emails aren't required so only check if @ exists
    }

    if(strlen($username) < 3) {
        $error = "username needs to be at least 3 characters long";
    }

    if(strlen($password) < 6) {
        $error = "password needs to be at least 6 characters long";
    }

    if(strcmp($password, $_POST['passwordconfirm'])) {
        $error = "passwords do not match";
    }

    // no errors. make acct
    if($error == "") {

        $hashset = create_hash($password);
        $pieces = explode(":", $hashset);
        $salt = $pieces[2];
        $hash = $pieces[3];

        $sql = "INSERT INTO `user` (
            `id`,
            `username`,
            `passhash`,
            `salt`,
            `email`,
            `created`,
            `lastip`
        ) VALUES (
            NULL,
            '".mysql_real_escape_string($username)."',
            '".mysql_real_escape_string($hash)."',
            '".mysql_real_escape_string($salt)."',
            '".mysql_real_escape_string($email)."',
            '".time()."',
            '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."'
        )";

        if($mysql->query($sql)) {
            //REDIRECT TO LOGIN
            header("Location: login.php");
            exit;
        } else {
            $r = $mysql->query("SELECT * FROM `user` WHERE `username` = '".mysql_real_escape_string($username)."' LIMIT 1");
            if($r->num_rows > 0) {
                $error = "username already exists";
            } else {
                $error = "database error";
            }
        }
    }


}


htmlHeader("create account - synccit");

?>
<div id="center">

    <span class="error"><?php echo $error; ?></span><br /><br />
    <form action="create.php" method="post">

        <input type="hidden" name="hash" value="<?php echo $hash; ?>" />
        <label for="username">username</label><br />
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" class="text" />
        <br /><br />
        <label for="password">password</label><br />
        <input type="password" id="password" name="password" value="" class="text" />
        <br /><br />
        <label for="passwordconfirm">confirm password</label><br />
        <input type="password" id="passwordconfirm" name="passwordconfirm" value="" class="text" />
        <br /><br />
        <label for="email">email</label><br />
        <input type="text" id="email" name="email" value="<?php echo $email; ?>" class="text" />
        <br /><br />

        <input type="submit" value="create" name="create" class="submit" />

    </form>
</div>
<?php

htmlFooter();