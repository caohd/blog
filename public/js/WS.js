var userSelf;
var userOther;
var userOthers = {};
var msgs = {};
var chatList = document.getElementsByClassName('chatList')[0];
function connentToWS() {
    $.post('fd.html' + getGet(), {c : 'User', a : 'infoInit'}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            userSelf = res.data.user;
            connws();
        }
    });
    function connws() {
        var ws = new WebSocket('ws://120.24.214.209:1380');
        ws.onopen = function (event) {
            var obj = {username : userSelf.username, id: userSelf.id, type : 0};
            ws.send(JSON.stringify(obj));
        };
        ws.onmessage = function (p1) {
            tip('收到了一条信息');
            data = JSON.parse(p1.data);
            userOther = getUserInfo(data.from);

            var c = {
                type : 2,
                from : data.from,
                time : getDateString(),
                logo : userOther.logo,
                content : data.data
            };
            if (isInFriend()) { // firend.html
                var text = document.getElementById('ddd' + data.from);
                var index = text.innerText.lastIndexOf('\ ');
                if (-1 === index) {
                    text.innerHTML = text.innerHTML + ' 1';
                } else {
                    var num = parseInt(text.innerText.substring(index + 1)) + 1;
                    text.innerHTML = text.innerText.substring(0, index + 1) + num;
                }
            } else { // message.html
                if (undefined === msgs[userOther]) {
                    userOther = getUserInfo(data.from);
                }
                changeCover(c);
            }
            if (undefined === msgs[userOther.username]) {
                var chat = {
                    username : userOther.username,
                    nickname : userOther.nickname,
                    logo     : userOther.logo,
                    content  : data.data,
                    time     : getDateString()
                };
                var cts = document.getElementsByClassName('priMesContent')[0].getElementsByTagName('section')[0];
                var ct = chatTemplate(chat).replace(/\n/, '<br />');
                cts.innerHTML += ct;
            } else {
                msgs[userOther.username] += chatItemTemplate(c);
                chatList.innerHTML += chatItemTemplate(c).replace('/\n/', '<br />');
            }
            chatList.scrollTop = chatList.scrollHeight;
        };

        ws.onclose = function (p1) {
            reconnect();
        };

        var chatContent = document.getElementById('chatContent');
        var send = document.getElementById('sendChatButton');
        send.onclick = function () {
            var data = {
                type : 1,
                fromid  : userSelf.id,
                from    : userSelf.username,
                toid    : userOther.id,
                to      : userOther.username,
                content : this.previousElementSibling.value
            };
            var c = {
                username : userSelf.username,
                nickname : userSelf.nickname,
                logo     : userSelf.logo,
                content  : this.previousElementSibling.value,
                time     : getDateString(),
                type     : 1
            };
            chatList.innerHTML = chatList.innerHTML + chatItemTemplate(c).replace(/\n/, '<br />');
            msgs[userOther.username] = msgs[userOther.username] + chatItemTemplate(c);
            chatList.scrollTop = chatList.scrollHeight;
            ws.send(JSON.stringify(data));
            this.previousElementSibling.value = '';
            changeCover(c);
        };
        document.onkeydown = function (e) {
            if(e.keyCode === 13)
            {
                if (e.ctrlKey) { // ctrl-enter
                    chatContent.value += "\n";
                } else if (e.altKey) { // alt-enter

                } else if (e.shiftKey) { // shift-enter

                } else { // 只是按了回车
                    send.click();
                }
            }
        };
    }
}

var delChat = document.getElementsByClassName('delChat')[0];
delChat.onclick = function () {
    $.post('fdsaf.html', {c : 'Chat', a : 'deleteChats', uid : userOther.id}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            // 删除本地聊天记录
            this.innerHTML = '';
            msgs[userOther.username] = undefined;
        }
    });
};
function windowOpen() {
    var tname = document.getElementsByClassName('friID')[0];
    if (userOther.username === tname.innerHTML && 'block' === tname.parentNode.style.display) {
        return true;
    } else {
        return false;
    }
}
function isInFriend() {
    if (document.getElementsByClassName('fix_foot').length > 0) {
        return false;
    } else {
        return true;
    }
}
function chatTemplate(chat) {
    var rv = '';
    if (undefined === msgs[chat.username]) {
        rv = "<section id='ddd" + chat.username + "'>" +
            "    <!--发私信人头像-->" +
            "    <div class='comment_pic'>" +
            "        <img src='public/images/" + chat.logo + "'/>" +
            "    </div>" +
            "    <!--发私信人昵称-->" +
            "    <div class='comment_name'>" +
            "        <span>" + chat.nickname + "</span>" +
            "    </div>" +
            "    <div class='comment_bg'>" +
            "        <!--发私信时间-->" +
            "        <div class='comment_time'>" +
            "            <span>" + chat.time.substring(0, chat.time.lastIndexOf(':')) + "</span>" +
            "        </div>" +
            "        <!--未读信息数量，阅读后消失-->" +
            "        <div class='comment_number'>" + 0 + "</div>" +
            "        <!--私信内容-->" +
            "        <div class='comment_word'>" + chat.content +"</div>" +
            "        <div class='comment_list'>" +
            "            <ul>" +
            "                <!--回复-->" +
            "                <li><a href='#' title='回复私信' onclick='showChatBox1(this)'><img src='public/images3/M.png'/></a></li>" +
            "                <!--删除 -->" +
            "                <li><a onclick='delSection2(this)' href='#' title='删除私信'><img src='public/images3/I.png'/></a></li>" +
            "            </ul>" +
            "        </div>" +
            "    </div>" +
            "</section>";
        msgs[chat.username] = chatItemTemplate(chat);
        userOthers[chat.username] = getUserInfo(chat.username);
    } else {
        msgs[chat.username] = chatItemTemplate(chat) + msgs[chat.username];
        rv = '';
    }
    return rv;
}

