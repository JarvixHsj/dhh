// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
// 例子：
// date_format(date, "yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
// date_format(date, "yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
function date_format(date, fmt) {
    if(date == null || !date) return '';

    date = new Date(date);
    var o = {
        "M+" : date.getMonth()+1,                 //月份
        "d+" : date.getDate(),                    //日
        "h+" : date.getHours(),                   //小时
        "m+" : date.getMinutes(),                 //分
        "s+" : date.getSeconds(),                 //秒
        "q+" : Math.floor((date.getMonth()+3)/3), //季度
        "S"  : date.getMilliseconds()             //毫秒
    };
    if(/(y+)/.test(fmt)) {
        fmt=fmt.replace(RegExp.$1, (date.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
}

/**
 * 对Date的扩展，将 Date 转化为指定格式的String
 * 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q) 可以用 1-2 个占位符
 * 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
 * eg:
 * (new Date()).pattern("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
 * (new Date()).pattern("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04
 * (new Date()).pattern("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04
 * (new Date()).pattern("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04
 * (new Date()).pattern("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18

使用：(eval(value.replace(/\/Date\((\d+)\)\//gi, "new Date($1)"))).pattern("yyyy-M-d h:m:s.S");
 */
Date.prototype.pattern=function(fmt) {
    var o = {
    "M+" : this.getMonth()+1, //月份
    "d+" : this.getDate(), //日
    "h+" : this.getHours()%12 == 0 ? 12 : this.getHours()%12, //小时
    "H+" : this.getHours(), //小时
    "m+" : this.getMinutes(), //分
    "s+" : this.getSeconds(), //秒
    "q+" : Math.floor((this.getMonth()+3)/3), //季度
    "S" : this.getMilliseconds() //毫秒
    };
    var week = {
    "0" : "/u65e5",
    "1" : "/u4e00",
    "2" : "/u4e8c",
    "3" : "/u4e09",
    "4" : "/u56db",
    "5" : "/u4e94",
    "6" : "/u516d"
    };
    if(/(y+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    if(/(E+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "/u661f/u671f" : "/u5468") : "")+week[this.getDay()+""]);
    }
    for(var k in o){
        if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
}

function DateFormatter(value, format) {
    if (value == null || value == '') {
        return '';
    }
    var dt;
    if (value instanceof Date) {
        dt = value;
    } else {
        dt = new Date(value * 1000);
    }
    return dt.pattern(format);
}

function set_cookie(name, value, expires) {
    if(cookie_prefix) name = cookie_prefix+name;
    if(!cookie_path) cookie_path = '/';
    if(!cookie_domain) cookie_domain = '';
    if(!cookie_secure) cookie_secure = false;
    var options = {expires:expires, path:cookie_path, domain:cookie_domain, secure:cookie_secure};
    $.cookie(name, value, options);
}

function get_cookie(name) {
    if(cookie_prefix) name = cookie_prefix+name;
    return $.cookie(name);
}

function render_yes_no(rowdata, index, value) {
    var h = '';
    if(parseInt(value) == 1) h = '是';
    else h = '否';

    return h;
}

//设置带滚动条的div高度
function resize_scroll_window(id, heightdiff) {
    var h = null;

    h = $(window).height() || 500;

    h = h + heightdiff; //误差

    $("#"+id).css({overflow:'auto'}).height(h);
}

function clickCheckbox(){
    $(".chooseAll").click(function(){
        var status=$(this).prop('checked');
        $("tbody input[type='checkbox']").prop("checked",status);
        $(".chooseAll").prop("checked",status);
        $(".unsetAll").prop("checked",false);
    });
    $(".unsetAll").click(function(){
        var status=$(this).prop('checked');
        $("tbody input[type='checkbox']").each(function(){
            $(this).prop("checked",! $(this).prop("checked"));
        });
        $(".unsetAll").prop("checked",status);
        $(".chooseAll").prop("checked",false);
    });
}

function commonAjaxPost(url, json, callbackFun, callbackParam) {
	commonAjax(url, json, "post", callbackFun, callbackParam);
}

function commonAjax(url, json, callbackType, callbackFun, callbackParam) {
	var $parent = window.parent || window;

    //func.loading.show();
    $.ajax({
        type: callbackType,
        url: url,
        data: json,
        dataType: "json",
        success: function (json) {
            setTimeout(function () {
                if(json.status == -10001){
                    $parent.location.href = admin_defult_url;
                } else {
                    if(callbackFun){ callbackFun(json, callbackParam); }
                }
            }, 0);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            /*console.log(XMLHttpRequest);
            console.log(textStatus);
            console.log(errorThrown);*/
            $parent.$.ligerDialog.warn("获取数据失败,请重新尝试!");
            //func.loading.hide();
        }
    });
}