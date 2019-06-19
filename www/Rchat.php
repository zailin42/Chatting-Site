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
<?php include "./header.php"; ?>
    <section>
<?php
    if(@$_GET['state']=="chat"){
        $nick = urldecode($_GET['t']);

        $que = $con->prepare("select uname from users where nick=:nick limit 1");
        $que->bindParam(":nick",$nick);
        $que->execute();
        $rraw = $que->fetch();
        $nick = $rraw[0];

        $que = $con->prepare("select * from room where (you=:you and target=:target) or (you=:target and target=:you)");
        $que->bindParam(":you",$_SESSION['you']);
        $que->bindParam(":target",$nick);
        $que->execute();
        $rrow = $que->fetch();

        if(empty($rrow)){
            $que = $con->prepare("insert into room values(0,:you,:target,now())");
            $que->bindParam(":you",$_SESSION['you']);
            $que->bindParam(":target",$nick);
            $que->execute();
        }
?>
        <style>
        @media screen and (max-width:758px){
            header,footer{display:none;}
        }
        </style>
        <div class="chat">
            <div class="color_boxs">
                <div class="color-drop">
                    <div class="col_box" style="background: #AAB2BD;"></div>
                    <div class="col_box" style="background: #f7a2bd;"></div>
                    <div class="col_box" style="background: #967BDC;"></div>
                    <div class="col_box" style="background: #3d5c83;"></div>
                    <div class="col_box" style="background: #6c89ad;"></div>
                    <div class="col_box" style="background: #a0d468;"></div>
                    <div class="col_box" style="background: #FFCE55;"></div>
                    <div class="col_box" style="background: #ed6942;"></div>
                    <div class="col_box" style="background: #df6464;"></div>
                </div>
                <button id="back"><i class="fas fa-arrow-left"></i> 대화 나가기</button>
            </div>

            <div class="info"></div>
                
            <div class="write">
                <form action="javascript:form_send()" method="post" id="Chat_form">
                    <input type="text" placeholder="Type a message..."  name="msg" id="msg" autofocus autocomplete="off" required>
                    <input type="submit" id="sub" value="전송">
                </form>
            </div>
        </div>
<?php }else{
    $que = $con->prepare("select A.uname,A.nick,B.profile from users A inner join uinfo B on A.id=B.id where A.uname in (select you from room where target=:you union select target from room where you=:you)");
    $que->bindParam(":you",$_SESSION['you']);
    $que->execute();
    $str = "";
    while($row=$que->fetch()){
		$str .= "\t\t\t<li class='name'>";
		$str .= "<img src='$row[2]'>";
		$str .= "<p class='nick'>".$row[1]."</p>";
		$str .= "<p class='id'>($row[0])</p></li>\n";
    }
?>
        <div class="choi">
<?php echo @$str;?>
        </div>
        <div id="RBMenu">
            <ul>
                <li class="prof">채팅</li>
                <li class="del">대화 나가기</li>
            </ul>
        </div>
        <?php }?>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
