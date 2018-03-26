//上传头像
var userInfo;
var lg;
var aLocations;
var curProvince;
var curCity;
var submitAble;
var submit = document.getElementById('submit');
var email = document.getElementById('email');
var mistake = document.getElementById('mistake');
var cury = 1960;
var curm = 1;
var curd = 1;
$(function(){
    'use strict';
    var upload = $('.uploadPicBox');
    var save = $('.savePicContent');
    var close = $('.closePicContent');
    var photo = $('.photo');
    var mask = $('.mask');
    //显示博客弹出框
    function showUploadPic() {
        upload.css("display", "block");
        mask.fadeIn();
    }
    //隐藏博客弹出框
    function hideUploadPic(){
        upload.css("display", "none");
        mask.fadeOut();
    }
    photo.on('click',showUploadPic);    //监听侧栏触发点击事件
    save.on('click',function () {
        upload.css("display", "none");
        $.post('haishikun.html', {c : "User", a : 'saveLogo', logo : lg}, function (res, status) {
            var logo = document.getElementById('logo');
            logo.src = 'public/images/' + lg;
        });
        mask.fadeOut();
    });    //监听mask触发点击事
    close.on('click',hideUploadPic);    //监听mask触发点击事
});
/**
 * 上传
 */
$(function () {
    $('#choosePicContent').uploadifive({
        'buttonText'       : '',
        'fileObjName'      : 'logo',
        'auto'             : true,
        'formData'         : {
            'c'            : 'User',
            'a'            : 'upl'
        },
        'queueID'          : 'upPicPicContent',
        'itemTemplate'     : '<div class="uploadifive-queue-item"></div>',
        'uploadScript'     : 'dddd.html',
        'onUploadComplete' : function(file, data) {
            data = JSON.parse(data);
            if (0 === data.code) {
                var d = document.getElementById('upPicPicContent');
                lg = data.data.logo;
                d.innerHTML = "<img src=\"public/images/" + lg + "\" width='100px' height='100px'/>"
            }
        }
    });
});

/**
 * 检查邮箱格式是否正确
 */
function checkEmail() {
    if (regLib('mail', email.value)) {
        mistake.innerHTML = '';
        submitAble = true;
    } else {
        mistake.innerHTML = '邮箱格式不正确';
        submitAble = false;
    }
}

/**
 * 把用户的信息显示到页面上
 */
function init() {
    document.getElementsByTagName('body')[0].style.display = 'none';
    $.post('haokuna.html', {c : 'User', a : 'getCurUserInfo'}, function (res, status) {
        res = JSON.parse(res);
        if (403 === res.code) {
            window.location.href = '403.html';
        }
        var user = userInfo = res.data.user;
        var sex = document.getElementsByName('sex');
        for (var i = 0; i < sex.length; i ++) {
            if (user.sex === sex[i].value) {
                sex[i].checked = true;
            }
        }
        var logo = document.getElementById('logo');
        lg = logo.src = 'public/images/' + user.logo;
        var intro = document.getElementById('intro');
        intro.innerHTML = user.brief;
        email.value = user.mail;
        if (null === userInfo.birthday) {
            bir();
        } else {
            cury = parseInt(userInfo.birthday.toString().substr(0, 4));
            curm = parseInt(userInfo.birthday.toString().substr(5, 2));
            curd = parseInt(userInfo.birthday.toString().substr(8, 2));
            bir();
        }

        $.post('xiecuole.html', {c : 'User', a : 'allLocations'}, function (res, status) {
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
            document.getElementsByTagName('body')[0].style.display = 'block';
        });
        onerrorImg();
    });
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

/**
 * 提交数据
 */
function startFodia() {
    checkEmail();
    if (submitAble) {
        if (email.value !== userInfo.mail) {
            $.post('wobuhao.html', {c : 'Mail', a : 'checkEmail', m : email.value}, function (res, status) {
                res = JSON.parse(res);
                if (0 === res.code) {
                    update();
                } else {
                    mistake.innerHTML = '发送验证邮件失败';
                }
            });
        } else {
            update();
        }
    }
}
function update() {
    var data = {};

    data.c = 'User';
    data.a = 'updateUser';
    var bir;
    var y = getSelect(document.getElementById('birth_year')).toString();
    var m = '0' + getSelect(document.getElementById('birth_month')).toString();
    var d = '0' + getSelect(document.getElementById('birth_day')).toString();
    data.bir = y + '-' + m.substring(m.length - 2, m.length) + '-' + d.substring(d.length - 2, d.length);
    data.location = getSelect(document.getElementById('city'));
    data.intro = document.getElementById('intro').innerHTML;
    var sex = document.getElementsByName('sex');
    for (var i = 0; i < sex.length; i ++) {
        if (true === sex[i].checked) {
            data.sex = sex[i].value;
            break;
        }
    }
    $.post('dddddd.html', data, function (res, status) {
        res = JSON.parse(res);
        if (0 === res.code) {
            window.location.href = 'index.html';
        }
    });
}
/**
 * 检查是否为闰年
 * @param year
 * @returns {boolean}
 */
function isLeapYear(year) {
    if (0 === year % 4) {
        if (0 === year % 100) {
            if (0 === year % 400) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return false;
    }
}
// 监听回车事件
document.onkeydown = function (e) {
    if (13 === e.keyCode) {
        startFodia();
    }
};

bir();
init();
email.onblur = checkEmail;
submit.onclick = startFodia;