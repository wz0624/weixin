var express = require('express');
//var config = require('config.js');
var IO = require('socket.io');
var app = express();
var server = require('http').Server(app);
var socketIO = IO(server);
// 房间用户名单
var roomInfo = {};
app.get('/', function(req, res){
    res.send('<h1>Welcome Meepo online Server</h1>');
});
socketIO.on('connection', function (socket) {
  // 获取请求建立socket连接的url
  // 如: http://localhost:3000/room/room_1, roomID为room_1
  var url = socket.request.headers.referer;
  var name = 'listid';
  var result = url.match(new RegExp("[\?\&]" + name + "=([^\&]+)","i"));
  if (result == null || result.length < 1){ 
			return false;
  }
  
  var roomID = result[1];   // 获取房间ID
  
  socket.on('join', function (info) {
	if(typeof info.nickname == "undefined" || info.nickname==''){
		return false;
	}
    socket.name = info.openid;
    // 将用户昵称加入房间名单中
    if (!roomInfo[roomID]) {
      roomInfo[roomID] = {};
    }
	if(!roomInfo[roomID].hasOwnProperty(socket.name)) {
			 var temp_info = info.openid+'|'+info.nickname+'|'+info.avatar;
			roomInfo[roomID][socket.name] = temp_info ;
			 socketIO.to(roomID).emit('sys', info.nickname + '加入了直播室', info.avatar);  
	}
    
    socket.join(roomID);    // 加入房间
    // 通知房间内人员
    console.log(roomInfo);
    console.log(info.nickname + '加入了' + roomID);
  });

  socket.on('leave', function () {
    socket.emit('disconnect');
  });

  socket.on('disconnect', function () {
    // 从房间名单中移除
    if (!roomInfo[roomID]) {
      roomInfo[roomID] = {};
    }
	if(roomInfo[roomID].hasOwnProperty(socket.name)) {
            delete roomInfo[roomID][socket.name];
			//socketIO.to(roomID).emit('sys', nickname + '退出了直播室',avatar);
			//console.log(socket.name + '退出了' + roomID);
    }
    socket.leave(roomID);    // 退出房间
    
  });

  // 接收用户消息,发送相应的房间
  socket.on('message', function (msg) {
    // 验证如果用户不在房间内则不给发送
    if(!roomInfo[roomID].hasOwnProperty(socket.name)){
      return false;
    }
    socketIO.to(roomID).emit('msg', info.nickname, msg);
  });
   socket.on('dashang', function (msg) {
		console.log(msg);
		if(!roomInfo[roomID].hasOwnProperty(socket.name)){
		  return false;
		}
		var split = roomInfo[roomID][socket.name].split("|");
		if(split[1] != "undefined"){
			var nickname = split[1];
			msg.avatar = split[2];
		}else{
			msg.avatar = split[2];
			var nickname = socket.name;
		}
		socketIO.to(roomID).emit('dashang',nickname,msg);
	});
	socket.on('gift', function (msg) {
		console.log(msg);
		// 验证如果用户不在房间内则不给发送
		if(!roomInfo[roomID].hasOwnProperty(socket.name)){
		  return false;
		}
		var split = roomInfo[roomID][socket.name].split("|");
		if(split[1] != "undefined"){
			var nickname = split[1];
			msg.avatar = split[2];
		}else{
			msg.avatar = '';
			var nickname = socket.name;
		}
		socketIO.to(roomID).emit('gift',nickname,msg);
	});
});



server.listen(1409, function () {
  console.log('server listening on port 1409');
});