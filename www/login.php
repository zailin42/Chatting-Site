<?php
    session_start();
    if(isset($_SESSION['you']))
        header("Location:./");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>로그인</title>
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
        <div class="form">
            <!--<p>로그인</p>-->
            <h2 onclick="location.href='./'">X</h2>
            <h1 class="login">LOGIN</h1>
            <form action="./work.php?log" method="post">
                <input type="text" placeholder=" ID" name="id" maxlength="15" autofocus required>
                <input type="password" placeholder=" Password" name="pw" required>
                <button class="sub">Login</button>
            </form>
        </div>
    </section>
<?php include "./footer.php"; ?>
</body>
</html>
