;$(function(){
    'use strict';

    /*var friend_select = $('.friend_select');
    var myCon = $('#my_Concern');
    var conMe = $('#concern_Me');
    var list_1 =$('.list_One');
    var list_2 =$('.list_Two');

    //显示博友选择
    function showFriendList() {
        list_1.css("display", "block");
        list_2.css("display", "none");
        friend_select.css("display", "none");
        mask.fadeIn();
    }
    //隐藏博友选择
    function hideFriendList(){
       list_1.css("display", "none");
       list_2.css("display", "block");
       friend_select.css("display", "none");
       mask.fadeOut();
    }
    myCon.on('click',showFriendList);    //监听侧栏触发点击事件
    mask.on('click',hideFriendList);    //监听mask触发点击事*/

    //发起私信
    var chatBox = $('.chatBox');
    var closeChat = $('.closeChat');
    // var delChat = $('.delChat');
    var chatList = $('.chatList');
    var mask = $('.mask');
    var showChat = $('.friend_message');

    //隐藏私信对话框
    function hideChatBox(){
        chatBox.css("display", "none");
        mask.fadeOut();
    }
    closeChat.on('click',hideChatBox);

    //删除聊天记录
    // function delChatList() {
    //     chatList.css("display", "none");
    // }
    // delChat.on('click',delChatList);

});



//显示私信对话框
function showChatBox(e) {
    //发起私信
    var chatBox = $('.chatBox');
    var mask = $('.mask');
    chatBox.css("display", "block");
    chatBox.css("z-index", "1000");
    var eid = e.id;
    var username = eid.substring(3);
    var ehtml = e.innerHTML;
    if (null !== ehtml.match('\ ')) {
        e.innerHTML = '发私信';
    }
    if (undefined === msgs[username]) {
        document.getElementsByClassName('chatList')[0].innerHTML = '';
    } else {
        var chatList = document.getElementsByClassName('chatList')[0];
        chatList.innerHTML = (msgs[username]);
        chatList.scrollTop = chatList.scrollHeight;
    }
    document.getElementsByClassName('friID')[0].innerHTML = username;
    userOther = getUserInfo(username);

    onerrorImg();
    mask.fadeIn();
}



//显示博友关系
function showFriendAbout1(e) {
    // var int_mask = $('.int_mask');
    if ('block' !== e.nextElementSibling.style.display)
        e.nextElementSibling.style.display = 'block';
    else
        e.nextElementSibling.style.display = 'none';

    // int_mask.fadeIn();

}

function hideFriendAbout1(e){
    e.style.display = 'none';
}

// 我关注了他，但是我不想关注他了
function noShip1(e){
    var p = e.parentNode.parentNode.parentNode.parentNode;
    $.post('hdsafjasdf.html', {c : 'Relation', a : 'cancelShip', id : p.id.substring('section'.length)}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            if (null === e.parentNode.nextElementSibling) { // 之后没关系了
                p.style.display = 'none';
            } else { // 他还关注着我
                var fs = p.getElementsByClassName('friend_ship')[0];
                fs.innerHTML = '加关注';
                var ul = p.getElementsByTagName('ul')[0];
                ul.innerHTML = "<li><a onclick='addConcern1(this)'>加关注</a></li>\n" +
                    "<li><a onclick='noConcern1(this)''>移除粉丝</a></li>";
            }
        }
    });
}

// 他关注了我，但是我不想让他关注了
function noConcern1(e){
    var p = e.parentNode.parentNode.parentNode.parentNode;
    $.post('hdsafjasdf.html', {c : 'Relation', a : 'cancelConcern', id : p.id.substring('section'.length)}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            if (e.parentNode.previousElementSibling.getElementsByTagName('a')[0].innerHTML === '加关注') { // 没关系了
                p.style.display = 'none';
            } else { // 我没有让他关注，但是我还是关注着他 互相关注=>我关注着他
                var fs = p.getElementsByClassName('friend_ship')[0];
                fs.innerHTML = '取消关注';
                var ul = p.getElementsByTagName('ul')[0];
                ul.innerHTML = "<li><a onclick='noShip1(this)'>取消关注</a></li>";
            }
        }
    });
}

