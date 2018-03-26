//搜索框
;$(function(){
    'use strict';
    var searchBox = $('.search_box');
    var searchList = $('.searchList');
    var mask = $('.mask');
    //显示搜索选项
    function showSearchList() {
        searchList.css("display", "block");
        mask.fadeIn();
    }
    //隐藏搜索选项
    function hideSearchList(){
        searchList.css("display", "none");
        mask.fadeOut();
    }
    searchBox.on('click',showSearchList);
    mask.on('click',hideSearchList);

    //对应搜索内容、ID
    var searchContent = $('#searchContent');
    var searchId = $('#searchId');

    searchContent.on('click',hideSearchList);
    searchId.on('click',hideSearchList);

});

function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';

    $.post('fdas.html' + getGet(), {c : 'Search', a : 'init'}, function (res, status) {
        res = JSON.parse(res);
        if (403 === res.code) {
            window.location.href = 'login.html';
        }
        usersDIV = document.getElementsByClassName('content')[0];
        momentsDIV = document.getElementsByClassName('content')[1];
        rtdata = res.data;
        console.log(rtdata);
        var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
        user[0].innerHTML = "<img id='logo' src='public/images/" + rtdata.logo + "'/>";
        user[1].innerHTML = "<span><a href='home.html'>" + rtdata.nick + "</a></span>";
        document.getElementsByClassName('myPhoto')[0].innerHTML = "<a href='home.html'><img src='public/images/" + rtdata.logo + "'/></a>";

        if (0 === res.code) {


            if (0 === rtdata.users.length) {
                usersDIV.style.display = 'none';
            } else {
                var ulist = document.getElementById('userlist');
                ulist.innerHTML = '';
                for (var i = 0; i < rtdata.users.length; i ++) {
                    ulist.innerHTML += userTemplate(rtdata.users[i])
                }
            }
            if (0 === rtdata.moments.length + rtdata.articles.length) {

                momentsDIV.style.display = 'none';
            } else {
                var mlist = document.getElementById('momentslist');
                mlist.innerHTML = '';
                var articles = rtdata.articles;
                var moment = rtdata.moments;
                var a = 0;
                var m = 0;
                while ((a + m) < articles.length + moment.length) {
                    if (a === articles.length) {
                        var r = momentTemplate(moment, m);
                        mlist.innerHTML += r[0];
                        m += r[1];
                    } else if (m === moment.length) {
                        mlist.innerHTML += articleTemplate(articles[a]);
                        a++;
                    } else if (articles[a].time > moment[m].time) {
                        mlist.innerHTML += articleTemplate(articles[a]);
                        a++;
                    } else {
                        var r = momentTemplate(moment, m);
                        mlist.innerHTML += r[0];
                        m += r[1];
                    }
                    i++;
                }
            }
        } else {
            usersDIV.style.display = 'none';
            // momentsDIV.style.display = 'none';
            tip('请输入搜索条件');
        }
        document.getElementsByTagName('body')[0].style.display = 'block';
        onerrorImg();
    });
}
init();