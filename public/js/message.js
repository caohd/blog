//评论处展示全文
function viewBlog(e) {
    var pass_dialog = document.getElementsByClassName('passage_dialog')[0];
    var mask = $('.mask');
    pass_dialog.style.display = 'block';
    pass_dialog.style.zIndex = 1000;
    mask.fadeIn();
    $.post('cgedt.html', {c : 'Article', a : 'getByMAId', id : e.id}, function (res, status) {
        console.log(res)
        res = JSON.parse(res);
        if (0 === res.code) {
            var m = pass_dialog.getElementsByClassName('moments')[0];
            if (1 === res.data.type) {
                m.innerHTML = articleTemplate(res.data.ma[0]);
            } else if (2 === res.data.type) {
                m.innerHTML = momentTemplate(res.data.ma, 0);
            }
        }
    });
}




;$(function(){
    'use strict';

    //隐藏查看全文
    var passageClose = $('.passage_Close');
    function hideViewBlog() {
        var pass_dialog = $('.passage_dialog');
        var mask = $('.mask');
        pass_dialog.css("display", "none");
        mask.fadeOut();
    }
    passageClose.on('click',hideViewBlog);


    //评论,私信，其他 切换内容
    var comment = $('.Comment');
    var priMes = $('.privateMessage');
    var others = $('.Others');
    var commentC = $('.commentContent');
    var priMesC = $('.priMesContent');
    var othersC = $('.othersContent');
    //显示评论
    function showComment() {
        commentC.css("display", "block");
        priMesC.css("display", "none");
        othersC.css("display", "none");

        comment.css("color", "white");
        comment.css("background", "#4d5f77");
        priMes.css("color", "#4d5f77");
        priMes.css("background", "white");
        others.css("color", "#4d5f77");
        others.css("background", "white");
    }
    //显示私信
    function showPrivateMessage(){
        commentC.css("display", "none");
        priMesC.css("display", "block");
        othersC.css("display", "none");

        comment.css("color", "#4d5f77");
        comment.css("background", "white");
        priMes.css("color", "white");
        priMes.css("background", "#4d5f77");
        others.css("color", "#4d5f77");
        others.css("background", "white");
    }
    //显示其他
    function showOthers(){
        commentC.css("display", "none");
        priMesC.css("display", "none");
        othersC.css("display", "block");

        comment.css("color", "#4d5f77");
        comment.css("background", "white");
        priMes.css("color", "#4d5f77");
        priMes.css("background", "white");
        others.css("color", "white");
        others.css("background", "#4d5f77");
    }
    //执行事件
    comment.on('click',showComment);
    priMes.on('click',showPrivateMessage);
    others.on('click',showOthers)


///////////////////////////////////////
//    私信对话框
    var chatBox = $('.chatBox');
    var closeChat = $('.closeChat');
    var delChat = $('.delChat');
    var chatList = $('.chatList');
    var mask = $('.mask');
    // var showChat = $('.showChatBox');

    //隐藏私信对话框
    function hideChatBox(){
        chatBox.css("display", "none");
        mask.fadeOut();
    }
    closeChat.on('click',hideChatBox);
    //删除聊天记录
    function delChatList() {
        chatList.css("display", "none");
    }
    delChat.on('click',delChatList);

});


var cid;
var clickAble = true;
function showReplyComment1(e) {
    p = e.parentNode.parentNode.parentNode.parentNode.parentNode;
    cid = p.id.substring('comment'.length);
    var comment = document.getElementsByClassName("inputReply")[0];
    comment.style.display = "block";
}
function ccComment() {
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    var content = document.getElementById('ccomment').value;
    $.post('fdasf.html', {c : 'Comment', a : 'cComment', id : cid, content : content}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            tip('回复评论成功');
        }
        document.getElementById('ccomment').value = '';
        document.getElementsByClassName("inputReply")[0].style.display = 'none';
        clickAble = true;
    });
}

function flTemplate(item, type) {
    var rv = "<section>" +
        "<!--点赞人头像-->" +
        "<div class='comment_pic'>" +
        "    <img src='public/images/" + item.logo + "'/>" +
        "</div>" +
        "<!--点赞人昵称-->" +
        "<div class='comment_name'>" +
        "    <span>" + item.nickname + "</span>" +
        "</div>" +
        "<div class='comment_bg'>" +
        "    <!--点赞时间-->" +
        "    <div class='comment_time'>" +
        "        <span>" + item.time.substring(0, item.time.lastIndexOf(':')) + "</span>" +
        "    </div>" +
        "    <!--点赞相关内容-->" +
        "    <div class='comment_word'>" +
        "        <span>" + ((type === 1) ? '点赞' : '评论') + "</span>了你的<span>" + ((type === 1) ? '图片' : '博客') + "</span>：" +
        "        <span>" + ((type === 1) ? item.name : item.title) + "</span>" +
        "    </div>" +
        "</div>" +
        "</section>";
    return rv;
}
function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';
    $.post('last.html', {c : 'Message', a : 'init'}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var rtdata = res.data;
            var userInfo = rtdata.user;
            var comments = rtdata.comments;
            userSelf = rtdata.user;

            var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
            user[0].innerHTML = "<img id='logo' src='public/images/" + userInfo.logo + "'/>";
            user[1].innerHTML = "<span><a href='home.html'>" + userInfo.nickname + "</a></span>";
            document.getElementsByClassName('myPhoto')[0].innerHTML = "<a href='home.html'><img src='public/images/" + userInfo.logo + "'/></a>";

            var coms = document.getElementsByClassName('commentContent')[0];
            coms.innerHTML = '';
            var j = 0;
            var k = 0;
            var d = '';
            var s;
            for (var i = 0; i < comments.length; i ++) {
                var t =comments[i].time.substring(0, comments[i].time.lastIndexOf('\ '));
                if (d !== t) {
                    coms.innerHTML = coms.innerHTML + "<section><div class='time'>" + t + "</div></section>";
                    s = document.getElementsByClassName('commentContent')[0].getElementsByTagName('section')[0];
                    for (var m = 0; m < j; m ++) {
                        s = s.nextElementSibling;
                    }
                    j ++;
                    k ++;
                    d = t;
                }
                s.innerHTML = s.innerHTML + commentTemplate(comments[i]);
            }

            var chats = res.data.chats;
            var cts = document.getElementsByClassName('priMesContent')[0].getElementsByTagName('section')[0];
            cts.innerHTML = '';
            for (var i = 0; i < chats.length; i ++) {
                cts.innerHTML += chatTemplate(chats[i]).replace(/\n/, '<br />');
            }
            var sec = document.getElementsByClassName('othersContent')[0].getElementsByTagName('section')[0];
            var forwards = res.data.forwards;
            var picLikes = res.data.picLikes;
            sec.innerHTML = '';
            j = 0; k = 0;
            console.log(forwards.length + picLikes.length)
            for (var i = 0; i < forwards.length + picLikes.length; i ++) {
                if (j === forwards.length) {
                    sec.innerHTML += flTemplate(picLikes[k] ,1);
                    k ++;
                } else if (k === picLikes.length) {
                    sec.innerHTML += flTemplate(forwards[j], 2);
                    j ++;
                } else if (forwards[j].time > picLikes[k].time) {
                    sec.innerHTML += flTemplate(forwards[j], 2);
                    j ++;
                } else {
                    sec.innerHTML += flTemplate(picLikes[k], 1);
                    k ++;
                }
            }
        }
        document.getElementsByTagName('body')[0].style.display = 'block';
        onerrorImg();
        connentToWS();
    });
}


init();
