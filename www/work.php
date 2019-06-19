<?php

function MysqlConnect(){
    return new PDO("mysql:host=localhost;dbname=TABLE_NAME","USER_NAME","PASSWORD");       // DB 연결
}

function alert($msg){
    $str = "<div class='alert'><p>sdhsroot.kro.kr Info:</p><p>";
    $str .= $msg;
    $str .= "<span id='alertCheck'>확인</span></p></div>";
    return $str;
}

function bath($con,$nick){
    $que = $con->prepare("select word from bath where instr(:nick,word)");
    $que->bindParam(":nick",$nick);
    $que->execute();
    return $que;
}

// 로그인
if(isset($_GET['log']) and isset($_POST['id'])){
    $id = $_POST['id'];
    $pw = $_POST['pw'];

    $con = MysqlConnect();

    $que = $con->prepare("update uinfo set last_ip=:ip where id=(select id from users where uname=:id)");
    $que->bindParam(":ip",$_SERVER['REMOTE_ADDR']);
    $que->bindParam(":id",$id);
    $que->execute();

    $que = $con->prepare("select pw,nick from users where uname=:id");   // ID 존재여부 확인
    $que->bindParam(":id",$id);
    $que->execute();
    $que = $que->fetch();

    session_start();
	// 입력 ID 에 해당하느 PW 를 입력했는지 확인  ( sqli 방지 )
    if($que[0] == md5($pw)){
        $_SESSION['you']=$id;
        $_SESSION['nick']=$que[1];
        header("Location:./");
    }
    else{
        $_SESSION['alert'] = alert("ID/PW가 일치하지 않습니다.");
        header("Location:./login.php");
    }
}

