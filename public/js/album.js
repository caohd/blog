;$(function(){
    'use strict';
    var addBox = $('.addPhotoListBox');
    var close = $('.closeName');
    var add = $('.photoAdd');
    var submit = $('.submitName');
    var mask = $('.int_mask');
    //显示添加相册弹出框
    function showUploadPic() {
        addBox.css("display", "block");
        mask.fadeIn();
    }
    //隐藏添加相册弹出框
    function hideUploadPic(){
        addBox.css("display", "none");
        mask.fadeOut();
    }
    add.on('click',showUploadPic);    //监听侧栏触发点击事件
    close.on('click',hideUploadPic);    //监听mask触发点击事
    submit.on('click',hideUploadPic);    //监听mask触发点击事

    var photoDel = $('.photoDelete');
    //显示删除相册按钮
    function showDel() {
        var delList = $('.delList');
        delList.css("display", "block");
        mask.fadeIn();
    }
    //隐藏删除相册按钮
    function hideDel(){
        var delList = $('.delList');
        delList.css("display", "none");
        mask.fadeOut();
    }
    photoDel.on('click',showDel);    //监听侧栏触发点击事件
    mask.on('click',hideDel);    //监听mask触发点击事

    //删除相册
    function delSection(num){
        var section = document.getElementById("section"+num);
        section.style.display = "none";
    }

    //上传相册图片
    var addPhoto = $('.addPhotoBox');
    var up = $('.photoUp');
    var closeUp = $('.closeAdd');
    //显示添加相片弹出框
    function showAddPic() {
        addPhoto.css("display", "block");
        // mask.fadeIn();
    }
    //隐藏添加相片弹出框
    function hideAddPic(){
        addPhoto.css("display", "none");
        // mask.fadeOut();
    }
    up.on('click',showAddPic);    //监听侧栏触发点击事件
    closeUp.on('click',hideAddPic);    //监听mask触发点击事


});
var curpic = '';
function likePic(e) {

    var img = e.getElementsByTagName('img')[0];
    var next = e.nextElementSibling;
    num = parseInt(next.innerText.match(/\d+/)[0]);
    var src = img.src;
    if (null === src.match(/E\.png/)) {// 已经点赞了的
        $.post('like.html', {c: 'Picture', a: 'rmLike', src: openPic}, function (res, status) {
            res = JSON.parse(res);
            if (0 === res.code) {
                img.src = 'public/images3/E.png';
                next.innerHTML = '(' + (num - 1) + ')';
                tip('取消点赞成功');
            } else {
                tip('取消点赞失败');
            }
        });
    } else { // 还没有赞
        $.post('like.html', {c: 'Picture', a: 'likePic', src: openPic}, function (res, status) {
            res = JSON.parse(res);
            if (0 === res.code) {
                img.src = 'public/images3/F.png';
                next.innerHTML = '(' + (num + 1) + ')';
                tip('点赞成功');
            } else {
                tip('点赞失败');
            }
        });
    }
}
function submitPic(e) {
    var name = e.previousElementSibling.previousElementSibling.previousElementSibling;
    console.log(name);
    if (curpic.length > 0) {
        $.post('fdas.html' + getGet(), {c : 'Picture', a : 'addPicToAlbum', name : name.value, src : curpic}, function (res, status) {
            res = JSON.parse(res);
            if (0 === res.code) {

                var list = document.getElementsByClassName('photoMinList');
                list = list[0];
                list.innerHTML = "<img onclick='showPhotoDetail(this)' title='查看图片详情' src='public/images/" + curpic + "'/>" +
                    list.innerHTML;
                name.value = '';
                var d = document.getElementById('upPicPicContent');
                curpic = '';
                d.innerHTML = '';

                var sp = document.getElementsByClassName('photoTitle')[0].getElementsByTagName('span')[1];
                var cnt = Number(sp.innerText.match(/\d+/)[0]) + 1;
                sp.innerHTML = '(' + cnt + ')' ;
                tip('上传成功');
            } else {
                tip('上传失败，请稍后再试');
            }
        });
    }
}
/**
 * 上传
 */
