<?php
    session_start();
    if(!isset($_SESSION['you'])){
        header("Location:./");
    }
?>
<html>
<head>
    <title>Profile</title>
    <meta charset="utf-8">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="0">
<?php
    include "./header.php";
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");

    if(!empty($_SESSION['alert'])){
        echo $_SESSION['alert'];
        $_SESSION['alert']="";
    }

    $_SESSION['target'] = empty($_GET['who'])?$_SESSION['nick']:explode('who=',$_SERVER['REQUEST_URI'])[1];
    $target = empty($_GET['who'])?$_SESSION['target']:explode('who=',$_SERVER['REQUEST_URI'])[1];
    $target = urldecode($target);

    // 유저 정보 확인
    $que = $con->prepare("select A.nick,B.profile,B.background,A.uname from users A inner join uinfo B on A.id=B.id where A.nick=:target");
    $que->bindParam(":target",$target);
    $que->execute();
    $row = $que->fetch();

    if(empty($row)) echo "<script>alert('해당 유저가 존재하지 않습니다.');location.href='./';</script>";

    // 친구 닉넴 가져오기
	$que = $con->prepare("select * from friends where uid=:you and who=:target");
	$que->bindParam(":you",$_SESSION['you']);
	$que->bindParam(":target",$row[3]);
	$que->execute();
	$raw = $que->fetch();

    $str = empty($_GET['state'])?"프로필 관리":"수정 완료";
    $ran = "?rand=".rand(1,100000);
    $row[1].= $ran;
    $row[2] .= $ran;

    echo @$_SESSION['alert'];
    unset($_SESSION['alert']);
?>
    <section style="overflow:hidden;">
        <div class="prof">
            <div class="back">
                <img src="<?php echo @$row[2];?>">
                <div class="blind" id="back">
                    <form action="./work.php?chanImg=1" method="post" class="picForm" enctype="multipart/form-data">
                        <input type="file" name="pic" accept="file_extension|image/*|media_type">
                    </form>
                </div>
            </div>
            <div class="img">
                <img src="<?php echo @$row[1];?>">
                <div class="blind" id="profile">
                    <form action="./work.php?chanImg=2" method="post" class="picForm" enctype="multipart/form-data">
                        <input type="file" name="pic" accept="file_extension|image/*|media_type">
                    </form>
                </div>
            </div>
            <div class="info">
                <p class="name"><?php echo @$row[0];?></p>
				<div class="blind">
					<form action="./work.php?nick" method="post" class="nickForm">
						<input type="text" name="nick" value="" maxlength="12" autofocus autocomplete='off' id="nick" required>
					</form>
				</div>
<?php if($target==$_SESSION['nick']){ ?>
                <span class="control"><?php echo $str;?></span>
<?php }else{ ?>
                <div class="buttons">
<?php if(empty($raw)){?>
                    <span class="button" id="fr"><i class="fas fa-user-plus"></i>친구추가</span>
<?php }else{
                    echo "\t\t\t\t\t<span class='button $raw[2]' id='fd'><i class='fas fa-user-times'></i>친구삭제</span>\n";
}?>
                    <span class="button" id="ch"><i class="fas fa-comments"></i>채팅</span>
                    <span class="button" id="yy"><a href="./Decla.php"><i class="fas fa-exclamation-triangle"></i>신고</a></span>
                </div>
<?php }?>
            </div>
        </div>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