// 회원가입
else if(isset($_GET['reg']) and isset($_POST['rpw'])){
    $id = $_POST['rid'];
    $pw = $_POST['rpw'];
    $age = $_POST['age'];
    $nick = $_POST['nick'];
    $sex = $_POST['sex'];
    $str = "";

    session_start();
    $con = MysqlConnect();

    $que = bath($con,$nick);
    $rrow = $que->fetch();

    if(!empty($rrow))
        $str = "닉네임에 부적절한 단어가 포함되어있습니다.";

    $que = $con->prepare("select nick from users where nick=:nick limit 1");
    $que->bindParam(":nick",$nick);
    $que->execute();
    $roow = $que->fetch();

    if(!empty($roow))
        $str = "중복된 닉네임입니다.";

    if(strlen($id)<5 || strlen($pw)<5)
        $str = "ID와 PW는 5자 이상 입력하셔야 합니다.";

    if(!preg_match("/^[0-9]*$/",$age) || ($age < 1900 || $age >date("Y")))
        $str = "생년을 정확히 입력해 주세요.";
        
    if(!preg_match("/^[a-zA-Z0-9]*$/",$id))
        $str = "ID는 알파벳과 숫자만 가능합니다.";

    if(preg_match("/^<|>|&n$/",$nick))
        $str = "닉네임에 < , > 등 특수 부호가 들어갈수 없습니다.";
        
    if(strlen($id) > 12 || strlen($pw) > 12)
        $str = "ID와 PW는 12자 이하이여야 합니다.";

    if(mb_strlen($nick) > 12)
        $str = "닉네임은 최대 12글자 입니다";

    if(strlen($str) > 0){
        $_SESSION['alert']=alert($str);
        header("Location:./register.php");
    }

    else {
        $que = $con->prepare("select pw from users where uname=:id");   // ID 존재여부 확인
        $que->bindParam(":id",$id);
        $que->execute();
        $que = $que->fetch();

        if(empty($que)){
            $pro_path = './assets/user/'.$id.'/img/profile.jpg';
            $back_path = './assets/user/'.$id.'/img/back.jpg';

            $que = $con->prepare("insert into users values(0,:id,md5(:pw),:age,:nick,now(),:sex,0);insert into uinfo values(0,:pro,:back,NULL,NULL);
                                    insert into friends values(0,:id,'ssumkin')");
            $que->bindParam(":id",$id);
            $que->bindParam(":pw",$pw);
            $que->bindParam(":age",$age);
            $que->bindParam(":nick",$nick);
            $que->bindParam(":pro",$pro_path);
            $que->bindParam(":back",$back_path);
            $que->bindParam(":sex",$sex);
            $que->execute();

            $path = "./assets/user/".$id;
            mkdir($path,0700);  chmod($path,0777);
            mkdir($path."/img",0700);  chmod($path."/img/",0777);
            mkdir($path."/file",0700);  chmod($path."file",0777);
            if($sex == "boy") copy("./assets/img/logo.jpg",$path."/img/profile.jpg");
            else if($sex == "girl") copy("./assets/img/logo2.jpg",$path."/img/profile.jpg");
            copy("./assets/img/back.jpg",$path."/img/back.jpg");

            header("Location:./login.php");
        }
        else {
            $_SESSION['alert']=alert("이미 같은 ID가 존재합니다.");
            header("Location:./register.php");
        }
    }
}

// 로그 아웃
else if(isset($_GET['logout'])){
    session_start();

    $con = MysqlConnect();
    $que = $con->prepare("update uinfo set last_visit=date_add(now(),interval -5 minute) where id=(select id from users where uname=:uname)");
    $que->bindParam(":uname",$_SESSION['you']);
    $que->execute();

    session_destroy();
    header("Location:./");
}

/*-------------------------------------------------------------------------------------------------*/

// 상대 매칭
else if(isset($_GET['Match'])){         // 1 전체 매칭   0 또래 매칭   2 이성 매칭		3. 또라이
    session_start();
    $you = @$_SESSION['you'];

    $con = MysqlConnect();

    if($_GET['Match'] == 1)
		$sql = "select count(*) from users where uname!=:you and 
        uname not in (select target from room where you=:you) and 
        uname not in (select you from room where target=:you) and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now())";
    
	else if($_GET['Match'] == 2)
		$sql = "select count(*) from users where uname!=:you and 
        uname not in (select target from room where you=:you) and 
        uname not in (select you from room where target=:you) and 
        age>=(select age-7 from users where uname=:you) and age<=(select age+7 from users where uname=:you) and 
        sex!=(select sex from users where uname=:you and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now()) limit 1)";

    else if($_GET['Match'] == 3)
        $sql = "select count(*) from users where uname!=:you and 
        uname not in (select target from room where you=:you) and 
        uname not in (select you from room where target=:you) and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now()) and alert>=10";

	else
		$sql = "select count(*) from users where uname!=:you and 
        uname not in (select target from room where you=:you) and 
        uname not in (select you from room where target=:you) and 
        age>=(select age-2 from users where uname=:you) and age<=(select age+2 from users where uname=:you) and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now())";

	$que = $con->prepare($sql);
    $que->bindParam(":you",$you);
    $que->execute();
    $row = $que->fetch();

    $randNum = rand(0,$row[0]-1);

    if($_GET['Match'] == 1)
		$sql = "select uname,nick from users where uname!=:you and 
        uname not in (select target from room where you=:you) and uname not in (select you from room where target=:you) and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now()) limit ".$randNum.",1";
    
	else if($_GET['Match'] == 2)
		$sql = "select uname,nick from users where uname!=:you and 
        uname not in (select target from room where you=:you) and 
        uname not in (select you from room where target=:you) and 
        age>=(select age-7 from users where uname=:you) and age<=(select age+7 from users where uname=:you) and 
        sex!=(select sex from users where uname=:you) and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now()) limit ".$randNum.",1";

    else if($_GET['Match'] == 3)
        $sql = "select uname,nick from users where uname!=:you and 
        uname not in (select target from room where you=:you) and 
        uname not in (select you from room where target=:you) and alert>=10 and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now()) limit ".$randNum.",1";

	else
        $sql = "select uname,nick from users where uname!=:you and 
        uname not in (select target from room where you=:you) and 
        uname not in (select you from room where target=:you) and 
        age>=(select age-2 from users where uname=:you) and age<=(select age+2 from users where uname=:you) and 
        id in (select id from uinfo where date_add(last_visit,interval 5 minute) > now()) limit ".$randNum.",1";

	$que = $con->prepare($sql);
    $que->bindParam(":you",$you);
    $que->execute();
    $row = $que->fetch();

    if($row[0]==""){
        $_SESSION['alert']=alert("매칭대상이 없거나 현재 활동중인 유저가 없습니다.");
        header("Location:./");
    }
    else {
        $_SESSION['target']=$row[1];
        $que = $con->prepare("insert into room values(0,:you,:target,now())");
        $que->bindParam(":you",$you);
        $que->bindParam(":target",$row[0]);
        $que->execute();

        header("Location:./Rchat.php?state=chat&t=".$_SESSION['target']);
    }
}

// 채팅 기록 가져오기
else if(isset($_GET['DataId']) and isset($_GET['target'])){
    session_start();
    $_SESSION['target']=urldecode(explode('target=',$_SERVER['REQUEST_URI'])[1]);
    $con = MysqlConnect();

    $que = $con->prepare("select rid from room where rid=(select rid from room where (you=:you and target=(select uname from users where nick=:target)) or (you=(select uname from users where nick=:target) and target=:you)) limit 1");
    $que->bindParam(":you",$_SESSION['you']);
    $que->bindParam(":target",$_SESSION['target']);
    $que->execute();
    $raw = $que->fetch();

	$que = $con->prepare("select id,rid,(select nick from users where uname=sender) sender,msg,time,if(sender=:you,1,0) me,sender uname from chat where rid=:rid and id>:id");
    $que->bindParam(":you",$_SESSION['you']);
    $que->bindParam(":rid",$raw[0]);
    $que->bindParam(":id",$_GET['DataId']);
    $que->execute();

    $info = array();
    foreach($que as $row){
        $info[] = $row;
    }
    $info[] = $raw;

    echo json_encode($info);
}

