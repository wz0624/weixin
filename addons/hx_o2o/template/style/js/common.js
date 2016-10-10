/**
 * 弹出提示框
 * @param  {[type]} title   [description]
 * @param  {[type]} content [description]
 * @param  {[type]} callback [description]
 * @return {[type]}         [description]
 */
function alert_modal(content, callback)
{
    $('<div id="alert-modal" class="modal"><div class="modal-content"><p>' + content + '</p></div></div>').appendTo('body');
    $('#alert-modal').openModal({
        complete: function()
        {
            $('#alert-modal').remove();
            callback && callback();
        }
    });
}

/**
 * 弹出确认框
 * @param  {[type]}   title    [description]
 * @param  {[type]}   content  [description]
 * @param  {Function} callback [description]
 * @return {[type]}            [description]
 */
function alert_confirm(content, callback)
{
    var title = '';
    $('<div id="alert-confirm" class="modal"><div class="modal-content"><p>' + content + '</p></div><div class="modal-footer"><a href="javascript:void(0)" class="modal-action waves-effect waves-green btn-flat" id="alert-confirm-ok">确认</a></div></div>').appendTo('body');

    $('#alert-confirm').openModal({
        dismissible: false,
        complete: function()
        {
            $('#alert-confirm').remove();
        }
    });

    $('#alert-confirm-ok').click(function()
    {
        callback && callback();
        $('#alert-confirm').closeModal({
            complete: function()
            {
                $('#alert-confirm').remove();
            }
        });
    });
}

/**
 * 弹出选择框
 * @param  {[type]}   title    [description]
 * @param  {[type]}   content  [description]
 * @param  {Function} callback [description]
 * @return {[type]}            [description]
 */
function alert_choose(content, callback)
{
    var title = '';
    $('<div id="alert-choose" class="modal"><div class="modal-content"><p>' + content + '</p></div><div class="modal-footer"><a href="javascript:void(0)" class="modal-action waves-effect waves-green btn-flat btn_ok" id="alert-choose-ok">确认</a><a href="javascript:void(0)" class="modal-action modal-close waves-effect waves-red btn-flat btn_cancle">取消</a></div></div>').appendTo('body');

    $('#alert-choose').openModal({
        dismissible: false,
        complete: function()
        {
            $('#alert-choose').remove();
        }
    });

    $('#alert-choose-ok').click(function()
    {
        callback && callback();
        $('#alert-choose').closeModal({
            complete: function()
            {
                $('#alert-choose').remove();
            }
        });
    });
}

/**
 * 弹出自动消失提示
 * @param  {[type]}   content  [description]
 * @param  {Function} callback [description]
 * @param  {[type]}   time     [description]
 * @return {[type]}            [description]
 */
function tips(content, callback, time)
{
    Materialize.toast(content, time ? time : 3000, 'teal accent-4', callback);
}

function loading(close)
{
    var bg = $('<div id="loading"></div>').css({
        'width': '100%',
        'height': $(document).outerHeight(),
        'backgroundColor': '#FFFFFF',
        'opacity': 0.7,
        'position': 'absolute',
        'zIndex': 1001,
        'top': 0,
        'left': 0
    }), pre = $('<div class="preloader-wrapper big active" id="progress"><div class="spinner-layer spinner-green-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>').css({
        'position': 'fixed',
        'top': parseInt($(window).height()) / 2 - 32,
        'left': parseInt($(window).width()) / 2 - 32,
        'zIndex': 1002
    });

    if (close)
    {
        $('#loading').fadeOut(500, function()
        {
            $(this).remove();
        });

        $('#progress').fadeOut(300, function()
        {
            $(this).remove();
        });
    }
    else
    {
        bg.appendTo('body');
        pre.appendTo('body');
    }
}

/**
 * 异步
 * @param  {[type]}   url      [description]
 * @param  {[type]}   data     [description]
 * @param  {Function} callback [description]
 * @param  {[type]}   type     [description]
 * @param  {[type]}   dataType [description]
 * @return {[type]}            [description]
 */
function ajax(url, data, callback, type, dataType)
{
    $.ajax({
        "url": url,
        "data": data,
        "type": type ? type : 'POST',
        "dataType": dataType ? dataType : 'JSON',
        "beforeSend": function()
        {
            loading();
        },
        "success": function(result)
        {
            callback && callback(result);
        },
        "error": function()
        {
            alert_modal('请求失败');
        },
        "complete": function()
        {
            loading(true);
        }
    });
}

/**
 * 验证码60秒发送限制
 * @param  {[type]} o [description]
 * @return {[type]}   [description]
 */
var mobileCaptchaTiming = 60;
function mobile_captcpa_countdown(o)
{
    if (mobileCaptchaTiming == 0)
    {
        o.removeClass('disabled').html('获取验证码');
        mobileCaptchaTiming = 60;
    }
    else
    {
        o.addClass('disabled').html('重新发送（' + mobileCaptchaTiming + ')');
        --mobileCaptchaTiming;
        setTimeout(function()
        {
            mobile_captcpa_countdown(o);
        }, 1000);
    }
}