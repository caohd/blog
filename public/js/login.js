var cur = 0;
//注册、登录显示切换
function loginRegister(){
    var title = document.getElementsByClassName("choose");
    var links = title[0].getElementsByTagName("li");
    var loginRegister = document.getElementsByClassName("content");
    var section = loginRegister[0].getElementsByTagName("section");
    section[1].setAttribute("style","display:none");
    for(var i=0;i<links.length;i++){
        links[i].order=i;
        links[i].onclick = function (){
            cur = i;
            for(var i=0;i<links.length;i++){
                //设置未被选中项的样式
                section[i].setAttribute("style","display:none");
                links[i].setAttribute("style","background:rgba(234,239,243,0.7);");
                links[i].getElementsByTagName("a")[0].setAttribute("style",
                    "color:#ffffff");
            }
            //设置当前选中项的样式
            section[this.order].setAttribute("style","display:block;");
            this.getElementsByTagName("a")[0].setAttribute("style",
                "color:#000");
            links[this.order].setAttribute("style","background:none;");
        }
    }
}
loginRegister();
// 监听回车事件
document.onkeydown = function (e) {
    if (13 === e.keyCode) {
        if (0 === cur) {
            login();
        } else {
            register();
        }
    }
};

// 登录的时候检测是否为以注册用户
function lUserExist() {
    $.post('nihaoa.html',
        {c : 'Login', user : document.getElementById('user').value, a : 'isUser'},
        function (res, status) {
            res = JSON.parse(res);
            var ms1 = document.getElementsByClassName('mistake')[0];
            if (res.code === 0) { // 用户存在
                ms1.style.display = 'none';
            } else {
                ms1.innerHTML = res.msg;
                ms1.style.display = 'block';
            }
        }
    );
}
// 注册的时候检测是否为以注册用户
function rUserExist() {
    $.post('wobuhao.html',
        {c : 'Login', user : document.getElementById('author').value, a : 'userExist'},
        function (res, status) {
            res = JSON.parse(res);
            var ms2 = document.getElementsByClassName('mistake')[1];
            if (res.code === 0) { // 用户存在
                ms2.style.display = 'block';
                ms2.innerHTML = res.msg;
            } else {
                ms2.style.display = 'none';
            }
        }
    );
}


// 清除的错误提示内容
function clearMs() {
    var ms1 = document.getElementsByClassName('mistake')[0];
    if (ms1.innerHTML === '用户名或密码错误' ||
        ms1.innerHTML === '用户名或密码为空' ||
        ms1.innerHTML === '错误的密码格式') {
        ms1.style.display = 'none';
    }
}

function login() {
    var ms1 = document.getElementsByClassName('mistake')[0];
    var user = document.getElementById('user');
    var pwd = document.getElementById('pwd');
    if (!checkPassword(pwd.value)) {
        ms1.style.display = 'block';
        ms1.innerHTML = '错误的密码格式';
        return;
    } else {
        ms1.style.display = 'none';
    }
    if (ms1.style.display === 'block' || pwd.value === '') {
        // to do nothing
    } else {
        $.post('woyebuhao.html',
            {
                c : 'Login',
                username : user.value,
                password : pwd.value
            },
            function (res, status) {
                res = JSON.parse(res);
                var ms1 = document.getElementsByClassName('mistake')[0];
                if (res.code === 0) { // 用户存在
                    window.location.href = 'index.html';
                } else {
                    ms1.style.display = 'block';
                    ms1.innerHTML = res.msg;
                }
            }
        );
    }
}

function truePassword() {
    var password = document.getElementById('password');
    var ms2 = document.getElementsByClassName('mistake')[1];
    if (checkPassword(password.value)) {
        ms2.style.display = 'none';
    } else {
        ms2.innerHTML = "不合法的密码";
        ms2.style.display = 'block';
    }
}

document.getElementsByClassName('forget_password')[0].onclick = function () {
    $.post('nihaoa.html', function (res, status) {

    });
};

function register() {
    var ms1 = document.getElementsByClassName('mistake')[1];
    var user = document.getElementById('author');
    var nick = document.getElementById('nickname');
    var password = document.getElementById('password');
    if (!checkPassword(password.value)) {
        ms1.innerHTML = '密码格式不正确';
        ms1.style.display = 'block';
        return;
    } else {
        ms1.innerHTML = '';
        ms1.style.display = 'none';
    }
    if (ms1.style.display === 'block' || nick.value === '') {
        // to do nothing
    } else {
        $.post('woyebuhao.html',
            {
                c : 'Register',
                username : user.value,
                nickname : nick.value,
                password : password.value
            },
            function (res, status) {
                res = JSON.parse(res);
                var ms1 = document.getElementsByClassName('mistake')[0];
                if (res.code === 0) { // 注册成功
                    window.location.href = 'information.html';
                } else {
                    ms1.style.display = 'block';
                    ms1.innerHTML = res.msg;
                }
            }
        );
    }
}









// function userExist(obj) {
//     $.post('nihaoa.html', {c : 'Login', user : document.getElementById(obj).value, a : 'isUser'}, function (res, status) {
//         res = JSON.parse(res);
//         var ms1 = document.getElementById('ms1');
//         var ms2 = document.getElementById('ms2');
//         if (res.code === 0) { // 用户存在
//             if (obj === 'user') { // 登录
//                 ms1.style.display = 'none';
//                 ms2.style.display = 'none';
//             } else { // 注册
//                 ms2.innerHTML = res.msg;
//                 ms1.style.display = 'none';
//                 ms2.style.display = 'block';
//             }
//         } else {
//             if (obj === 'user') { // 登录
//                 ms1.innerHTML = res.msg;
//                 ms1.style.display = 'block';
//                 ms2.style.display = 'none';
//             } else {
//                 // to do nothing
//                 ms1.style.display = 'none';
//                 ms2.style.display = 'none';
//             }
//         }
//     });
// }



//方法2
/*
function loginRegister(){
    var title = document.getElementsByClassName("choose");
    var links = title[0].getElementsByTagName("li");
    var loginRegister = document.getElementsByClassName("content");
    var section = loginRegister[0].getElementsByTagName("section");
    section[1].setAttribute("style","display:none");

        //选择第一个
        links[0].onclick = function (){
            //设置当前选中项的样式
            section[0].setAttribute("style","display:block;");
            links[0].getElementsByTagName("a")[0].setAttribute("style",
                "color:red");
            links[0].setAttribute("style","background:#000;");
                //设置未被选中项的样式
                section[1].setAttribute("style","display:none");
                links[1].setAttribute("style","background:#ffffff;");
                links[1].getElementsByTagName("a")[0].setAttribute("style",
                    "color:#000");
        }

        //选择第二个
        links[1].onclick = function (){
        //设置未被选中项的样式
        section[0].setAttribute("style","display:none");
        links[0].setAttribute("style","background:#ffffff;");
        links[0].getElementsByTagName("a")[0].setAttribute("style",
            "color:#000");

        //设置当前选中项的样式
        section[1].setAttribute("style","display:block;");
        links[1].getElementsByTagName("a")[0].setAttribute("style",
            "color:red");
        links[1].setAttribute("style","background:#000;");
    }

}
loginRegister();*/