//加关注
function addConcern1(e){
    var p = e.parentNode.parentNode.parentNode.parentNode;
    $.post('whatseven.html', {c : 'Relation', a : 'addConcern', id : p.id.substring('section'.length)}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var fs = p.getElementsByClassName('friend_ship')[0];
            fs.innerHTML = '互相关注';
            var ull = p.getElementsByTagName('ul')[0];
            ull.innerHTML = "<li><a  onclick='noShip1(this)'>取消关注</a></li>" +
                "<li><a  onclick='noConcern1(this)'>移除粉丝</a></li>";
        }
    });
}

function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';
    $.post('fdasfas.html' + getGet(), {c : 'Relation', a : 'init'}, function (res, status) {
        res = JSON.parse(res);
        if (403 === res.code){
            window.location.href = 'login.html';
        }
        if ('1' === getCookie('admin')) {
            if (0 !== getGet().length) {
                url = 'fdaf.html' + getGet();
                document.getElementById('album').href += getGet();
                document.getElementById('info').href += getGet();
                document.getElementById('likeme').href += getGet();
                document.getElementById('blog').href += getGet();
            }
        }
        if (0 === res.code) {
            userSelf = res.data.user;
            var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
            user[0].innerHTML = "<img id='logo' src='public/images/" + res.data.logo + "'/>";
            user[1].innerHTML = "<span><a href='home.html'>" + res.data.nickname + "</a></span>";

            document.getElementsByClassName('top_banner')[0].innerHTML = "<img src='public/images/" + res.data.user.logo+ "' alt=''>" +
                "<h1>" + res.data.user.nickname + "</h1>" +
                "<!--个人简介-->" +
                "<a id='introduce' href='javascript:showIntroduce();'>" + res.data.user.brief + "</a>";


            var follow = res.data.follow;
            var fans = res.data.fans;
            var j = 0, k = 0;
            var mutualConcern = [];
            var m = 0;
            var len = follow.length + fans.length;
            for (var i = 0; i < len; i ++) {
                if (j === follow.length) {
                    k ++;
                } else if (k === fans.length) {
                    j ++;
                } else if (follow[j].uid === fans[k].uid) {
                    mutualConcern[m] = follow[j];
                    mutualConcern[m].f = 2;
                    follow.splice(j, 1);
                    fans.splice(k, 1);
                    m ++;
                } else if (follow[j].uid > fans[k].uid) {
                    j ++;
                } else {
                    k ++;
                }
            }
            var f = document.getElementById('fri');
            f.innerHTML = '';
            for (var i = 0; i < mutualConcern.length; i ++) {
                f.innerHTML += relationTemplate(mutualConcern[i]);
            }
            for (var i = 0; i < follow.length; i ++) {
                f.innerHTML += relationTemplate(follow[i]);
            }
            for (var i = 0; i < fans.length; i ++) {
                f.innerHTML += relationTemplate(fans[i]);
            }
        }
        document.getElementsByTagName('body')[0].style.display = 'block';

        var chats = res.data.chats;
        for (var i = 0; i < chats.length; i ++) {
            chatTemplate(chats[i]);
        }
        connentToWS();
        onerrorImg();
    });
}

function relationTemplate(fri) {
    rv = "<section id='section" + fri.uid + "'>" +
    "    <div class='friend_pic'><a href='home.html'><img src='public/images/" + fri.logo + "' height='640' width='640'/></a></div>" +
    "    <div class='friend_name'>" + fri.nickname + "</div>" +
    "    <div class='friend_message' id='ddd" + fri.username + "' onclick='showChatBox(this)'>" +"发私信" +"</div>"+
    "    <div class='friend_intro'>" + fri.brief + "</div>" +
    "    <div class='friend_ship' onclick='showFriendAbout1(this)'>";
    if (2 == fri.f) {
        rv += "相互关注";
    } else if (1 == fri.f) {
        rv += "加关注";
    } else {
        rv += "取消关注";
    }
    rv += "</div>" +
    "    <div class='friend_about' onclick='hideFriendAbout1(this)'>" +
    "        <ul>";
    if (2 == fri.f) {
        rv += "<li><a  onclick='noShip1(this)'>取消关注</a></li>" +
        "<li><a  onclick='noConcern1(this)'>移除粉丝</a></li>";
    } else if (0 == fri.f) {
        rv += "<li><a onclick='noShip1(this)'>取消关注</a></li>";
    } else {
        rv += "<li><a onclick='addConcern1(this)'>加关注</a></li>\n" +
            "<li><a onclick='noConcern1(this)''>移除粉丝</a></li>";
    }
    rv += "        </ul>" +
    "    </div>" +
    "</section>";
    return rv;
}


init();