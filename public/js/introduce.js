//获取元素名称
function g(id){
    return document.getElementById(id);
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
})

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
}
//鼠标松开事件
document.onmouseup = function(){
    isDialog = false;
}

//展现登录浮层
function showIntroduce(){
    g('int_dialog').style.display = 'block';
    g('int_mask').style.display = 'block';
    autoCenter(g('int_dialog'));
    fillAll(g('int_mask'));

}

//隐藏登录浮层
function hideIntroduce(){
    g('int_dialog').style.display = 'none';
    g('int_mask').style.display = 'none';
}

window.onresize = function(){
    autoCenter(g('int_dialog'));
    fillAll(g('int_mask'));
}
