;$(function(){
    'use strict';
    var setting = $('.setting');
    var setting_select = $('.setting_select');
    var mask = $('.mask');
    var backTop = $('.backTop');
    //显示设置
    function showMore() {
        setting_select.css("display", "block");
        setting[0].innerHTML = "<img src='public/images2/set02.png'/>";
        mask.fadeIn();
    }
    //隐藏设置
    function hideMore(){
        setting_select.css("display", "none");
        setting[0].innerHTML = "<img src='public/images2/set01.png'/>";
        mask.fadeOut();
    }
    setting.on('click',showMore);    //监听触发点击事件
    mask.on('click',hideMore);    //监听mask触发点击事件

    //弹出草稿箱
    var draftBox = $('.draftBox');
    var draftContent = $('.draftContent');
    var draftClose = $('.draftClose');
    function showDraftBox(){
        draftContent.css("display","block");
    }
    function hideDraftBox(){
        draftContent.css("display","none");
        setting_select.css("display", "none");
        setting[0].innerHTML = "<img src='public/images2/set01.png'/>";
        mask.fadeOut();
    }
    draftBox.on('click',showDraftBox);    //监听触发点击事件
    draftClose.on('click',hideDraftBox);    //监听触发点击事件


    /*返回顶部*/
    backTop.on('click',function(){        //监听返回按钮点击事件
        $('html,body').animate({scrollTop:0},500)
    })

    /*返回顶部按钮的隐藏与显示*/
    $(window).on('scroll',function(){         //监听window的点击事件
        //如果已经滚动的部分高于窗口高度
        if($(window).scrollTop() > $(window).height()){
            //显示返回按钮
            backTop.fadeIn();
        }
        //隐藏返回按钮
        else{
            backTop.fadeOut();
        }
    });
    /*刷新自动触发滚动页面函数scroll*/
    $(window).trigger('scroll');

    var cb = document.getElementsByClassName('content_bar');

    if (0 !== cb.length) {
        var g = getGet();
        var index;
        if (0 !== getGet().length) {
            index  = g.lastIndexOf('&');
            if (-1 === index) {
                index = g.length;
            }
            // document.getElementById('blog').href += g.substring(0, index);
            // document.getElementById('album').href += g.substring(0, index);
        }
        var li = cb[0].getElementsByTagName('li')[0].nextElementSibling;
        if ('blog_select' === li.id) {
            li = li.nextElementSibling;
        }
        var s1 = 'user=';
        var i1 = g.lastIndexOf(s1);
        var ns = g.substring(i1 + s1.length, index);

        if (-1 === i1 || ns === getCookie('user') || '1' === getCookie('admin')) {

        } else {
            li = li.nextElementSibling;
            li.innerHTML = "<a id='friend' href='javascript:tip(\"无法访问\")'>博友</a>";
            li = li.nextElementSibling;
            li.innerHTML = "<a id='likeme' href='javascript:tip(\"无法访问\")'>我的赞</a>";
        }
    }
});
var clickAble = true;
/*收起全文*/
function hidePassage1(e){
    var hideAllPass = e.parentNode;
    var showAllPass = hideAllPass.previousElementSibling;
    var passageContent = showAllPass.previousElementSibling;
    passageContent.style.overflow = "hidden";
    passageContent.style.height = "250px";
    showAllPass.style.display = "block";
    hideAllPass.style.display = "none";
}
/*展开全文*/
function showPassage1(e){
    var showAllPass = e.parentNode;
    var sec = e.parentNode.parentNode.parentNode;
    console.log(sec);
    $.post('daf.html', {c : 'Article',  a : 'moreReader', id : sec.id.substring('article'.length)}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var d = sec.getElementsByClassName('fri_read')[0].getElementsByTagName('span')[0];
            d.innerHTML = 1 + parseInt(d.innerHTML);
        }
    });
    var hideAllPass = showAllPass.nextElementSibling;
    var passageContent = showAllPass.previousElementSibling;
    passageContent.style.overflow = "visible";
    passageContent.style.height = "auto";
    showAllPass.style.display = "none";
    hideAllPass.style.display = "block";
}
function iLike1(e){
    var am = e.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
    // like
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    $.post('haolena.html', {c : 'Like', a : 'ILike', id : am.id} ,function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code)
            changeLikeImg(e);
        clickAble = true;
    });
}

