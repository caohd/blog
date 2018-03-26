var p = 1;
var am = 10000000;
var mm = 10000;
var canLoadNext = false;
var totle = 0;
$(function(){
    'use strict';
    var blog = $('#Blog');
    var blog_select = $('#blog_select');
    var mask = $('.int_mask');
    function showChooseBlog() {
        blog_select.css("display", "block");
        blog_select.css("z-index", "100");
        mask.fadeIn();
    }
    function hideChooseBlog(){
        blog_select.css("display", "none");
        blog_select.css("z-index", "10");
        mask.fadeOut();
     }
    blog.on('click',showChooseBlog);    //监听侧栏触发点击事件
    mask.on('click',hideChooseBlog);    //监听mask触发点击事件


    var friend = $('#Friend');
    var friend_select = $('#friend_select');
    function showChooseFriend() {
        friend_select.css("display", "block");
        friend_select.css("z-index", "100");
        mask.fadeIn();
    }
    function hideChooseFriend(){
        friend_select.css("display", "none");
        friend_select.css("z-index", "10");
        mask.fadeOut();
    }
    friend.on('click',showChooseFriend);    //监听侧栏触发点击事件
    mask.on('click',hideChooseFriend);    //监听mask触发点击事件


    var nScrollHeight = 0; //滚动距离总长(注意不是滚动条的长度)
    var nScrollTop = 0;   //滚动到的当前位置
    var nDivHeight = 0;
    $(window).scroll(function(){
        nDivHeight = $(window).height();
        nScrollHeight = $('#main_wrapper')[0].scrollHeight;
        nScrollTop = $(window).scrollTop();
        if(nScrollTop + nDivHeight >= nScrollHeight - 100)
            if (canLoadNext) {
                loadMore();
                canLoadNext = false;
            }
    });






    var bar=$('.content_bar');
    $(window).on('scroll',function(){         //监听window的点击事件
        //如果已经滚动的部分高于窗口高度
        if($(window).scrollTop() > $(window).height()/2){
            //显示返回按钮
            bar.css("position","fixed");
            bar.css("width","58%");
            bar.css("left","21%");
            bar.css("top","60px");
            bar.css("z-index","10");

        }
        //隐藏返回按钮
        else{
            bar.css("position","relative");
            bar.css("width","90%");
            bar.css("left","0%");
            bar.css("top","0px");
        }
    });
    /*刷新自动触发滚动页面函数scroll*/
    $(window).trigger('scroll');

});

//获取元素名称
function g(id){
    return document.getElementById(id);
}

// autoCenter(g('dialog'));
//自动遮罩
function fillAll(el){
    el.style.width = document.documentElement.clientWidth + "px";
    el.style.height = document.documentElement.clientHeight + "px";
    // el.style.display = "block";
}
// fillAll(mask);

var mouseOffsetX = 0;   //鼠标偏移浮动层左上角的位置
var mouseOffsetY = 0;
var isDialog = false;   //是否可以移动

//鼠标拖拽函数1，计算鼠标相对可拖拽元素的左上角的坐标的距离，并设置为不可拖拽
g('int_dialogTitle').addEventListener('mousedown',function(e){
    var e= e||window.event;
    mouseOffsetX = e.pageX - g('int_dialog').offsetLeft;        //g('dialog').offsetLeft;元素距离左边的距离
    mouseOffsetY = e.pageY - g('int_dialog').offsetTop;
    isDialog = true;
});

//鼠标拖拽函数2，
document.onmousemove = function(e){
    var e= e||window.event;
    var mouseX = e.pageX;  //鼠标当前位置
    var mouseY = e.pageY;

    var moveX = 0;//浮层元素的新位置
    var moveY = 0;

    if(isDialog === true){

        var moveX = mouseX - mouseOffsetX;  //浮层左上角位置
        var moveY = mouseY - mouseOffsetY;

        var pageWidth  = document.documentElement.clientWidth ;   //页面最大宽度
        var pageHeight  = document.documentElement.clientHeight ;

        var dialogWidth = g('int_dialog').offsetWidth;    //浮层宽度
        var dialogHeight = g('int_dialog').offsetHeight;

        var maxX = pageWidth - dialogWidth;   //可移动最大范围
        var maxY = pageHeight - dialogHeight;


        moveX = Math.min(maxX, Math.max(0,moveX) );  //浮层元素的范围（左上，右下）
        moveY = Math.min(maxY, Math.max(0,moveY) );


        g('int_dialog').style.left = moveX + "px";
        g('int_dialog').style.top = moveY + "px";

    }
};
//鼠标松开事件
document.onmouseup = function(){
    isDialog = false;
};




