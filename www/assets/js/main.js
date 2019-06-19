var menu = document.getElementsByClassName("menu")[0];
var navMenu = document.getElementsByClassName("navMenu")[0];
var alertNum = 100000000000000000000000000000000000000000;
var longP;

menu.onclick = function(){
    let log = document.getElementsByClassName("log")[0];

    if(navMenu.style.height == "") navMenu.style.height = (navMenu.children.length * 85)+"px";
    else navMenu.style.height = "";
}

function alarm(ws,datas){
    var target = decodeURI(location.href.split("&t=")[1]);
    if(target == "undefined")
        target = "mklasdmlqkwehjnalkshnfjkasrasdqwe";
    
    if(datas[1] != target && datas[2] == 'ta'){
        var n = new Notification('Random_Chatting',{
            body: datas[1]+'와의 채팅에서 읽지 않은 메시지가 있습니다.',
            tag: 'test',
            data: {url: "https://warzone.kro.kr/Rchat.php?state=chat&type=2&t="+datas[1]},
            requireInteraction: true
        });
    }
}
// 푸시알람
Notification.requestPermission();
var chatCount = 1;      // chatting sign

// Connect Websocket
var ws = new WebSocket("wss://warzone.kro.kr:8080");

ws.onopen = function(){
    ws.send(["news",document.cookie]);

    if(location.href.indexOf("?state=chat")>=0){
        var target = decodeURI(location.href.split("&t=")[1]);
        if(target == "undefined")
            target = "mklasdmlqkwehjnalkshnfjkasrasdqwe";
        ws.send(["chat",document.cookie,target]);
    }
}

ws.onmessage = function(evt){
    alarm(ws,evt.data.split(','));

    if(location.href.indexOf("?state=chat")>=0){
        if(chatCount-- == 1) chatting(1,evt.data);
        else chatting(2,evt.data);
    }
}

ws.onerror = function(){}

window.onbeforeunload = function(){
    ws.send("closeNews");
    ws.close();
}


// 채팅 시 실행
if(location.href.indexOf("?state=chat")>=0) {
	var color = document.getElementsByClassName("col_box");
	var info = document.getElementsByClassName("info")[0];
    var colorBox = document.getElementsByClassName("color_boxs")[0];
    var back = document.getElementById("back");
    
    try{
        var way = location.href.split("type=")[1].split("&")[0];
    }catch(err){
        var way=0;
    }

    back.onmousedown = function(){
        if(way == 1) location.href='./Flist.php';
        else if(way == 2) location.href='./Rchat.php';
        else location.href="./";
    }

	for(var i=0; i<color.length;i++){
		color[i].onmousedown = function(){
			var col = this.style.background;
			info.style.backgroundColor = col;
           
            col = col.split(/[(),]/);
            col[0] += "(";
            col[4] += ")";
            col[1] = String(col[1]-20)+",";
            col[2] = String(col[2]-20)+",";
            col[3] = String(col[3]-20);
            col = col.join('');
           
            colorBox.style.backgroundColor = col;
		}
	}
}

// 상대 선택 시 실행
else if(location.href.indexOf("Rchat.php")>=0) {
	// 마우스 좌표에 위치한 유저
    var sign;
    var Target = document.getElementsByClassName("name");  // 친구_블록
    var Menu = document.getElementById("RBMenu");	// 우클릭_메뉴
    
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
    
    navMenu.children[3].style.background="#62615d";

    for (var i = 0; i < Target.length; i++) {
        
        Target[i].onmousedown = function (event,i) {
            sign = this.innerText.split(/[\n()]/);
           
            if(isAndroid || isiOS){
                /*
                longP = setTimeout(function(){
                    console.log("asd");
                    Menu.style.left=event.clientX+"px";
                    Menu.style.top=event.clientY+"px";
                    Menu.style.display="block";
                },200);
                */
            }
            
            if(event.button==0)
				location.href = "./profile.php?type=2&who=" + (sign[0]==""?sign[1]:sign[0]);
            
			else if(event.button==2){
                Menu.style.left=event.clientX+"px";
                Menu.style.top=event.clientY+"px";
                Menu.style.display="block";
            }
            else if(event.button!=2){
                Menu.style.display="none";
            }
        };

        /*Target[i].onmouseup = function(){
            clearTimeout(longP);
        }*/

        Target[i].oncontextmenu = function(){
            window.event.returnValue=false;
            return false;
        };
    }
    Menu.oncontextmenu = function(){
        window.event.returnValue=false;
        return false;
    };
    window.onmousedown = function(event){
        if(event.button!=2) document.getElementById("RBMenu").style.display="none";
    };

    var prof = document.getElementsByClassName("prof")[0];
    var del = document.getElementsByClassName("del")[0];

    prof.onmousedown = function(){
        location.href="./Rchat.php?state=chat&type=2&t="+(sign[1]==""?sign[0]:sign[1]);
    };
    del.onmousedown = function(){
        location.href="./work.php?type=1&FriendDel="+(sign[4]==""?sign[3]:sign[4]);
    };
}

