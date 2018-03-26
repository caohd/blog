//上传头像
var lg;
var clickAble = true;
var cemail = '';
;$(function(){
    'use strict';
    var upload = $('.uploadPicBox');
    var save = $('.savePicContent');
    var close = $('.closePicContent');
    var photo = $('.photo');
    var mask = $('.mask');
    //显示弹出框
    function showUploadPic() {
        upload.css("display", "block");
        mask.fadeIn();
    }
    //隐藏弹出框
    function hideUploadPic(){
        upload.css("display", "none");
        mask.fadeOut();
    }
    photo.on('click',showUploadPic);    //监听侧栏触发点击事件
    close.on('click',hideUploadPic);    //监听mask触发点击事
    save.on('click',function () {
        upload.css("display", "none");
        $.post('haishikun.html', {c : "User", a : 'saveLogo', logo : lg}, function (res, status) {
            var logo = document.getElementById('logo');
            logo.src = 'public/images/' + lg;
            document.getElementById('clogo').innerHTML = "<img src='public/images/" + lg + "'/>";
            document.getElementsByClassName('top_banner')[0].innerHTML = "<img src='public/images/" + lg+ "' alt=''>";

        });
        mask.fadeOut();
    });    //监听mask触发点击事

    var change = $('.changePassword');

    change.on('click',showPasswordBox);    //监听侧栏触发点击事件


});
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
                    lg = data.data.logo;
                    d.innerHTML = "<img src=\"public/images/" + lg + "\" width='100px' height='100px'/>"
                }
            }
        });
    }
});
//显示修改密码框
function showPasswordBox() {
    var password = $('.password');
    password.css("display", "block");
    // mask.fadeIn();
}
//隐藏修改密码框
function hidePasswordBox(){
    var password = $('.password');
    password.css("display", "none");
    // mask.fadeOut();
}
var curProvince;
var curCity;
var cury = 1960;
var curm = 1;
var curd = 1;
var password = '';
function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';
    var g = getGet();
    var index;
    if (0 !== getGet().length) {
        index  = g.lastIndexOf('&');
        if (-1 === index)
            index = g.length;
        document.getElementById('blog').href += g.substring(0, index);
        document.getElementById('album').href += g.substring(0, index);
        document.getElementById('likeme').href += g.substring(0, index);
        document.getElementById('friend').href += g.substring(0, index);

    }

    var list = document.getElementById('infoList');
    $.post('lianlian.html' + getGet(), {c : 'User', a : 'infoInit'}, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            var userInfo = res.data.user;
            var user = document.getElementsByClassName('user')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
            user[0].innerHTML = "<img id='logo' src='public/images/" + res.data.logo + "'/>";
            user[1].innerHTML = "<span><a href='home.html'>" + res.data.nick + "</a></span>";

            document.getElementsByClassName('top_banner')[0].innerHTML = "<img src='public/images/" + userInfo.logo+ "' alt=''>" +
                "<h1>" + userInfo.nickname + "</h1>" +
                "<!--个人简介-->" +
                "<a id='introduce' href='javascript:showIntroduce();'>" + userInfo.brief + "</a>";
            document.getElementById('clogo').innerHTML = "<img src='public/images/" + userInfo.logo + "'/>";

            if (null === list) {
                var birth = new Date(userInfo.birthday);
                cury = birth.getFullYear();
                curm = birth.getMonth() + 1;
                curd = birth.getDate();
                bir();

                $.get('public/js/locations.js', function (res, status) {
                    res = JSON.parse(res);
                    aLocations = res.data.locations;
                    var provinces = document.getElementById('province');
                    for (var i = 0; i < aLocations.length; i ++) {
                        if (userInfo.locationid === aLocations[i].id) {
                            curProvince = aLocations[i].area;
                            curCity = aLocations[i].city;
                        }
                    }

                    provinces.innerHTML = '';
                    for (var i = 0; i < aLocations.length; i ++) {
                        if (i === 0 || aLocations[i].area !== aLocations[i-1].area) {
                            if (curProvince === aLocations[i].area) {
                                provinces.innerHTML += "<option selected='selected' value='" + aLocations[i].id + "'>" + aLocations[i].area + "</option>";
                            } else {
                                provinces.innerHTML += "<option value='" + aLocations[i].id + "'>" + aLocations[i].area + "</option>";
                            }
                        }
                    }
                    changeCity();
                });

                var sex = document.getElementsByName('sex');
                for (var i = 0; i < sex.length; i ++) {
                    if (userInfo.sex === sex[i].value) {
                        sex[i].checked = true;
                    }
                }
                document.getElementById('email').value = cemail = userInfo.mail;
                document.getElementById('intro').value = userInfo.brief;
                document.getElementById('nickname').placeholder = userInfo.nickname;
                document.getElementById('username').innerHTML = userInfo.username;
                document.getElementById('toinfo').href += g.substring(0, index);
                mistakeInfo();
            } else {
                document.getElementById('editinfo').href += g.substring(0, index);

                var s1 = 'user=';
                var i1 = g.lastIndexOf(s1);
                var ns = g.substring(i1 + s1.length, index);
                if (-1 === i1 || ns === getCookie('user') || '1' === getCookie('admin')) {
                    document.getElementsByClassName('write_info')[0].style.display = 'block';
                } else {
                    document.getElementsByClassName('write_info')[0].style.display = 'none';
                }
                list.innerHTML = '';
                list.innerHTML += "<p>账号：<span>" + userInfo.username + "</span></p>" +
                    "<p>昵称：<span>" + userInfo.nickname + "</span></p>" +
                    "<p>性别：<span>" + (userInfo.sex === '3' ? '保密' : (userInfo.sex === '0' ? '男' : '女')) + "</span></p>" +
                    "<p>生日：<span>" + userInfo.birthday + "</span></p>" +
                    "<p>所在地：<span>" + userInfo.city + "</span></p>" +
                    "<p>邮箱：<span>" + userInfo.mail + "</span></p>" +
                    "<p>个性签名：<span>" + userInfo.brief + "</span></p>";
            }
        } else if (403 === res.code) {
            window.location.href = 'login.html';
        }
        onerrorImg();

        document.getElementsByTagName('body')[0].style.display = 'block';
    });
}
function mistakeInfo() {
    var mistake = document.getElementById('emailMistake');
    var email = document.getElementById('email').value;
    if (regLib('mail', email)) {
        mistake.style.display = 'none';
    } else {
        mistake.style.display = 'block';
    }
}
/**
 * 当改变了省份的时候更新城市的内容
 */