function changeLikeImg(e) {
    var img = e.getElementsByTagName('img')[0];
    if ('public/images2/beforelike.png' === decodeURI(img.src) ||
        'http://blog.caohd.com/public/images2/beforelike.png' === decodeURI(img.src)) {
        img.src = 'public/images2/afterlike.png';

    } else {
        img.src = 'public/images2/beforelike.png';
    }
}

//转发提示
function rePostTip(e){
    $("#tip_dialog").css("visibility","visible");
    var t=setTimeout("$('.tip_dialog').css('visibility', 'hidden');",1000);
    $("#tip_dialog").attr("visibility","visible");
}
/*转发图标转换*/
/**
 * @deprecated
 */
function rePost(){
    var rePost = document.getElementById("rePost" + num);
    var isPost = false;
    if(isPost === false){
        rePost.innerHTML = "<img src='public/images2/b.png'/>";
        isPost = true;
    }
}

/**
 * 转发
 * @param e
 */
function rePost1(e) {
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    var p = e.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
    $.post('xiangshuijiao.html', {c : 'Article', a : 'reprint', id : p.id.substring('article'.length)}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            tip('转载成功');
        } else if (1 === res.code) {
            tip('已经转载过了');
        }
        clickAble = true;
    });
}

function tip(info){
    var tip = $("#tip");
    tip.css("visibility","visible");
    tip.html(info);
    var t=setTimeout("$('#tip').css('visibility', 'hidden');",1000);
    tip.attr("visibility","visible");
}
//删除博客提示
function deleteTip(){
    $(".delete_dialog").css("visibility","visible");
    var t=setTimeout("$('.delete_dialog').css('visibility', 'hidden');",1000);
    $(".delete_dialog").attr("visibility","visible");
}
//删除博客
function delSection1(e){
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    var div = e.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
    $.post('fdafads.html', {c : 'Article', a : 'delete', id : div.id}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            div.style.display = "none";
            if (totle < 5) {
                p = 1;
                document.getElementById('moments').innerHTML = '';
                loadMore();
                $('html,body').animate({scrollTop:0},500);
            }
            tip('删除成功');
        } else {
            tip('删除失败');
        }
        clickAble = true;
    });
}
//展开评论
function showComment1(e){
    var comment = e.parentNode.parentNode.parentNode.parentNode.nextElementSibling.nextElementSibling.nextElementSibling;
    comment.style.display = "block";
    loadComment(e);
}

//关闭评论
function closeComment1(e){
    var comment = e.parentNode.parentNode.parentNode.parentNode;
    comment.style.display = "none";
}
/**
 * 删除评论
 * @param e
 */
