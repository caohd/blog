var p = 1;
var totle = 0;
var canLoadNext = false;
// 设置
;$(function(){
    'use strict';
    var blog = $('.Blog');
    var sendBlog = $('.sendBlog');
    var closeBlog = $('.closeBlogContent');
    var mask = $('.mask');
    //显示博客弹出框
    function showBlog() {
        sendBlog.css("display", "block");
        mask.fadeIn();
    }
    //隐藏博客弹出框
    function hideBlog(){
        sendBlog.css("display", "none");
        mask.fadeOut();
    }
    blog.on('click',showBlog);    //监听侧栏触发点击事件
    closeBlog.on('click',hideBlog);    //监听mask触发点击事

    //图片发表
    var photo = $('.Photo');
    var sendPhoto = $('.sendPhoto');
    var closePhoto = $('.closePhotoContent');
    //显示图片发表框
    function showPhoto() {
        sendPhoto.css("display", "block");
        mask.fadeIn();
    }
    //隐藏弹出框
    function hidePhoto(){
        sendPhoto.css("display", "none");
        mask.fadeOut();
    }
    photo.on('click',showPhoto);    //监听侧栏触发点击事件
    closePhoto.on('click',hidePhoto);    //监听mask触发点击事


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
var mompics = [];
var idiv = '';
/**
 * 上传
 */
$(function () {
    $('#choosePicContent').uploadifive({
        'buttonText'       : '',
        'fileObjName'      : 'momentPic',
        'auto'             : true,
        'formData'         : {
            'c'            : 'Index',
            'a'            : 'upl'
        },
        'queueID'          : 'previewPhoto',
        'itemTemplate'     : '<div class="uploadifive-queue-item"></div>',
        'uploadScript'     : 'dddd.html',
        'onUploadComplete' : function(file, data) {
            data = JSON.parse(data);
            if (0 === data.code) {

                var d = document.getElementById('previewPhoto');
                idiv += "<img src='public/images/" + data.data.src + "' height='640' width='640'/>";
                d.innerHTML = idiv;
                mompics[mompics.length] = data.data.src;
            }
        }
    });
});

var curUser;
var mm = 100000000000;
var am = 10000000000;
function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';

    $.post('id.html?p=' + p + '&' + getGet(), {c : 'Index', a : 'init'}, function (res, status) {
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

            var moments = document.getElementById('moments');
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
        } else if (1 === res.code) {
            tip('没有更多数据了');
        }
        document.getElementsByTagName('body')[0].style.display = 'block';
        onerrorImg();
    });

}

function loadMore() {
    $.post('id.html?p=' + p + '&' + getGet(), {c : 'Index', a : 'loadMore', am : am, mm : mm}, function (res, status) {
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

function sendMoment() {
    var data = {};
    var con = document.getElementById('inputPicTitle');
    data.content = con.value;
    data.srcs = mompics;
    data.c = 'Moment';
    data.a = 'publish';
    $.post('dsfa.html', data, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var moments = document.getElementById('moments');
            var mmm = res.data.moment;
            mmm[0].like = null;
            moments.innerHTML = momentTemplate(mmm, 0)[0] + moments.innerHTML;
            var div = document.getElementsByClassName('sendPhoto')[0];
            document.getElementById('previewPhoto').innerHTML = '';
            div.style.display = 'none';
            con.value = '';
            tip('发表成功');
            var mask = $('.mask');
            mask.fadeOut();
        } else if (1 === res.code) {
            var div = document.getElementsByClassName('sendPhoto')[0];
            tip(res.msg);
            var mask = $('.mask');
            mask.fadeOut();
        } else {
            moments.innerHTML += moments.innerHTML + momentTemplate(res.data, 0);
            var div = document.getElementsByClassName('sendPhoto')[0];

            var mask = $('.mask');
            mask.fadeOut();
        }
    });
}
function publishArticle() {
    publish(0);
}
function draftArticle() {
    publish(2);
}
function publish(flag) {
    var title = document.getElementById('pTitle');
    var content = document.getElementById('pContent');
    $.post('lena.html', {c : 'Article', a : 'publish', title : title.value, content : content.value, flag : flag}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var div = document.getElementsByClassName('sendBlog')[0];
            div.style.display = 'none';
            var moments = document.getElementById('moments');
            moments.innerHTML = articleTemplate(res.data.new) + moments.innerHTML;
            title.value = '';
            content.value = '';
            tip('发表成功');
            totle ++;
            var mask = $('.mask');
            mask.fadeOut();
        } else if (1 === res.code) {
            var div = document.getElementsByClassName('sendBlog')[0];
            div.style.display = 'none';
            tip(res.msg);
            var mask = $('.mask');
            mask.fadeOut();
        } else {

        }
    });
}
init();
