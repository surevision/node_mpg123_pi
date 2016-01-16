var querystring = require('querystring');
var proces = require('child_process');
var http = require('http')
var url = require('url');
var PORT = 8888;

var PARAM_LIST = "list"; // param to play by list

var S_NAME = " nodempg ";	// screen name
var CMD_SCREEN = "screen "; // cmd screen
var PARAM_SCREEN_S = " -S "; // create screen session
var PARAM_SCREEN_D = " -d "; // detach screen session
var PARAM_SCREEN_M = " -m "; // solution to "Must be connected to a terminal"
var PARAM_SCREEN_X = " -X "; // kill
var CMD_MPG123 = " mpg123 "; // cmd mpg123
var PARAM_MPG123_LIST = " --list ";	// mpg123 -- list
function spawn_callback(cmd, params, callback_end, callback_data, callback_err) {
	var file = '/bin/sh';
	var args = ['-c', cmd];
	var sCmd = proces.spawn(file, args);
	// data event
	sCmd.stdout.on('data', function(data) {
		callback_data(data);
	});
	// event end
	sCmd.stdout.on('end', function(data) {
		callback_end(data);
	});
	// event error
	sCmd.on('exit', function(code) {
		if (code != 0) {
			callback_err(code);
		}
	});
}
function cb_end(data, func) {
	console.log("cb_end " + data);
	func(data);
}
function cb_data(data) {
	console.log("cb_data " + data);
}
function cb_err(data) {
	console.log("cb_err " + data);
}
function mpg(argv){
	var pre_cmd = CMD_SCREEN + PARAM_SCREEN_S + S_NAME + PARAM_SCREEN_X + "quit"; // stop play session by screen -S session_name -X quit
	var cmd = "";
	console.log(argv);
	if (argv[2] === PARAM_LIST) {
		// play audio by list
		cmd = CMD_SCREEN + PARAM_SCREEN_D + PARAM_SCREEN_M + PARAM_SCREEN_S + S_NAME + CMD_MPG123 + PARAM_MPG123_LIST + argv[3];
	} else {
		// play single audio
		cmd = CMD_SCREEN + PARAM_SCREEN_D + PARAM_SCREEN_M + PARAM_SCREEN_S + S_NAME + CMD_MPG123 + argv[2];
	}
	// exec pre cmd to stop playing session
	spawn_callback(pre_cmd, [], function (data) {
		console.log("cmd " + pre_cmd + " progressed");
		// exec play cmd
		spawn_callback(cmd, [], function (data) {
			console.log("cmd " + cmd + " progressed");
		}, cb_data, cb_err);
	}, cb_data, cb_err);
}

// server

var handler = {};
handler["/callnode"] = function(req, res, param) {
	console.log("deal " + param);
	mpg([false, false, querystring.parse(param)["url"]]);
}

function onRequest(request, response) {
	var postData = "";
	var pathname = url.parse(request.url).pathname;
	var param = url.parse(request.url).query;
	console.log("onRequest: " + pathname);
	request.addListener("data", function(data) {
    });
    request.addListener("end", function() {
		if (handler[pathname]) {
			handler[pathname](request, response, param);
		}
    });
}
http.createServer(onRequest).listen(PORT);