function changeCity() {
    var city = document.getElementById('city');
    var provinces = document.getElementById('province');
    city.innerHTML = '';
    /**
     * 获得select选中的元素
     * @param obj
     * @returns {*}
     */
    for(var i=0; i < provinces.length; i ++) {
        if (true === provinces.options[i].selected) {
            curProvince = provinces.options[i].innerHTML;
            break;
        }
    }
    for (var i = 0; i < aLocations.length; i ++) {
        if (aLocations[i].area === curProvince) {
            if (curCity === aLocations[i].city) {
                city.innerHTML += "<option selected='selected' value='" + aLocations[i].id + "'>" + aLocations[i].city + "</option>";
            } else {
                city.innerHTML += "<option value='" + aLocations[i].id + "'>" + aLocations[i].city + "</option>";
            }
        }
    }
}
var changeAble = false;
function isRight(e) {
    var password = e.value;
    $.post('fdsa.html', {c : 'Login', a : 'truePassword', password : password}, function (res, status) {
        res = JSON.parse(res);
        if (0 !== res.code) {
            tip('密码错误');
        } else {
            changeAble = true;
        }
    });
}
function firstPassword(e) {
    if (checkPassword( e.value)) {
        password = e.value;
    } else {
        tip('密码格式错误');
    }
}
function confirmPassword(e) {
    if (checkPassword( e.value)) {
        if (e.value !== password) {
            tip('两次的密码不一致');
        } else {
            if (changeAble) {
                $.post('dsafa.html', {c: 'User', a: 'changePassword', password: e.value}, function (res, status) {
                    hidePasswordBox();
                    tip('修改密码成功');
                });
            }
        }
    } else {

        tip('密码格式错误');
    }
}

