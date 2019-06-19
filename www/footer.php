<?php
    if(substr($_SERVER['PHP_SELF'],-10)=="footer.php"){
        header("HTTP/1.0 404 Not Found");
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">';
        echo "\n<html><head>";
        echo "\n<title>404 Not Found</title>";
        echo "\n</head><body>";
        echo "\n<h1>Not Found</h1>";
        echo "\n<p>The requested URL " . $_SERVER["REQUEST_URI"] . " was not found on this server.</p>";
        exit("\n</body></html>");
    } else{
?>
    <script src="./assets/js/main.js"></script>
<?php if(@$_GET['state'] == "chat"){?>
    <script src="./assets/js/chat.js"></script>
<?php }?>
    <footer>
        <p>CopyRight by LJL / PSJ All Right Reversed</p>
    </footer>
<?php }?>