function delSection2(e) {
    var p = e.parentNode.parentNode.parentNode.parentNode.parentNode;
    var username = p.id.substring(3);
    userOther = getUserInfo(username);
    $.post('fdsaf.html', {c : 'Chat', a : 'deleteChats', uid : userOther.id}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            // 删除本地聊天记录
            msgs[userOther.username] = undefined;
        }
    });
    p.style.display = 'none';
}
function changeCover(c) {
    var section = document.getElementById('ddd' + userOther.username);
    if (null === section)
        return;
    var time = section.getElementsByClassName('comment_time')[0];
    time.innerHTML = c.time;
    var word = section.getElementsByClassName('comment_word')[0];
    word.innerHTML = c.content;
}
//显示私信对话框
function showChatBox1(e) {
    var p = e.parentNode.parentNode.parentNode.parentNode.parentNode;
    var username = p.id.substring(3);
    var chatBox = $('.chatBox');
    var mask = $('.mask');
    chatBox.css("display", "block");
    chatBox.css("z-index", "100");
    chatList.innerHTML = (msgs[username]).replace(/\n/, '<br />');
    chatList.scrollTop = chatList.scrollHeight;
    document.getElementsByClassName('friID')[0].innerHTML = username;
    userOther = getUserInfo(username);
    onerrorImg();
    mask.fadeIn();
}
function chatItemTemplate(msg) {
    if (1 == msg.type) {
        var rv = "<section class='me'>" +
            "                <div class='word'>" + msg.content + "</div>" +
            "                <div class='pic'><img src='public/images/" + userSelf.logo + "'/></div>" +
            "            </section>";
    } else {
        rv = "<section class='firend'>" +
            "                <div class='pic'><img src='public/images/" + msg.logo + "'/></div>" +
            "                <div class='word'>" + msg.content + "</div>" +
            "            </section>";
    }
    return rv;
}

function commentTemplate(comment) {
    var rv = "<section class='viewBlog' id='comment" + comment.id + "'>" +
        "<!--评论人头像-->" +
        "<div class='comment_pic'>" +
        "    <img src='public/images/" + comment.logo + "'/>" +
        "</div>" +
        "<div class='comment_name'>" +
        " <span>" + comment.nickname + "</span>" +
        "</div>" +
        "<div class='comment_bg'>" +
        "    <div class='comment_time'>" +
        "        <span>" + comment.time.substring(0, comment.time.lastIndexOf(':')) + "</span>" +
        "    </div>";
    if (null === comment.mid) {
        rv += "    <div class='comment_title' onclick='viewBlog(this)' id='article" + comment.aid +"'>" +
            "        <span>评论了你的文章<span>";
    } else {
        rv += "    <div class='comment_title' onclick='viewBlog(this)' id='moment" + comment.mid +"'>" +
            "        <span>评论了你的朋友圈<span>";
    }
    rv +="    </div>" +
        "    <div class='comment_word'>" + comment.content + "</div>" +
        "    <div class='comment_list'>" +
        "        <ul>" +
        "            <li><a onclick='showReplyComment1(this)' title='回复评论'><img src='public/images3/M.png'/></a></li>";
    rv += "            <li><a onclick='delSection(this)' style='display: none;' title='删除评论'><img src='public/images3/I.png'/></a></li>";
    rv += "        </ul>" +
        "    </div>" +
        "</div>" +
        "</section>";
    return rv;
}

function getUserInfo(username) {
    if (undefined === userOthers[username]) {
        $.ajax({
            url: "fdaf.html",
            type : 'post',
            async: false,
            data : {c : 'User', a : 'getUserInfo', user : username},
            success: function(res){
                res = JSON.parse(res);
                if (0 === res.code) {
                    userOthers[username] = res.data.user;
                } else {
                    return [];
                }
            }
        });
        return userOthers[username];
    } else {
        return userOthers[username];
    }
}