function submitChange(e) {
    var data = {};

    data.c = 'User';
    data.a = 'updateUser';
    var bir;
    var y = getSelect(document.getElementById('birth_year')).toString();
    var m = '0' + getSelect(document.getElementById('birth_month')).toString();
    var d = '0' + getSelect(document.getElementById('birth_day')).toString();
    data.bir = y + '-' + m.substring(m.length - 2, m.length) + '-' + d.substring(d.length - 2, d.length);
    data.location = getSelect(document.getElementById('city'));
    data.intro = document.getElementById('intro').value;
    var nick = document.getElementById('nickname');
    if ('' !== nick.value) {
        data.nickname = nick.value;
    }
    var sex = document.getElementsByName('sex');
    for (var i = 0; i < sex.length; i ++) {
        if (true === sex[i].checked) {
            data.sex = sex[i].value;
            break;
        }
    }
    if (clickAble) {
        clickAble = false;
    } else {
        return;
    }
    $.post('dddddd.html', data, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            emailchange(email);
            tip('没有更改信息');
        } else if (1 === res.code) {
            emailchange(email);
        }
    });
}
function emailchange(email) {
    if (cemail != email.value) {
        $.post('changeEmail.html', {c: 'Mail', a: 'changeBindEmail', email: email.value}, function (res, status) {
            res = JSON.parse(res);
            if (0 === res.code)
                window.location.href = 'info.html';
        });
    } else {
        window.location.href = 'info.html';
        tip('修改信息成功');
    }
}
/**
 * 显示年月日
 */
function bir() {
    // year
    var yeah = document.getElementById('birth_year');
    yeah.innerHTML = '';
    for (var i = 2017; i >= 1960; i --) {
        if (i === cury) {
            yeah.innerHTML += "<option selected='selected' value='" + i +"'>" + i + "</option>";
        } else {
            yeah.innerHTML += "<option value='" + i +"'>" + i + "</option>";
        }
    }
    // month
    var month = document.getElementById('birth_month');
    month.innerHTML = '';
    for (var i = 0; i < 12; i ++) {
        if ((i + 1) === curm) {
            month.innerHTML += "<option selected='selected' value='" + (i + 1) +"'>" + (i + 1) + "</option>";
        } else {
            month.innerHTML += "<option value='" + (i + 1) +"'>" + (i + 1) + "</option>";
        }
    }
    // day
    changeDay();
}

/**
 * 当选择了月份的时候更新日期的数量
 */
function changeDay() {
    var yeah = document.getElementById('birth_year');
    var month = document.getElementById('birth_month');
    var day = document.getElementById('birth_day');
    var daynum;
    // start from 0
    var yindex = yeah.selectedIndex;
    var mindex = month.selectedIndex;
    switch (mindex) {
        // 1, 3, 5, 7, 8, 10 ,12月
        case 0:
        case 2:
        case 4:
        case 6:
        case 7:
        case 9:
        case 11:
            daynum = 31;
            break;
        // 4, 6, 9, 11月
        case 3:
        case 5:
        case 8:
        case 10:
            daynum = 30;
            break;
        // 2月
        default:
            if (isLeapYear(yeah.options[yindex].value)) {
                daynum = 29;
            } else {
                daynum = 28;
            }
    }

    day.innerHTML = '';
    for (var i = 0; i < daynum; i ++) {
        if ((i + 1) === curd) {
            day.innerHTML += "<option selected='selected' value='" + (i + 1) +"'>" + (i + 1) + "</option>";
        } else {
            day.innerHTML += "<option value='" + (i + 1) +"'>" + (i + 1) + "</option>";
        }
    }
}
init();