function delComment1(e){
    var commentList = e.parentNode.parentNode.parentNode.parentNode;
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    $.post('fda.html', {c : 'Comment', a : 'delete', id : commentList.id.substring('comment'.length)}, function (res, status) {
        res = JSON.parse(res);
        if (res.code < 2) {
            commentList.style.display = "none";
        } else {
            tip(res.msg);
        }
        clickAble = true;
    });
}
//评论弹出框
var ccom = 0;
var cid;
function showReplyComment(e){
    var comment = document.getElementsByClassName("inputReply")[0];
    console.log(e.parentNode.parentNode.id);
    cid = e.parentNode.parentNode.id;
    ccom = cid.substring("comment".length, cid.length);
    comment.style.display = "block";
}
//关闭评论弹出框
function hideReplyComment(){
    var comment = document.getElementsByClassName("inputReply")[0];
    comment.style.display = "none";
}
function cComment() {
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    var content = document.getElementById('ccomment').value;
    $.post('fdasf.html', {c : 'Comment', a : 'cComment', id : ccom, content : content}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var cl = document.getElementById(cid).getElementsByClassName('comment_reply')[0];
             cl.innerHTML = cl.innerHTML +
                 "<section> " +
                 "    <div class='reply_time'> " +
                 "        <span>" + getDateString() + "</span> " +
                 "        </div> " +
                 "        <div class='reply_name'> " +
                 "            <span>" + document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('a')[0].innerHTML + "</span> " +
                 "        </div> " +
                 "    <div class='reply_word'> " + content + "    </div> " +
                 "</section> ";
                 hideReplyComment();
        }
        clickAble = true;
    });
}
function getDateString() {
    date = new Date();
    var rv = date.getFullYear() + "-" + (date.getMonth() + 1) + '-' + date.getDay();
    rv += ' ' + date.getHours() + ':' + date.getMinutes();
    return rv;
}
function articleTemplate(article) {
    var rv = "<section id='article" + article.id + "'> " +
        "    <div class='fri_pic'><img src='public/images/" + article.logo + "' alt=''></div> " +
        "    <div class='fri_main'> " +
        "        <div class='fri_name'><a href='home.html?user=" + article.user + "'>" + article.nickname + "</a></div> ";

    if (null === article.isFriend && article.user !== getCookie('user')) {
        rv += "        <div class='fri_concern' >" +
            "             <a id='concern1' title='点击关注好友'  onclick='concern(this," + article.uid + ")'>+关注</a>" +
            "        </div>";
    }
    rv += "        <div class='fri_time'>" + article.time.substring(0, article.time.length - 3) + "</div> " +
        "        <div class='fri_read'>阅读量 | <span>" + article.readers + "</span></div> " +
        "        <div class='fri_content'> " +
        "            <span>" + article.title + "</span> " +
        "            <article style='word-wrap:break-word'> " + article.content.replace(/\n/g, '<br />') + " </article> " +
        "            <div class='fri_list'> " +
        "                <ul> " +
        "                    <li> " +
        "                        <a onclick='iLike1(this)' title='点赞' class='like'> ";
    if (null === article.like)
        rv += "<img src='public/images2/beforelike.png'/> ";
    else
        rv += "<img src='public/images2/afterlike.png'/> ";
    rv += "                        </a> " +
        "                    </li> " +
        "                    <li> " +
        "                        <a onclick='showComment1(this)' title='评论' class='comment'> " +
        "                            <img src='public/images2/beforecomment.png'> " +
        "                        </a> " +
        "                    </li> ";
    if (article.user === getCookie('user') || '1' === getCookie('admin')) {
        rv += "                    <li> " +
            "                        <a onclick='delSection1(this)'> " +
            "                            <img src='public/images2/trash.png' /> " +
            "                        </a> " +
            "                    </li> ";
    } else {
        rv += "                    <li> " +
            "                        <a onclick='rePost1(this)' title='转载' class='rePost'> " +
            "                            <img src='public/images2/before.png'/> " +
            "                        </a> " +
            "                    </li> ";
    }
    rv += "                </ul> " +
        "            </div> " +
        "        </div> " +
        "        <div class='fri_contentAll'> " +
        "            <a onclick='showPassage1(this)' title='展开全文'><img src='public/images2/full.png'/></a> " +
        "        </div> " +
        "        <div class='fri_contentAllHide'> " +
        "            <a onclick='hidePassage1(this)' title='收起全文'> " +
        "            <img src='public/images2/unfull.png'/></a> " +
        "        </div> " +
        "        <!--评论框--> " +
        "        <div class='fri_comment'> " +
        "            <!--列表--> " +
        "            <div class='fri_List'> " +
        "                <ul> ";

    rv += "                    <li><a title='点赞数' class='like'><img src='public/images2/点赞之" + (null === article.like ? "前" : "后") + ".png'/> <span>" + article.belike + "</span></a></li> ";

    rv += "                    <li><a title='评论数' class='comment' onclick='closeComment1(this)'><img src='public/images2/c.png'> <span>" + article.comment + "</span></a></li> " +
        "                    <li><a title='转载数' class='rePost'><img src='public/images2/before.png'/> <span>" + article.repost + "</span></a></li> " +
        "                </ul> " +
        "            </div> " +
        "            <!--评论输入框--> " +
        "            <div class='sendComment'> " +
        "                <form method='post' action=''> " +
        "                    <textarea name='' placeholder='请输入你的评论……' class='inputComment'></textarea> " +
        "                    <input type='button' value=' ' onclick='maComment(this)' class='submitComment'> " +
        "                </form> " +
        "            </div> " +
        "            <!--评论列表--> " +
        "            <div class='commentList'> " +
        "            </div> " +
        "        </div> " +
        "    </div> " +
        "</section>";
    return rv;
}
function momentTemplate(moment, m) {
    var mm = moment[m];
    var rs;
    var ri = 0;
    rs = "<section id='monent" + mm.id  + "'> " +
        "    <div class='fri_pic'><img src='public/images/" + mm.logo + "' alt=''></div> " +
        "    <div class='fri_main'> " +
        "        <div class='fri_name'><a href='home.html?user=" + mm.user + "'>" + mm.nickname + "</a></div> ";
    if (null === mm.isFriend && mm.user !== getCookie('user')) {
        rs += "        <div class='fri_concern' >" +
            "             <a id='concern1' href='#' title='点击关注好友' onclick='concern(this," + mm.uid + ")'>+关注</a>" +
            "        </div>";
    }
    rs += "        <div class='fri_time'>" + mm.time.substring(0, mm.time.length - 3) + "</div> " +
        "        <div class='fri_read1'>阅读量 | <span>0</span></div> " +
        "        <div class='fri_content' id='fri_content2'> " +
        "            <div class='photoTitle'> " + mm.content.replace(/\n/g, '<br />').replace(/ /g, '&nbsp;') + "</div> " +
        "            <div class='photoContent'> ";

    for (var i = 0; i + m < moment.length ; i ++) {
        if (mm.id === moment[m + i].id) {
            rs += "<img src='public/images/" + moment[m + i].src + "'/> ";
            ri ++;
        } else {
            break;
        }
    }
    rs += "            </div> " +
        "            <div class='fri_list'> " +
        "                <ul> " +
        "                    <li> " +
        "                        <a onclick='iLike1(this)' title='点赞' class='like'> ";

    if (null === mm.like)
        rs += "<img src='public/images2/beforelike.png'/> ";
    else
        rs += "<img src='public/images2/afterlike.png'/> ";

    rs += "                        </a> " +
        "                    </li> " +
        "                    <li> " +
        "                        <a onclick='showComment1(this)' title='评论' id='comment2' class='comment'> " +
        "                            <img src='public/images2/beforecomment.png'> " +
        "                        </a> " +
        "                    </li> ";
    if (mm.user === getCookie('user') || 1 === getCookie('admin')) {
        // rs += "                    <li> " +
        //     "                        <a onclick='rePost1(this)' id='rePost2' title='转载' class='rePost'> " +
        //     "                            <img src='public/images2/before.png'/> " +
        //     "                        </a> " +
        //     "                    </li> ";
    // }else {
        rs += "                    <li> " +
            "                        <a onclick='delSection1(this)' title='删除' id='delete2'> " +
            "                            <img src='public/images2/trash.png' /> " +
            "                        </a> " +
            "                    </li> ";
    }

    rs += "                </ul> " +
        "            </div> " +
        "        </div> " +
        "        <div class='fri_contentAll' id='fri_contentAll2'><a onclick='showPassage1(this)' title='展开全文'><img src='public/images2/full.png'/></a> " +
        "        </div> " +
        "        <div class='fri_contentAllHide' id='fri_contentAllHide2'> " +
        "            <a onclick='hidePassage1(this)' title='收起全文'> " +
        "                <img src='public/images2/unfull.png'/> " +
        "            </a> " +
        "        </div> " +
        "        <!--评论框--> " +
        "        <div class='fri_comment' id='fri_comment2'> " +
        "            <!--列表--> " +
        "            <div class='fri_List'> " +
        "                <ul> " +
        "                    <li><a title='点赞数' class='like'><img src='public/images2/点赞之" + (null === mm.like ? '前' : '后') + ".png'/> <span>" + mm.belike + "</span></a></li> " +
        "                    <li><a title='评论数' class='comment' onclick='closeComment1(this)'><img src='public/images2/c.png'> <span>" + mm.comment + "</span></a></li> " +
        "                    <li><a title='转载数' class='rePost'><img src='public/images2/before.png'/> <span>0</span></a></li> " +
        "                </ul> " +
        "            </div> " +
        "            <!--评论输入框--> " +
        "            <div class='sendComment' id='sendComment2'> " +
        "                <form method='post' action=''> " +
        "                    <textarea name='' id='' cols='30' rows='10' placeholder='请输入你的评论……' class='inputComment'></textarea> " +
        "                    <input type='button' value='' onclick='maComment(this)' class='submitComment'> " +
        "                </form> " +
        "            </div> " +
        "            <!--评论列表--> " +
        "            <div class='commentList'> ";
    rs += "            </div> " +
        "        </div> " +
        "    </div> " +
        "</section>";
    return [rs, ri];
}
//自动居中
function autoCenter(el){
    var bodyW = document.documentElement.clientWidth;    //屏幕可视宽高
    var bodyH = document.documentElement.clientHeight;

    var elW = el.offsetWidth;    //元素实际宽高
    var elH = el.offsetHeight;

    el.style.left = (bodyW - elW) / 2 + "px";
    el.style.top = (bodyH - elH) / 2 + "px";

}
//隐藏简介浮层
function hideIntroduce(){
    document.getElementById('int_dialog').style.display = 'none';
    document.getElementById('int_mask').style.display = 'none';
}
//展现简介浮层
function showIntroduce(){

    document.getElementById('int_dialog').style.display = 'block';
    document.getElementById('int_mask').style.display = 'block';
    autoCenter(document.getElementById('int_dialog'));
    fillAll(document.getElementById('int_mask'));

}
function loadComment(e) {
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    comment = e.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
    $.post('fds.html', {c : 'Comment', a : 'loadComment', id : comment.id}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var comments = res.data.comment;
            if (0 === comments.length) {

            } else {
                var clist = comment.getElementsByClassName('commentList')[0];
                clist.innerHTML = '';
                for (var i = 0; i < comments.length; i ++) {
                    clist.innerHTML += "<section id='comment" + comments[i].id+ "'> " +
                        "<div class='comment_time'> " +
                        "    <span>" + comments[i].time.substring(0, comments[i].time.length - 3) + "</span> " +
                        "</div> " +
                        "<div class='comment_pic'> " +
                        "    <img src='public/images/" + comments[i].logo + "' height='640' width='640'/></div> " +
                        "<div class='comment_name'> " +
                        "    <span>" + comments[i].nick + "</span> " +
                        "</div> " +
                        "<div class='comment_word'> " + comments[i].content + "</div> " +
                        "<div class='comment_btn'> " +
                        "    <a onclick='showReplyComment(this)' title='回复评论' >回复</a> " +
                        "</div> " +
                        "<div class='comment_list'> " +
                        "    <ul> " +
                        "        <li><a onclick='delComment1(this)' title='删除评论'> " +
                        "            <img src='public/images2/trash.png' /> " +
                        "        </a></li> " +
                        "        <li><a onclick='iLike1(this)' title='点赞' class='like'> " +
                        "            <img src='public/images2/beforelike.png'/><span>(0)</span></a></li> " +
                        "        <li><a onclick=' ' title='评论' id='showComment2' class='comment'><img src='public/images2/c.png'><span>(0)</span></a></li> " +
                        "    </ul> " +
                        "</div> " +
                        "<div class='comment_reply'> " +
                        "</div> " +
                        '</section>';
                    var reply = clist.getElementsByClassName('comment_reply')[i];
                    reply.innerHTML = '';
                    var cm = comments[i].ccomm;
                    for (var j = 0; j < cm.length; j ++) {
                        console.log(j);
                        reply.innerHTML += "" +
                            "<section > " +
                            "    <div class='reply_time'> " +
                            "        <span>" + cm[j].time.substring(0, cm[j].time.length - 3) + "</span> " +
                            "    </div> " +
                            "    <div class='reply_name'> " +
                            "        <span>" + cm[j].nick + "</span> " +
                            "    </div> " +
                            "    <div class='reply_word'> " + cm[j].content + "    </div> " +
                            "</section> ";
                    }
                }
            }
        }
        clickAble = true;
    });
}
function userTemplate(user) {
    var rv = "<section >" +
    "<div class='friend_pic'><a href='home.html?user=" + user.username + "'><img src='public/images/" + user.logo + "' /></a></div>" +
    "<div class='friend_name'>" + user.nickname + "</div>" +
    "<div class='friend_intro'>" + user.brief + "</div>";
    if (null === user.isFriend && user.username !== getCookie('user')) {
        rv += "<div class='friend_concern' >" +
        "     <a title='点击关注好友' onclick='concern(this," + user.id + ")'>+关注</a>" +
        "</div>";
    }
    rv += "</section>";
    return rv;
}
function concern(e, uid) {
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    $.post('fd.html', {c : 'Relation', uid : uid, a : 'concern'}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            e.parentNode.style.display = 'none';
        }
        clickAble = true;
    });
}
function maComment(e) {
    var p = e.parentNode.parentNode.parentNode.parentNode.parentNode;
    var id = p.id;
    var content = e.previousElementSibling.value;
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    $.post('fd.html', {c : 'Comment', id : id, a : 'macomment', content : content}, function (res, status) {
        var clist = comment.getElementsByClassName('commentList')[0];
        res = JSON.parse(res);
        if (0 === res.code) {
            clist.innerHTML = "<section id='comment" + res.data.id + "'> " +
                "<div class='comment_time'> " +
                "    <span>" + getDateString() + "</span> " +
                "</div> " +
                "<div class='comment_pic'> " +
                "    <img src='" + document.getElementsByClassName('user')[0].getElementsByTagName('img')[0].src + "' height='640' width='640'/></div> " +
                "<div class='comment_name'> " +
                "    <span>" + document.getElementsByClassName('user')[0].getElementsByTagName('a')[0].innerHTML + "</span> " +
                "</div> " +
                "<div class='comment_word'> " + content + "</div> " +
                "<div class='comment_btn'> " +
                "    <a onclick='showReplyComment(this)' title='回复评论' >回复</a> " +
                "</div> " +
                "<div class='comment_list'> " +
                "    <ul> " +
                "        <li><a onclick='delComment1(this)' title='删除评论'> " +
                "            <img src='public/images2/trash.png' /> " +
                "        </a></li> " +
                "        <li><a onclick='iLike1(this)' title='点赞' class='like'> " +
                "            <img src='public/images2/beforelike.png'/><span>(0)</span></a></li> " +
                "        <li><a onclick=' ' title='评论' id='showComment2' class='comment'><img src='public/images2/c.png'><span>(0)</span></a></li> " +
                "    </ul> " +
                "</div> " +
                "<div class='comment_reply'> " +
                "</div> " +
                '</section>' + clist.innerHTML;
                e.previousElementSibling.value = '';
        }
        clickAble = true;
    });
}

function logout() {
    $.post('fd.html', {c : 'Login', a : 'logout'}, function (res, status) {
        window.location.href = 'login.html'
    })
}

var sel = document.getElementsByClassName('setting_select')[0].getElementsByTagName('li')[2];
sel.onclick = logout;
function changeBrief(e) {
    var pre = e.previousElementSibling;
    if (0 === pre.value.length) {
        return;
    }
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    $.post('yaojiaole.html', {c : 'User', a : 'changeBrief', content : pre.value}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            document.getElementById('introduce').innerHTML = pre.value;
            hideIntroduce();
        }
        clickAble = true;
    });
}

function onerrorImg() {
    var imgs = document.getElementsByTagName('img');
    for (var i = 0; i < imgs.length; i ++) {
        imgs[i].onerror = function () {
            this.src='public/images/default.jpg';
            this.onerror=null;
        }
    }
}