function init() {

    document.getElementsByTagName('body')[0].style.display = 'none';
    var url;
    if (0 !== getGet().length) {
        url = 'fdaf.html' + getGet() + '&p=' + p;
    } else {
        url = 'fdaf.html?p=' + p;
    }
    $.post(url, {c : 'User', a : 'init'}, function (res, status) {

        res = JSON.parse(res);
        if (403 === res.code) {
            window.location.href = 'login.html';
        }
        if (0 !== getGet().length) {
            url = 'fdaf.html' + getGet() + '&p=' + p;
            document.getElementById('album').href += getGet();
            document.getElementById('info').href += getGet();
            // console.log(document.getElementById('info'));
            document.getElementById('likeme').href += getGet();
            document.getElementById('friend').href += getGet();
        }
        if (1 === res.code) {
            tip('用户不存在');
        } else if (0 === res.code) {
            var rtdata = res.data;
            var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
            user[0].innerHTML = "<img id='logo' src='public/images/" + rtdata.logo + "'/>";
            user[1].innerHTML = "<span><a href='home.html'>" + rtdata.nick + "</a></span>";

            document.getElementById('userInfo').innerHTML = "<img src='public/images/" + rtdata.user.logo+ "' alt=''>" +
                "<h1>" + rtdata.user.nickname + "</h1>" +
                "<!--个人简介-->" +
                "<a id='introduce' href='javascript:showIntroduce();'>" + rtdata.user.brief + "</a>";

            curUser = rtdata.user;
            var moments = document.getElementById('moments');
            var articles = rtdata.articles;
            var moment = rtdata.moments;
            am = (0 === articles.length ? am : articles[articles.length - 1].id);
            mm = (0 === moment.length ? mm : moment[moment.length - 1].id);
            totle += articles.length + moment.length;
            var a = 0;
            var m = 0;
            if (0 === totle) {
                tip('没有更多数据了');
            }
            moments.innerHTML = '';
            while ((a + m) < articles.length + moment.length) {
                if (a === articles.length) {
                    var r = momentTemplate(moment, m);
                    moments.innerHTML += r[0];
                    m += r[1];
                } else if (m === moment.length) {
                    moments.innerHTML += articleTemplate(articles[a]);
                    a++;
                } else if (articles[a].time > moment[m].time) {
                    moments.innerHTML += articleTemplate(articles[a]);
                    a++;
                } else {
                    var r = momentTemplate(moment, m);
                    moments.innerHTML += r[0];
                    m += r[1];
                }
            }

            p++;
            canLoadNext = true;
        }

        document.getElementsByTagName('body')[0].style.display = 'block';
        onerrorImg();
    });
}
var _t = 0;
function reloadMoments(t) {
    if (_t !== t) {
        _t = t;
        p = 0;
        am = 10000000;
        mm = 10000000;
    }
}
function loadMore() {
    $.post('id.html?p=' + p + '&' + getGet(), {c : 'User', a : 'loadMore', am : am, mm : mm, t : _t}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            rtdata = res.data;

            var moments = document.getElementById('moments');
            var articles = rtdata.articles;
            var moment = rtdata.moments;
            am = (0 === articles.length ? am : articles[articles.length - 1].id);
            mm = (0 === moment.length ? mm : moment[moment.length - 1].id);
            var a = 0;
            var m = 0;
            var i = 0;
            totle += articles.length + moment.length;
            while ((m + a) < articles.length + moment.length) {
                if (a === articles.length) {
                    var r = momentTemplate(moment, m);
                    moments.innerHTML += r[0];
                    m += r[1];
                } else if (m === moment.length) {
                    moments.innerHTML += articleTemplate(articles[a]);
                    a++;
                } else if (articles[a].time > moment[m].time) {
                    moments.innerHTML += articleTemplate(articles[a]);
                    a++;
                } else {
                    var r = momentTemplate(moment, m);
                    moments.innerHTML += r[0];
                    m += r[1];
                }
            }

            p++;
            canLoadNext = true;
        } else {
            tip('没有更多数据了');
        }
    });
}
init();