// 채팅 메시지 저장
else if(isset($_GET['msg']) and isset($_POST['msg'])){
    $msg = $_POST['msg'];
    session_start();

    $con = MysqlConnect();
    $que = bath($con,$msg);

    $info = array();
    foreach($que as $row){
        $info[] = $row;
    }
    for($i=0;$i<count($info);$i++){
        $str = "";
        
        for($j=0;$j<mb_strlen($info[$i][0]);$j++)
            $str .= "*";
        
        $msg = str_ireplace($info[$i][0],$str,$msg);
    }
    

    $que = $con->prepare("insert into chat values(0,(select rid from room where (you=:sender and target=(select uname from users where nick=:target limit 1)) or (you=(select uname from users where nick=:target limit 1) and target=:sender)),:sender,:msg,now());update room set last_time=now() where (you=:sender and target=(select uname from users where nick=:target limit 1)) or (you=:target and target=:sender)");
    $que->bindParam(":sender",$_SESSION['you']);
    $que->bindParam(":target",$_SESSION['target']);
    $que->bindParam(":msg",$msg);
    $que->execute();
}


// 친구 삭제
else if(isset($_GET['FriendDel'])){
    $target = $_GET['FriendDel'];
	$type = $_GET['type'];
    session_start();

    $con = MysqlConnect();
	$sql = "delete from chat where rid=(select rid from room where (you=:sender and target=:target) or (you=:target and target=:sender));delete from room where (you=:sender and target=:target) or (you=:target and target=:sender);";

	if($type == 2)
		$sql .= "delete from friends where (uid=:sender and who=:target) or (uid=:target and who=:sender);";

    $que = $con->prepare($sql);
    $que->bindParam(":sender",$_SESSION['you']);
    $que->bindParam(":target",$target);
    $que->execute();

    if($type == 2) header("Location:./Flist.php");
    
    else header("Location:./Rchat.php");
}

/*----------------------------------------------------------------*/

// 사진 바꾸기
else if(isset($_GET['chanImg'])){
    session_start();

    if($_FILES['pic']['type'] == "image/gif" || $_FILES['pic']['type'] == "image/jpeg" || $_FILES['pic']['type'] == "image/png"){
        if($_GET['chanImg']==1)
            move_uploaded_file($_FILES['pic']['tmp_name'],"./assets/user/".$_SESSION['you']."/img/profile.jpg");
        
        else if($_GET['chanImg']==2)
            move_uploaded_file($_FILES['pic']['tmp_name'],"./assets/user/".$_SESSION['you']."/img/back.jpg");
    }
    else {
        $_SESSION['alert']=alert(".gif/.jpg/.pjpeg/.png 형식의 파일만 올릴수 있습니다.(Now : ".$_FILES['pic']['type'].")");
        header("Location:./");
    }
}

//닉네임 바꾸기
else if(isset($_GET['nick']) and isset($_POST['nick'])){
	session_start();
	$nick = $_POST['nick'];

    if(preg_match("/^<|>|&n$/",$nick)){
        $_SESSION['alert'] = alert("닉네임에 특수문가 들어갈수 없습니다.");
    }
    else if(mb_strlen($nick) > 12 || mb_strlen($nick)<1){
        $_SESSION['alert'] = alert("닉네임은 최대 1~12글자 까지 입력할수 있습니다.");
    }
    else {
	    $con = MysqlConnect();

        $que = bath($con,$nick);
        $rrow = $que->fetch();

        if(!empty($rrow)){
            $_SESSION['alert'] = alert("닉네임에 부적절한 단어가 포함되어있습니다.");
            header("Location:./profile.php");
        } else {

	        $que = $con->prepare("select nick from users where nick=:nick and :nick!=:you");
	        $que->bindParam(":nick",$nick);
            $que->bindParam(":you",$_SESSION['nick']);
	        $que->execute();
	        $raw = $que->fetch();
	
	        if(empty($raw)){
		        $que = $con->prepare("update users set nick=:nick where uname=:you");
		        $que->bindParam(":nick",$nick);
		        $que->bindParam(":you",$_SESSION['you']);
		        $que->execute();
		        $_SESSION['nick'] = $nick;
	        } else {
		        $_SESSION['alert'] = alert("중복된 닉네임 입니다.");
	        }
        }
    }
	header('Location:./profile.php');
}

