<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Random Chatting</title>
    <?php
        include "./header.php";
        if(isset($_SESSION['alert'])) echo $_SESSION['alert']; $_SESSION['alert']="";
    ?>
    <section>
<?php if(isset($_SESSION['you'])){ ?>
        <div class="choice">
            <a href="./work.php?Match=1" class="ran slc" class="match"><p class="pover"><img src="./assets/img/conversation.png" alt="">&nbsp;랜덤채팅</p></a>
            <a href="./work.php?Match=2" class="sane slc" class="./work.php?Match=2"><p class="pover"><img src="./assets/img/love.png" alt="">&nbsp;이성 찾기</p></a>
            <a href="./work.php?Match=0" class="ran_friend slc" class="#"><p class="pover"><img src="./assets/img/chat.png" alt="">&nbsp;또래 찾기</p></a>
            <a href="./work.php?Match=3" class="crazy slc" class="#"><p class="pover"><img src="./assets/img/crazy.png" alt="">&nbsp;또라이 찾기</p></a>
        </div>
        <span id="about"><i class="fas fa-info-circle"></i> About Us</span>
        <div class="about">
            <div class="blind"></div>
                <div class="blind-box">
                    <p>LetSwitching은 실시간 익명 랜덤채팅 서비스가 주요기능입니다.
                       모르는 사람과 재밌는 대화를 진행해 보세요!<br><br>
                       Developer Email <br>
                       LJL : l1366088121@gmail.com <br>
                       PSJ : rms856@gmali.com <br>
                    </p>
                    <input type="button" id="aboutBut" value="close">
                </div>
        </div>
<?php }else{ ?>
                <div class="hello-box">
                    <p class="welcome"></p>
                    <p class="welcome sec"></p>
                </div>
<?php }?>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
