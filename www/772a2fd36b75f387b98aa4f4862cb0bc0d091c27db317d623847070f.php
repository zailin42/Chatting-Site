<?php
    session_start();
    if(!isset($_SESSION['you']) or $_SESSION['you']!="ssumkin"){
        header("HTTP/1.0 404 Not Found");
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">';
        echo "\n<html><head>";
        echo "\n<title>404 Not Found</title>";
        echo "\n</head><body>";
        echo "\n<h1>Not Found</h1>";
        echo "\n<p>The requested URL " . $_SERVER["REQUEST_URI"] . " was not found on this server.</p>";
        exit("\n</body></html>");
    }
?>
<!Doctype html>
<html>
<head>
    <title>Admin</title>
    <meta charset="utf-8">
    <style>
        .controler{
            position:fixed;
            top:100px;
            right:10%;
        }

        .controler > span{
            background:#fff;
            border:1px solid #aaa;
            border-radius:5px;
            padding:5px;
            cursor:pointer;
            text-align:center;
        }

        .manage{
            width: 62%;
            position: relative;
            top:20%;
            left: 50%;
            transform: translate(-50%,0);
            border-radius:5px;
        }

        .manage > table{
            width:100%;
            background:white;
            border-collapse: collapse;
            border: 1px solid;
            border-radius:5px;
        }

        .manage > table tr{
            
            border:1px solid #00000080;
        }

        .manage > table td{
            border-right: 1px solid #00000080;
        }
        
        .manage > table > thead td{
            font-size:18px;
            font-weight:bold;
            color:white;
            height:40px;
            padding-left:10px;
            padding-right:10px; 
        }

        .manage > table > thead td:nth-child(3){
            width:40px;
        }

        .manage > table > tbody td:nth-child(1){
            text-align:center;
        }


        .manage > table > tbody td:nth-child(2){
            border:0px;
            display: inline-block;
            width:100%;
            max-width: 600px;
            margin-bottom:10px;
            padding-left:10px;
            padding-right:10px;
            padding-top:10px;
            white-space: pre-line;
            overflow: hidden;
        }


        .manage > table > tbody td:nth-child(3){
            text-align:center;
            border-left:1px solid #00000080;
        }

        .manage > table > tbody td:nth-child(4){
            text-align:center;
        }

        .manage > table > tbody td:nth-child(5){
            text-align:center;
        }

        .manage > table > thead{
            background:#DB4455;
            text-align:center;
            height:30px;
        }

        .userManage{
            background:#fff;
            width:45%;
            border:1px solid;
            border-radius:5px;
            position: relative;
            top: 30%;
            left: 50%;
            transform: translate(-50%,0);
            margin-bottom:60px;
        }
        
        .userManage > .users > form{
            width:100%;
            height:35px;
            margin-top:10px;
            border-bottom:1px solid #00000033;
        }

        .userManage > .users > form > input{
            width:100px;
            height:26px;
            font-size:20px;
            text-align:center;
            border:none;
            outline:none;
            margin-right:25px;
            border-right:1px solid #00000033;
        }

        .userManage > .users > form > .name{
            width:200px;
            margin-right:25px;
            padding-left:10px;
            border-right:1px solid rgba(0, 0, 0, 0.2);
            user-select:none;
        }

    @media screen and (max-width: 1000px){
        .controler{
            top:110px;
        }
       
        .manage{
            width:100%;
            max-width:550px;
        }
        .manage > table > tbody td:nth-child(2){
            width:100%;
            max-width:250px;
            margin-bottom:10px;
        }

        .userManage{
            width:100%;
            border-radius:0px;
            max-width:550px;
            margin-bottom:70px;
        }
        
        .userManage > .users > form > input{
            width:90px;
            margin-right:10px;
        }

        .userManage > .users > form > .name{
            width:100px;
        }    
    
        .manage > table > thead td:nth-child(5){
            display:none;
        }

        .manage > table > tbody td:nth-child(5){
            display:none;
        }

    }


    </style>
<?php
    include "./header.php";
    $con = new PDO("mysql:host=localhost;dbname=ran_chat","root","adminroot");
    $que = $con->prepare("select * from decla order by id desc");
    $que->execute();

    $str = "";
    foreach($que as $row){
        $str .= "\t\t\t\t\t<tr>";
        $str .= "\n\t\t\t\t\t\t<td>$row[0]</td>";
        $str .= "\n\t\t\t\t\t\t<td>$row[2]</td>";
        $str .= "\n\t\t\t\t\t\t<td>$row[1]</td>";
        $str .= "\n\t\t\t\t\t\t<td>$row[3]</td>";
        $str .= "\n\t\t\t\t\t\t<td>$row[4]</td>";
        $str .= "\n\t\t\t\t\t</tr>\n";
    }

    $que = $con->prepare("select nick,alert from users");
    $que->execute();

    $sstr = "";
    foreach($que as $row){
        $sstr .= "<form action='./work.php?UAlert' method='post'>";
        $sstr .= "<input type='text' value='$row[0]' name='user' class='name'>";
        $sstr .= "<input type='number' value='$row[1]' name='alert' min='0' required>";
        $sstr .= "<input type='submit' value='submit'>";
        $sstr .= "</form>";
    }
?>
    <section>
        <div class="controler">
            <span id="dc"><a href="#man">신고란</a></span>
            <span id="uc"><a href="#userser">유저관리</a></span>
        </div>
        <div class="manage" id="man">
            <table>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>사유</td>
                        <td>타입</td>
                        <td>신고자</td>
                        <td>시간</td>
                    </tr>
                </thead>
                <tbody>
<?php echo $str; ?>
                </tbody>
            </table>
        </div>
        <div class="userManage" id="userser">
            <div class="users">
<?php echo $sstr; ?>
            </div>
        </div>
    </section>
<?php include "./footer.php"; ?>
</html>