$(function () {
    var cpc = $('#choosePicContent');
    if (0 !== cpc.length) {
        cpc.uploadifive({
            'buttonText': '',
            'fileObjName': 'logo',
            'auto': true,
            'formData': {
                'c': 'User',
                'a': 'upl'
            },
            'queueID': 'upPicPicContent',
            'itemTemplate': '<div class="uploadifive-queue-item"></div>',
            'uploadScript': 'dddd.html',
            'onUploadComplete': function (file, data) {
                data = JSON.parse(data);
                if (0 === data.code) {
                    var d = document.getElementById('upPicPicContent');
                    curpic = data.data.logo;
                    d.innerHTML = "<img src='public/images/" + curpic + "' width='100px' height='100px'/>";
                }
            }
        });
    }
});
function delSection(e) {
    var p = e.parentNode;

    $.post('liangle.html', {c : 'Album', a : 'delete', id : p.id.substring('section'.length)}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {

            var sp = document.getElementsByClassName('photoTitle')[0].getElementsByTagName('span')[0];
            var cnt = Number(sp.innerText.match(/\d+/)[0]) - 1;
            sp.innerHTML = '(' + cnt +')';
            p.style.display = 'none';
            tip('删除相册成功');
        }
    });
}
function newAlbum() {
    var name = document.getElementById('album');
    $.post('xiangshuijiao.html', {c : 'Album', a : 'creatNewAlbum', albumname : name.value}, function (res, status) {
        res = JSON.parse(res);
        console.log(res);
        if (0 === res.code) {
            name.value = '';
            var sp = document.getElementsByClassName('photoTitle')[0].getElementsByTagName('span')[0];
            var cnt = Number(sp.innerText.match(/\d+/)[0]) + 1;
            sp.innerHTML = '(' + cnt +')';
            var wrapper = document.getElementsByClassName('wrapper')[0];
            var nalbum = {id : res.data.nid, name : res.data.albumname, cover : null};
            wrapper.innerHTML = albumTemplate(nalbum) + wrapper.innerHTML;
        }
    });
}
var openPic = '';
function showPhotoDetail(e){
    //查看大图（详情）
    var photoDet = $('.photoDetail');
    var photoMin = $('.photoMinList');
    var esrc = e.src;
    var index = e.src.lastIndexOf('/');
    var src = esrc.substring(index + 1);
    photoDet[0].getElementsByTagName('img')[0].src = 'public/images/' + src;
    $.post('fda.html', {c : 'Picture', a : 'getPicInfoBySrc', src : src}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            picInfo = res.data.picInfo;
            openPic = src;
            var l = document.getElementsByClassName('comment_list')[0].getElementsByTagName('li');
            if (null === picInfo.isLike) {
                l[0].innerHTML = "<a onclick='likePic(this)' title='点赞' id='like1' class='like' >" +
                    "         <img src='public/images3/E.png'/></a><span>(" + picInfo.belike+ ")</span>";
            } else {
                l[0].innerHTML = "<a onclick='likePic(this)' title='点赞' id='like1' class='like' >" +
                    "         <img src='public/images3/F.png'/></a><span>(" + picInfo.belike+ ")</span>";
            }
            l[1].innerHTML = "<a onclick='hidePhotoDetail(" + picInfo.id + ")' title='删除' id='delPhoto' class='delete'>" +
                "         <img src='public/images3/I.png'></a>";
        }
        document.getElementsByClassName('photoName')[0].innerHTML = picInfo.name;
    });
    photoDet.css("display", "block");
    photoMin.css("display", "none");
}
function hidePhotoDetail(id){
    $.post('fda.html', {c : 'Picture', a : 'delete', id : id}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var photoDet = $('.photoDetail');
            var photoMin = $('.photoMinList');
            var pm = photoMin[0].getElementsByTagName('img');
            // console.log()
            for (var i = 0; i < pm.length; i ++) {
                console.log(pm[i].src);
                var filename = pm[i].src;
                var index = filename.lastIndexOf('/');
                if (index === -1)
                    index = filename.lastIndexOf('\\');
                filename = filename.substring(index+1);
                console.debug(filename);
                if (filename === openPic) {
                    pm[i].style.display = 'none';
                }
            }

            var sp = document.getElementsByClassName('photoTitle')[0].getElementsByTagName('span')[1];
            var cnt = Number(sp.innerText.match(/\d+/)[0]) - 1;
            sp.innerHTML = '(' + cnt + ')' ;
            photoDet.css("display", "none");
            photoMin.css("display", "block");
        }
    });
}
function hp(){
    var photoDet = $('.photoDetail');
    var photoMin = $('.photoMinList');
    photoDet.css("display", "none");
    photoMin.css("display", "block");
}
function albumTemplate(album) {
    var rv = "<section class='locat first' id='section" + album.id + "'>" +
        "    <div class='delList' onclick='delSection(this)'>" +
        "        <img src='public/images3/H.png'/>" +
        "    </div>" +
        "    <div class='photoCover'>";
    var href
    if (0 === getGet().length)
        href = 'pictures.html?id='+ album.id;
    else
        href = 'pictures.html' + getGet() + '&id='+ album.id;
    if (null === album.cover) {
        rv += "        <a href='" + href + "'><img src='public/images2/cover5.png'/></a>";
    } else {
        rv += "        <a href='" + href + "'><img src='public/images/" + album.cover + "'/></a>";
    }
    rv += "    </div>" +
        "    <div class='photoName'>" + album.name + "</div>" +
        "</section>";
    return rv;
}
function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';
    var g = getGet();
    var index;
    if (0 !== getGet().length) {
        index  = g.lastIndexOf('&');
        if (-1 === index)
            index = g.length;
        document.getElementById('blog').href += g.substring(0, index);
        // 如果不是管理员或者在自己的home系列页面，这两个会在后面被覆盖(main.js)
        document.getElementById('friend').href += g.substring(0, index);
        document.getElementById('likeme').href += g.substring(0, index);
        document.getElementById('info').href += g.substring(0, index);
    }

    var s1 = 'user=';
    var i1 = g.lastIndexOf(s1);
    var ns = g.substring(i1 + s1.length, index);
    var list = document.getElementsByClassName('photoMinList');

    if (0 === list.length) { // album
        var nn = document.getElementsByClassName('photoAdd')[0];
        if (-1 === i1 || ns === getCookie('user') || '1' === getCookie('admin')) {
            nn.style.display = 'block';
            nn.nextElementSibling.style.display = 'block';
        } else {
            nn.style.display = 'none';
            nn.nextElementSibling.style.display = 'none';
        }
        $.post('ltwo.html' + g.substring(0, index), {c: 'Album', a: 'init'}, function (res, status) {
            res = JSON.parse(res);
            if (403 === res.code) {
                window.location.href = 'login.html';
            }
            if (0 === res.code) {
                var rtdata = res.data;
                var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
                user[0].innerHTML = "<img id='logo' src='public/images/" + res.data.logo + "'/>";
                user[1].innerHTML = "<span><a href='home.html'>" + res.data.nick + "</a></span>";

                document.getElementsByClassName('top_banner')[0].innerHTML = "<img src='public/images/" + res.data.user.logo + "' alt=''>" +
                    "<h1>" + res.data.user.nickname + "</h1>" +
                    "<!--个人简介-->" +
                    "<a id='introduce' href='javascript:showIntroduce();'>" + res.data.user.brief + "</a>";

                var albums = res.data.albums;
                document.getElementsByClassName('photoTitle')[0].getElementsByTagName('span')[0].innerHTML = '(' + albums.length + ')';
                var wrapper = document.getElementsByClassName('wrapper')[0];
                wrapper.innerHTML = '';
                console.log(wrapper);
                for (var i = 0; i < albums.length; i++) {
                    wrapper.innerHTML += albumTemplate(albums[i]);
                }
            }
            document.getElementsByTagName('body')[0].style.display = 'block';
            onerrorImg();
        });
    } else { // picture.html
        document.getElementById('album').href += g.substring(0, index);
        var nn = document.getElementsByClassName('photoUp')[0];
        var nd = document.getElementById('delPhoto');
        if (-1 === i1 || ns === getCookie('user') || '1' === getCookie('admin')) {
            nn.style.display = 'block';
            nd.style.display = 'block';
            // nn.nextElementSibling.style.display = 'block';
        } else {
            nn.style.display = 'none';
            nd.style.display = 'none';
            // nn.nextElementSibling.style.display = 'none';
        }
        $.post('fd.html' + getGet(), {c : 'Picture', a : 'allPictures'}, function (res, ststua) {
            res = JSON.parse(res);
            if (403 === res.code) {
                window.location.href = 'login.html';
            }
            if (0 === res.code) {
                list = list[0];
                var rtdata = res.data;
                var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
                user[0].innerHTML = "<img id='logo' src='public/images/" + res.data.logo + "'/>";
                user[1].innerHTML = "<span><a href='home.html'>" + res.data.nick + "</a></span>";

                document.getElementsByClassName('top_banner')[0].innerHTML = "<img src='public/images/" + res.data.user.logo + "' alt=''>" +
                    "<h1>" + res.data.user.nickname + "</h1>" +
                    "<!--个人简介-->" +
                    "<a id='introduce' href='javascript:showIntroduce();'>" + res.data.user.brief + "</a>";

                list.innerHTML = '';
                var pics = res.data.pics;
                // console.log(pics)
                for (var i = 0; i < pics.length; i ++) {
                    list.innerHTML += "<img onclick='showPhotoDetail(this)' title='查看图片详情' src='public/images/" + pics[i].src + "'/>";
                }

                document.getElementsByClassName('photoTitle')[0].innerHTML =
                    "<span>" + rtdata.album.name + "</span><span>(" + rtdata.album.pictures + ")</span>";
            }
            document.getElementsByTagName('body')[0].style.display = 'block';
            onerrorImg();
        });
    }
}

init();