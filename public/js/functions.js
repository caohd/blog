/**
 * 获得页面的GET参数
 */
function getGet() {
    var url = window.location.href;
    var rv = '';
    if (-1 !== url.indexOf('?')) {
        rv = url.substr(url.indexOf('?'));
    }
    return rv;
}

/**
 * 检测用户输入的用户名是否合法
 */
var nl = {
    tel     : /^13[0-9]|14[5|7]|15[0-9]|18[0-9]\d{8}$/,
    mail    : /^\w{1,}@\w{1,}\.\w{1,}$/,
    chinese : /[\u4e00-\u9fa5]/,
    // 数字或字母开头，中间可以出现_ 密码长度9-15
    passwd  : /^[0-9a-zA-Z]{1}[\-_\$\&0-9a-zA-Z]{8,15}/,
    p1      : /.*[A-Z]+.*/,
    p2      : /.*[a-z]+.*/,
    p3      : /.*[0-9]+.*/,
    p4      : /.*[\-_\$\&]+.*/
};
function regLib(name, obj) {
    var reg = nl[name];
    return reg.test(obj);
}

/**
 * 检测密码是否合法
 * 大写字母，小写字母，数字，特殊字符(_-&$)这四种类型至少出现三种
 * 开头不能为特殊字符，密码长度为9-16
 * @param obj
 * @returns {boolean}
 */
function checkPassword(obj) {
    if (regLib('passwd', obj)) {
        var pn = 0;
        if (regLib('p1', obj)) {
            pn ++;
        }
        if (regLib('p2', obj)) {
            pn ++;
        }
        if (regLib('p3', obj)) {
            pn ++;
        }

        if (regLib('p4', obj)) {
            pn ++;
        }
        if (pn > 2)
            return true;
    }
    return false;
}

/**
 * 获得select选中的元素
 * @param obj
 * @returns {*}
 */
function getSelect(obj) {
    var rv;
    for(var i=0; i < obj.length; i ++) {
        if (true === obj.options[i].selected) {
            rv = obj.options[i].value;
            break;
        }
    }
    return rv;
}

/**
 *
 * @param cname
 */
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++)
    {
        var c = ca[i].trim();
        if (c.indexOf(name) === 0)
            return c.substring(name.length,c.length);
    }
    return "";
}

if (self !== top) {
    top.location = '/';
}