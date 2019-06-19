<?php
    session_start();
    if(!isset($_SESSION['you'])){
        header("Location:./");
    }
?>
<html>
<head>
    <meta charset="utf-8">
    <title>친구추가</title>
<?php
	include "./header.php";
	
	$que = $con->prepare("select A.uname,A.age,A.nick from users A inner join (select if(you=:you,target,you) target,last_time from room where you=:you or target=:you) B on A.uname=B.target order by B.last_time desc limit 5");
	$que->bindParam(":you",$_SESSION['you']);
	$que->execute();
	
	$str = "";
	while($row = $que->fetch()){
		$str .= "<li><a href='./profile.php?type=2&who=$row[2]'>";
        $str .= "<img src='./assets/user/$row[0]/img/profile.jpg' class='profile'>";
        $str .= "<p class='name'>$row[2] <span class='age'>($row[0])</span></p>";
		$str .= "</a></li>";
	}
?>
    <section style="overflow:hidden">
        <div class="FF">
            <div class="search">
                <form action="#" method="post">
                    <input type="text" name="nick" placeholder=" USER NickName" autofocus autocomplete="off" required id="text">
                    <input type="button" value="검색" class="sub">
                </form>
            </div>
            <div class="info">
                <p class="sug">추천 친구<hr style="width:90%; margin:0 auto; margin-bottom:20px;"> </p>
<?php echo $str;?>
            </div>
        </div>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
