<?php
    session_start();
    if(isset($_SESSION['you']))
        header("Location:./");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <style> 
    @media screen and (max-width:758px){
        ul.navMenu{
            display:none;
        }
    }
    </style>
<?php
    include "./header.php";
    echo @$_SESSION['alert'];
    unset($_SESSION['alert']);
?>
    <section>
        <div class="hello-box"></div>
        <div class="form reg">
            <h2 onclick="location.href='./'">X</h2>
            <h1 class="login">Sign up</h2>
            <form action="./work.php?reg" method="post">
                <input type="text" placeholder=" ID" name='rid' maxlength="15" autofocus required>
                <input type="password" placeholder=" PW" name='rpw' required>
                <input type="text" placeholder=" NickName" name="nick" required>
                <select name="age" id="Age"></select>
				<select name="sex">
					<option value="boy">남성
					<option value="girl">여성
				</select>
                <input type="submit" class="sub" value="회원가입">
            </form>
        </div>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
