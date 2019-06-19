var maxId = 0;
var first=1;
var i=0;

function chatting(cho,data) {
    var str = "",i=0;
    var data = JSON.parse(data);

    if((first--)==1)
        str += "<li class='day'>"+decodeURI(location.href.split("t=")[1])+"님과 매칭 되었습니다.</li>";
            
    for (i; i < data.length-1; i++) {
        if(i>0)
            if(data[i-1].time.substr(0,10)<data[i].time.substr(0,10))
                str += "<li class='day'>"+data[i].time.substr(0,10)+"</li>";
                
        str += "<li";
        if(data[i].me == 1){
            str += " class='me";
                    
            if(i<data.length-2 && data[i+1].time.substr(-8,5)==data[i].time.substr(-8,5) && data[i+1].me==1)
                str += " myText";
                    
            str += "'>";
        }

        if(data[i].me == 0){
            if(i>0 && data[i-1].time.substr(-8,5)==data[i].time.substr(-8,5) && data[i-1].me==0)
                str += " class='line'>";
                    
            else {
                str += ">";
                str += "<a href='./profile.php?who="+data[i].sender+"'>";
                str += "<img src='./assets/user/"+data[i].uname+"/img/profile.jpg'></a>";
                str += "<p>" + data[i].sender + "</p>";
            }
        }
               
        str += "<p><span class='msg'>"+htmlEncode(data[i].msg)+"</span>";
               
        if(i<data.length-2 && data[i].me==0 && data[i+1].me==0 && data[i].time.substr(-8,5)==data[i+1].time.substr(-8,5));

        else if(i<data.length-2 && data[i].me==1 && data[i+1].me==1 && data[i].time.substr(-8,5)==data[i+1].time.substr(-8,5));
                
        else {
            str += "<span class='time'>";
            str += (data[i].time.substr(-8,2) < 12 ? "a.m " : "p.m ");
            str += data[i].time.substr(-8,5) + "</span>";
        }
        str += "</p></li>";
    }
    if (data.length > 1)
        maxId = data[data.length - 2].id;
           
    if(data.length>1) i=1;
           
    if(data[data.length-1].rid==undefined){
        alert("상대방에게 차단 되었습니다.");
        location.href="./Rchat.php";
    }

    var ChatBox = document.getElementsByClassName("info")[0];
           
    if (cho == 1)
        ChatBox.innerHTML = str;
            
    if (cho == 2)
        ChatBox.insertAdjacentHTML('beforeend', str);
 
    if(str != ""){
        ChatBox.scrollTop = ChatBox.scrollHeight;
        //getMsg();
    }
}

function form_send() {
    var msg = document.getElementById("msg");
    if(msg.value.match(/^[ ]+$/));
    else {
        var formData = document.getElementById("Chat_form");
        var data = new FormData(formData);
        var xml = new XMLHttpRequest();
        xml.open("post", "./work.php?msg");
        xml.send(data);

        document.getElementById("msg").value = "";
    }
}

function gosub(sub){
    return sub;
}
function htmlEncode(str){
    var ele = document.createElement('span');
    ele.appendChild(document.createTextNode(str));
    return ele.innerHTML;
}