// 친구 리스트
else if(location.href.indexOf("Flist.php") >= 0){
	var Target = document.getElementsByClassName("name");  // 친구_블록
	var Menu = document.getElementById("RBMenu");
	var sign;
    navMenu.children[2].style.background="#62615d";
	
	for(var i=0;i<Target.length;i++){
		Target[i].onmousedown = function(event,i){
			sign = this.innerText.split(/[\n()]/);
            if(event.button==0)
				location.href="./profile.php?type=1&who="+(sign[1]==""?sign[0]:sign[1]);

			else if(event.button==2){
                Menu.style.left=event.clientX+"px";
                Menu.style.top=event.clientY+"px";
                Menu.style.display="block";
            }
            else if(event.button!=2){
                Menu.style.display="none";
            }
		}
	}

	Menu.oncontextmenu = function(){
        window.event.returnValue=false;
        return false;
    };
    window.onmousedown = function(event){
        if(event.button!=2) document.getElementById("RBMenu").style.display="none";
    };

    var prof = document.getElementsByClassName("prof")[0];
    var del = document.getElementsByClassName("del")[0];

    prof.onmousedown = function(){
        location.href="./Rchat.php?state=chat&type=1&t="+(sign[1]==""?sign[0]:sign[1]);
    };
    del.onmousedown = function(){
        location.href="./work.php?type=2&FriendDel="+(sign[4]==""?sign[3]:sign[4]);
    };
}

// 프로필
else if(location.href.indexOf("profile.php")>=0) {
    var who = document.getElementsByClassName("name")[0].innerText;
	var nick = document.getElementById("nick");
	nick.value=who;
    navMenu.children[4].style.background="#62615d";

    if(location.href.indexOf("who=")>=0) {
        var chat = document.getElementsByClassName("button");
        
		for(var i=0;i<chat.length;i++){
			chat[i].onclick = function () {
				if(this.getAttribute('id') == 'ch'){
                    try{
                        let type = location.href.split("type=")[1].split("&")[0];
					    location.href = "./Rchat.php?state=chat&type="+type+"&t=" + who;
                    } catch(err){
					    location.href = "./Rchat.php?state=chat&t=" + who;
                    }
                }
				
				else if(this.getAttribute('id') == 'fr')
					location.href = "./work.php?addF=" + who;

                else if(this.getAttribute('id') == 'fd')
                    location.href = "./work.php?type=2&FriendDel="+this.getAttribute("class").split(" ")[1];
			}
		}
    }
    else{
        var ctrl = document.getElementsByClassName("control")[0];
        ctrl.onclick = function () {
            if(location.href.indexOf("state")<0) location.href = "?state=manage";
            else{
				var formData = document.getElementsByClassName("nickForm")[0];
				var data = new FormData(formData);
				var xml = new XMLHttpRequest();
				xml.open("post", "./work.php?nick");
				xml.send(data);
				location.href = "./profile.php";
			}
        };

        var blind = document.getElementsByClassName("blind");
        var info = document.getElementsByClassName("info")[0];

        if(location.href.indexOf("state")>=0){
            blind[0].style.display="block";
            blind[1].style.display="block";
            blind[2].style.display="block";
            info.style.marginTop="-100px";
        }
        else {
            blind[0].style.display="none";
            blind[1].style.display="none";
            blind[2].style.display="none";
        }

        blind[0].onchange = function(){
            var formData = document.getElementsByClassName("picForm")[0];
            var data = new FormData(formData);
            var xml = new XMLHttpRequest();
            xml.open("post", "./work.php?chanImg=2");
            xml.send(data);
        };
        blind[1].onchange = function(){
            var formData = document.getElementsByClassName("picForm")[1];
            var data = new FormData(formData);
            var xml = new XMLHttpRequest();
            xml.open("post", "./work.php?chanImg=1");
            xml.send(data);
        };

    }
}

