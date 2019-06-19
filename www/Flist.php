<?php
    session_start();
    if(!isset($_SESSION['you'])){
        header("Location:./");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Random Chatting</title>
    <?php
		include "./header.php";

		$str = "";
		$que = $con->prepare("select who,(select nick from users where uname=who) nick from friends where uid=:you");
		$que->bindParam(":you",$_SESSION['you']);
		$que->execute();
		while($row=$que->fetch()){
			$str .= "\t\t\t<li class='name'>";
			$str .= "<img src='./assets/user/$row[0]/img/profile.jpg'>";
			$str .= "<p>".$row[1]."</p>";
			$str .="<p class='id'>($row[0])</p></li>\n";
		}
	?>
    <section>
        <div class="choi">
<?php echo @$str;?>
        </div>
	<div id="RBMenu">
            <ul>
                <li class="prof">프로필</li>
                <li class="del">친구 삭제</li>
            </ul>
        </div>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
