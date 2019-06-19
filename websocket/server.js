var websocketserver = require('ws').Server;
var fs = require('fs');
var https = require('https');
var request = require('request');

var cfg = {
    port : 8080,
    key : fs.readFileSync("ssl-cert/privkey.pem"),
    cert : fs.readFileSync("ssl-cert/cert.pem"),
    chain : fs.readFileSync('ssl-cert/chain.pem'),
    requestCert: true,
    rejectUnauthorized: false
};

var processRequest = function(req, res){
    res.writeHead(200);
    res.end("Oh!! It's my ssl Websoket!\n");
}

var app = https.createServer({
    key : cfg.key,
    cert : cfg.cert
},processRequest).listen(cfg.port);

var wss = new websocketserver({
    server:app
});

function originsAllowed(origin){
    return true;
}

function loadP(url,cookie,ws){
    var page = [9999999999999];
    var counter = 1;

    var opts = {
        url : url,
        method : 'GET',
        headers : {
            'Cookie' : cookie
        },
    };
    
    news = setInterval(function(){
        request(opts, function(err, res, body){
            if(!err && res.statusCode == 200){
                console.log(counter +" : "+ page);
                counter++;
                var asd = body.split(",");
                if(asd[0] > page[0]){
                    ws.send(body);
                }
                page = asd;
            }
        });
    },5000);
}

function loadChat(url,target,cookie,ws){
    var Did = 0;
    
    chat = setInterval(function(){
        var opts = {
            url : url+"?DataId="+Did+"&target="+encodeURI(target),
            method : "GET",
            headers : {
                'Cookie' : cookie
            }
        };
    
        request(opts, function(err, res, body){
            if(!err && res.statusCode == 200){
                let id = JSON.parse(body);
                if(id[0][0] == null){
                    clearInterval(chat);
                    return 0;
                }
                if(id.length>1){
                    id = id[id.length-2].id;
                
                    if(id > Did){
                        ws.send(body);
                        Did = id;
                    }
                }
            }
        });
    },1000)
}


var news,chat;

var webUrl = "https://warzone.kro.kr/work.php";
wss.on("connection",function(wssC){
    console.log("Connected...");
    
    wssC.on("message",function(message){
        if(message.indexOf("news")>=0){
            var cookie = message.split(",")[1];
            loadP(webUrl+'?news',cookie,wssC);
        }
        else if(message.indexOf("closeNews")>=0){
            clearInterval(news);
        }
        else if(message.indexOf("chat")>=0){
            var cookie = message.split(",")[1];
            var target = message.split(",")[2];
            loadChat(webUrl,target,cookie,wssC);
        }
        else{
            console.log('Request : '+message);
            wssC.send('Response: '+message);
        }
    });
});