// 친구 찾기
else if(location.href.indexOf("FFriend")>=0){
    var text = document.getElementById("text");
	var info = document.getElementsByClassName("info")[0];
    navMenu.children[0].style.background="#62615d";

    text.oninput=function(){
        getInfo(this.value);
    };

    function getInfo(user){
        var str = "";
        xml = new XMLHttpRequest();
        xml.onreadystatechange = function(){
            if(xml.readyState == 4){
                eval("var data = "+xml.responseText);

                for(var i=0;i<data.length;i++) {
                    str += '<li><a href="./profile.php?type=2&who=' + data[i].nick + '">';
                    str += '<img src="'+data[i].profile+'" class="profile">';
                    str += '<p class="name">'+data[i].nick+' <span class="age">('+data[i].uname+')</span></p>';
                    str += '</a></li>';
                }
				info.innerHTML=str;
            }
        };
        xml.open("get","./work.php?getUser="+user);
        xml.send(null);
    }
}

// 친추 상태
else if(location.href.indexOf("Fstate")>=0){
    navMenu.children[1].style.background="#62615d";

    var down = document.getElementsByClassName("down")[0];
    var request = document.getElementsByClassName("box2")[0];
    var sign=1;

    down.onmousedown = function(){
        if(sign == 1){
            request.style.transform="translate(0,100%)";
            down.style.transform="rotate(180deg)";
            sign = 0;
        } else {
            request.style.transform="translate(0,0)";
            down.style.transform="rotate(0deg)";
            sign = 1;
        }
    }

    if(location.href.indexOf("?type=2")>=0 && window.innerWidth < 1000){
        sign = 0;
        request.style.transform="translate(0,100%)";
        down.style.transform="rotate(180deg)";
    }
}

// Register
else if(location.href.indexOf("register")>=0){
    var age = document.getElementById("Age");

    function years(){
        var str = "";
        var d = new Date()
        var d = d.getFullYear();
        
        str += "<option selected>년도</option>";
        for(var i=(d-150);i<=d;i++)
            str += "<option value='"+i+"'>"+i+"</option>";

        age.innerHTML = str;
    }

    years();
}

else{
    var text = ["LS Chat Rooms","Welcome to our Random Chatting Site"];
    var pp = document.getElementsByClassName("welcome");
    var sign,i=0,j=0;
    
    if(pp[0] !== undefined){
        sign = setInterval(function(){
            pp[i].innerHTML += text[i][j];
            j++;
            if(j == text[i].length)
                if(i > 0) clearInterval(sign);
                else {
                    i++;
                    j = 0;
                    pp.innerHTML += "<br>";
                }
        },100);
    }
    var button = document.getElementById("about");
    var abBut = document.getElementById("aboutBut");
    var about = document.getElementsByClassName("about")[0];
    var blind = document.getElementsByClassName("blind")[0];

    if(button !== undefined && button !== null){
        button.onmousedown = function(){
            about.style.display="block";
        }

        blind.onmousedown = function(){
            about.style.display="none";
        }

        abBut.onmousedown = function(){
            about.style.display="none";
        }
    }
}

// index.php ??
var but = document.getElementById("alertCheck");
if(but != null){
    var alarm = document.getElementsByClassName("alert")[0];
    var sign = 1;

    but.onmousedown = function(){
        alarm.style.display="none";
    };

    document.onkeydown = function(event){
        var e = event ||  window.event || arguments.callee.caller.arguments[0];

        if(e.keyCode == 13 && sign == 1){
            alarm.style.display="none";
            sign=0;
        }
    }
}

var hello_box = document.getElementsByClassName("hello-box")[0];
if(hello_box != null){
    if(window.innerWidth > 1000)
        hello_box.style.height = (window.innerHeight - 70) + "px";

    else
        hello_box.style.height = (window.innerHeight - 90) + "px";
}

window.onresize = function(){
    if(hello_box != null){
        if(window.innerWidth > 1000)
            hello_box.style.height = (window.innerHeight - 70) + "px";

        else
            hello_box.style.height = (window.innerHeight - 90) + "px";
    }
}


