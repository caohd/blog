
var p = 1;
var totle = 0;
var canLoadNext = false;
var mm = 100000000000;
var am = 10000000000;

;$(function(){
    'use strict';
    var nScrollHeight = 0; //滚动距离总长(注意不是滚动条的长度)
    var nScrollTop = 0;   //滚动到的当前位置
    var nDivHeight = 0;
    $(window).scroll(function(){
        nDivHeight = $(window).height()
        nScrollHeight = $('#main_wrapper')[0].scrollHeight;
        nScrollTop = $(window).scrollTop();
        if(nScrollTop + nDivHeight >= nScrollHeight - 100)
            if (canLoadNext) {
                loadMore();
                canLoadNext = false;
            }
    });
});
function loadMore() {
    $.post('id.html?p=' + p + '&' + getGet(), {c : 'World', a : 'loadMore', am : am, mm : mm}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            rtdata = res.data;

            var moments = document.getElementsByClassName('moments')[0];
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
function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';

    $.post('id.html?p=' + p + '&' + getGet(), {c : 'World', a : 'init'}, function (res, status) {
        res = JSON.parse(res);
        if (403 === res.code) {
            window.location.href = 'login.html';
        }
        if (0 === res.code) {
            rtdata = res.data;
            var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
            user[0].innerHTML = "<img id='logo' src='public/images/" + rtdata.logo + "'/>";
            user[1].innerHTML = "<span><a href='home.html'>" + rtdata.nick + "</a></span>";
            document.getElementsByClassName('myPhoto')[0].innerHTML = "<a href='home.html'><img src='public/images/" + rtdata.logo + "'/></a>";


            curUser = rtdata.user;
            var moments = document.getElementsByClassName('moments')[0];
            var articles = rtdata.articles;
            var moment = rtdata.moments;
            am = (0 === articles.length ? am : articles[articles.length - 1].id);
            mm = (0 === moment.length ? mm : moment[moment.length - 1].id);
            totle += articles.length + moment.length;
            var a = 0;
            var m = 0;
            var i = 0;
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
                i++;
            }

            p++;
            canLoadNext = true;

            onerrorImg();
        } else if (1 === res.code) {
            tip('没有更多数据了');
        }
        document.getElementsByTagName('body')[0].style.display = 'block';

    });

}

init();