<?php
    session_start();
    if(!isset($_SESSION['you'])){
        header("Location:./");
    }
?>
<html>
<head>
    <meta charset="utf-8">
    <title>유저 신고</title>
<?php  include "./header.php"; ?>
    <section>
        <div class="decla-box">
            <form action="/work.php?Decla" method="POST">
                <div class="writing">
                    <div class="h3"><h3>user 신고하기<span style="float:right; margin-right:20px; cursor:pointer; font-size:22px;" onclick="location.href='/'">X</span></h3></div>
                    <div class="decla">신고</div>
                    <select class ="reason" name="type">
                        <option value="bath">과도한 욕설 </option>
                        <option value="sexual">성적 수치심</option>
                        <option value="other">기타</option>
                    </select>
                    <div class="decla">신고 사유</div>
                    <div class ="reason">
                        <textarea  id ="write" name="reason" cols="50" rows="15" placeholder="피신고자 닉네임을 반드시 기제하시길 바랍니다. ex) 피신고자 : NickName"></textarea>
                    </div>
                        <button class="sub" type="submit">신고하기</button>
                </div>
            </form>
        </div>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
