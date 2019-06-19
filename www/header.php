<?php
if(substr($_SERVER['PHP_SELF'],-10)=="header.php"){
    header("HTTP/1.0 404 Not Found");
    echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">';
    echo "\n<html><head>";
    echo "\n<title>404 Not Found</title>";
    echo "\n</head><body>";
    echo "\n<h1>Not Found</h1>";
    echo "\n<p>The requested URL " . $_SERVER["REQUEST_URI"] . " was not found on this server.</p>";
    exit("\n</body></html>");
}else{
session_start();
?>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=no">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="icon" href="./assets/img/logo.png">
    <link rel="stylesheet" href="./assets/css/main.css" type="text/css"> 
    <link rel="stylesheet" href="./assets/css/media.css" type="text/css">
</head>
<body>
    <header>
        <nav <?php if(isset($_SESSION['you'])) echo "class='inlog'";?>>
            <a href="./"><img src="./assets/img/logo.png" class="logo" title="Go to Home"></a>
            <img class="menu" src="./assets/img/menu.png" alt="">
            <ul class="navMenu">
<?php
    if(isset($_SESSION['you'])){
        $con = new PDO("mysql:host=localhost;dbname=ran_chat","root","adminroot");

        $que = $con->prepare("update uinfo set last_visit=now() where id=(select id from users where uname=:uname limit 1)");
        $que->bindParam(":uname",$_SESSION['you']);
        $que->execute();

        $que = $con->prepare("select last_ip from uinfo where id=(select id from users where uname=:uname)");
        $que->bindParam(":uname",$_SESSION['you']);
        $que->execute();
        $raaw = $que->fetch();
        if($raaw[0] != $_SERVER['REMOTE_ADDR']){
            session_destroy();
            header("Location:./");
        }
?>
                <li><a href="./FFriend.php"><span></span>친구추가</a></li>
                <li><a href="./Fstate.php"><span></span>친추상태</a></li>
                <li><a href="./Flist.php"><span></span>친구목록</a></li>
                <li><a href="./Rchat.php"><span></span>채팅목록</a></li>
                <li><a href="./profile.php"><span></span>프로필</a></li>
<?php  if($_SESSION['you'] == "ssumkin") echo "<li><a href='./772a2fd36b75f387b98aa4f4862cb0bc0d091c27db317d623847070f.php'>Admin</a></li>"; ?>
                <li><a href="./work.php?logout">로그아웃</a></li>
<?php }else{ ?>
                <li class="log nolog"><a href="./login.php">로그인</a></li>
                <li class="reg nolog"><a href="./register.php">회원가입</a></li>
<?php }?>
            </ul>
        </nav>
    </header>
<?php }?>