// 친구 찾기
else if(isset($_GET['getUser'])){
	session_start();
    $user = "%".$_GET['getUser']."%";
	if($user == "%%") $user="";

    $con = MysqlConnect();
    $que = $con->prepare("select A.nick,A.age,S.profile,A.uname from users A inner join uinfo S on A.id=S.id where nick like :user and nick != :you");
    $que->bindParam(":user",$user);
	$que->bindParam(":you",$_SESSION['nick']);
    $que->execute();

    $info = array();
    foreach($que as $row) $info[] = $row;
    echo json_encode($info);
}

// 친구추가
else if(isset($_GET['addF'])){
	$target = $_GET['addF'];
	session_start();

	$con = MysqlConnect();
	$que = $con->prepare("select (select uname from users where nick=:nick limit 1) uname,(select 1 from request where (who=:you and target=uname) or (who=uname and target=:you) limit 1) aru,(select 1 from friends where uid=:you and who=uname limit 1) fri");
	$que->bindParam(":nick",$target);
    $que->bindParam(":you",$_SESSION['you']);
	$que->execute();
	$row = $que->fetch();
    
    if($row[2]){
        $_SESSION['alert'] = alert("이미 친구 입니다.");
        header("Location:./profile.php?who=".$target);
    }

    else if(!$row[1]){
        $que = $con->prepare("insert into request values (0,:you,:target)");
        $que->bindParam(":you",$_SESSION['you']);
        $que->bindParam(":target",$row[0]);
        $que->execute();
        header("Location:./Fstate.php?type=2");
    }

    else header("Location:./Fstate.php?type=2");
}

// 친추 수락/거절
else if(isset($_GET['Fstate'])){		// 1 수락  0 거절
	session_start();
	$target = $_GET['t'];
	$con = MysqlConnect();

	$sql = "delete from request where (who=:target and target=:you) or (who=:you and target=:target)";

	if($_GET['Fstate']==1)
		$sql .= ";insert into friends values(0,:you,:target);
                insert into friends values(0,:target,:you);
                insert into room (rid,you,target,last_time) select 0,:you,:target,now() from room
                where not exists (select * from room 
                where (you=:you and target=:target) or (you=:target and target=:you)) limit 1";

	$que = $con->prepare($sql);
	$que->bindParam(":you",$_SESSION['you']);
	$que->bindParam(":target",$target);
	$que->execute();

	header("Location:./Fstate.php");
}

// 알람
else if(isset($_GET['news'])){
    session_start();

    $con = MysqlConnect();
    $que = $con->prepare("select count(*) from chat where 
                        rid in (select rid from room where you=:you or target=:you)");
    $que->bindParam(":you",$_SESSION['you']);
    $que->execute();
    $row = $que->fetch();

    $que = $con->prepare("select (select nick from users where uname=sender),if(sender=:you,'me','ta') me from chat where 
                        rid in (select rid from room where you=:you or target=:you) 
                        order by id desc limit 1");
    $que->bindParam(":you",$_SESSION['you']);
    $que->execute();
    $raw = $que->fetch();

    echo "$row[0],$raw[0],$raw[1]";
}

// 신고하기
else if(isset($_GET['Decla']) && $_POST['reason']){
    session_start();
    $reason = $_POST['reason'];
    $type = $_POST['type'];

    $reason = str_replace("script>","*",$reason);

    $con = MysqlConnect();
    $que = $con->prepare("insert into decla values(0,:type,:reason,:sender,now())");
    $que->bindParam(":type",$type);
    $que->bindParam(":reason",$reason);
    $que->bindParam(":sender",$_SESSION['you']);
    $que->execute();

    header("Location:./");
}

// 경고 넣기
else if(isset($_GET['UAlert'])){
    session_start();
    if($_SESSION['you'] != "admin") header("Location:./");

    $user = $_POST['user'];
    $alert = $_POST['alert'];
    
    $con = MysqlConnect();
    $que = bath($con,$alert);

    $info = array();
    foreach($que as $row){
        $info[] = $row;
    }
    for($i=0;$i<count($info);$i++){
        $str = "";
        for($j=0;$j<mb_strlen($info[$i][0]);$j++)
            $str .= "*";
        $msg = str_ireplace($info[$i][0],$str,$msg);
    }
    
    $que = $con->prepare("update users set alert=:alert where nick=:nick");
    $que->bindParam(":alert",$alert);
    $que->bindParam(":nick",$user);
    $que->execute();

    header("Location:./");
}

// 욕

else if(isset($_GET['bath'])){
    $bath = $_GET['bath'];

    $con = MysqlConnect();
    $que = $con->prepare("insert into bath values(0,:word)");
    $que->bindParam(":word",$bath);
    $que->execute();
    header("Location:./test.php");
}

// work.php 임의 방문 방지
else header("Location:./");

?>
