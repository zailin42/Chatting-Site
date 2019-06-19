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
    <title>친추 상태</title>
    <?php
		include "./header.php";

		$str = "";
		$que = $con->prepare("select who,(select nick from users where uname=who) nick from request where target=:you");
		$que->bindParam(":you",$_SESSION['you']);
		$que->execute();
		while($row = $que->fetch()){
			$str .= "<li><p>$row[1]</p><button class='hi fir'><a href='./work.php?Fstate=1&t=$row[0]'><i class='fas fa-check'></i> 수락</a></button>";
            $str .= "<button class='hi'><a href='./work.php?Fstate=0&t=$row[0]'><i class='fas fa-times'></i> 거절</a></button></li>";
		}

		$str2 = "";
		$que = $con->prepare("select target,(select nick from users where uname=target) nick from request where who=:you");
		$que->bindParam(":you",$_SESSION['you']);
		$que->execute();
		while($row = $que->fetch()){
			$str2 .= "<li><p>$row[1]</p><button><a href='./work.php?Fstate=0&t=$row[0]'><i class='fas fa-user-slash'></i> 친추 취소</a></button></li>";
		}
	?>
    <section style="overflow:hidden;">
		<div class="fstate">
			<i class="fas fa-chevron-circle-down down"></i>
            <div class="fstate_box box1" id="fir">
				<p class="take">요청 대기</p>
				<ul class="take">
<?php echo $str2;?>
    			</ul>
			</div>
			<div class="fstate_box box2">
				<p class="send">수락 대기</p>
				<ul>
<?php echo $str;?>
				</ul>
			</div>
		</div>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
