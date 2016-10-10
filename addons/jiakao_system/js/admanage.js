/*
Ad.js
广告轮播
author：穿越的一只小猪
ad_config：外部配置
*/
var Ad = (function($,ad_config){

	var config = {
		save_url : ad_config.ajax_url.save,
		show_url : ad_config.ajax_url.show,
		del_url : ad_config.ajax_url.del,
		order_url : ad_config.ajax_url.order
	};
	// 编辑按钮触发
	var edit_btn = function(obj) {
		// 将输入框置为可编辑状态
		toggle_input(obj);
		// 将“保存”按钮进行展示
		toggle_btn(obj);
	}
	var cancel_btn = function(obj) {
		// 将输入框组设置为不可编辑
		toggle_input(obj);
		// 按钮切换
		toggle_btn(obj);
	}
	/*
	按钮组切换
	*/
	var toggle_btn = function(obj) {
		var _self = $(obj);
		// 显示保存，取消按钮；隐藏编辑、删除、下架按钮
		_self.parent("div").children("[ids_group='group1']").toggle();
		// 显示编辑、删除、下架按钮
		_self.parent("div").children("[ids_group='group2']").toggle();
	}
	/*
	输入框状态切换
	*/
	var toggle_input = function(obj) {
		// 对象本身
		var _self = $(obj);
		// 对象的最外层父节点
		var _father_div = _self.parents("[ids_group='father_div']");
		// 标题
		var _title = _father_div.find("[ids_group='title']");
		// 链接
		var _link = _father_div.find("[ids_group='link']");

		// 编辑状态切换
		if(_title.prop("disabled")) {
			_title.prop("disabled",false);
		}else{
			_title.prop("disabled",true);
		}

		if(_link.prop("disabled")) {
			_link.prop("disabled",false);
		}else{
			_link.prop("disabled",true);
		}

	}
	/*
	保存按钮
	兼容编辑保存、新增保存
	*/
	var save_btn = function(id,type,obj) {
		var _self = $(obj);
		// 对象的最外层父节点
		var _father_div = _self.parents("[ids_group='father_div']");
		// 标题
		var _title = _father_div.find("[ids_group='title']");
		// 链接
		var _link = _father_div.find("[ids_group='link']");
		// 图片
		var _img = _father_div.find("img");

		// ajax提交保存
		$.ajax({
			type:'post',
			data:{id:id,title:_title.val(),link:_link.val(),imgurl:_img.attr('src'),type:type},
			url: config.save_url,
			success : function(data) {
				if(data.res == 100 ) {
					alert('操作成功');
					window.location.href='';
				}else{
					alert('操作失败，请刷新后重试');
					console.info(data.msg);
				}
			}
		})
	}
	/*
	是否加入轮播
	*/
	var show_btn = function (id,type,obj) {
		var _self = $(obj);

		// ajax
		$.ajax({
			type:'post',
			data:{id:id,type:type},
			url: config.show_url,
			success : function(data) {
				if(data.res == 100 ) {
					if(type== 'on') {
						_self.html("已上架");
						_self.removeClass("btn-default");
						_self.addClass("btn-success");
					}else if(type == 'off') {
						_self.html("已下架");
						_self.removeClass("btn-success");
						_self.addClass("btn-default");
					}else{
						return false;
					}
					alert('操作成功，点击‘确定’页面刷新，重新排序');
					window.location.href='';
				}else{
					alert('上下架操作失败，请刷新后重试');
					console.info(data.msg);
				}
			}
		})
	}
	/*
	删除操作
	*/
	var delete_btn = function (id) {
		var token = $("#token").val();
		if(confirm("‘确认’删除该条广告图片？")) {

		}else{
			return false;
		}
		$.ajax({
			type:'post',
			data:{id:id,token:token},
			url: config.del_url,
			success : function(data) {
				if(data.res == 100 ) {
					alert('删除成功，点击‘确定’页面刷新，重新排序');
					window.location.href='';
				}else{
					alert('删除操作失败，请刷新后重试');
					console.info(data.msg);
				}
			}
		})
	}
	/*
	排序功能
	*/
	var order_btn = function(id,type) {
		if(id == '' || type == '') {
			alert('排序失败');
			return false;
		}
		//ajax
		$.ajax({
			type:'post',
			data:{id:id,type:type},
			url: config.order_url,
			success : function(data) {
				if(data.res == 100 ) {
					// alert('排序成功，点击‘确定’页面刷新，重新排序');
					window.location.href='';
				}else{
					alert('排序操作失败, 可能已是末尾或者排头');
					console.info(data.msg);
				}
			}
		})

	}
	/*
	上传模态框打开
	*/
	var upload_modal_btn = function() {
		$("#file_upload_modal").modal('show');
	}
	/*
	将按钮替换为图片地址
	*/
	var imgLoad = function (url) {
		//<img src="http://static.bootcss.com/www/assets/img/gruntjs.png" style="height:150px;width:320px;" class="img-thumbnail">
		img_obj = '<img src="'+url+'" style="height:150px;width:320px;" class="img-thumbnail">';
		$('#image_placeholder').html(img_obj);
	}
	/* 
	定义swfu的succes
	*/
	var uploadSuccess = function (file,serverData) {
		serverData = eval("("+serverData+")");
		try {
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setComplete();
			progress.setStatus("上传成功:"+serverData.msg);
			progress.toggleCancel(false);
			// 将获取的图片地址进行赋值
			imgLoad(serverData.url);
		} catch (ex) {
			this.debug(ex);
		}
	}
	/*
	定义swfu的error
	*/
	var uploadError = function (file, errorCode, message) {
		try{
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
			case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
				progress.setStatus("上传错误: " + message);
				this.debug("错误代码: HTTP错误, 文件名: " + file.name + ", 信息: " + message+"页面3秒后刷新");
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
				progress.setStatus("上传失败");
				this.debug("错误代码: 上传失败, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message+"页面3秒后刷新");
				break;
			case SWFUpload.UPLOAD_ERROR.IO_ERROR:
				progress.setStatus("服务器 (IO) 错误");
				this.debug("错误代码: IO 错误, 文件名: " + file.name + ", 信息: " + message+"页面3秒后刷新");
				break;
			case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
				progress.setStatus("安全错误");
				this.debug("错误代码: 安全错误, 文件名: " + file.name + ", 信息: " + message+"页面3秒后刷新");
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				progress.setStatus("超出上传限制.");
				this.debug("错误代码: 超出上传限制, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message+"页面3秒后刷新");
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
				progress.setStatus("无法验证.  跳过上传.");
				this.debug("错误代码: 文件验证失败, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message+"页面3秒后刷新");
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
				// If there aren't any files left (they were all cancelled) disable the cancel button
				if (this.getStats().files_queued === 0) {
					document.getElementById(this.customSettings.cancelButtonId).disabled = true;
				}
				progress.setStatus("取消");
				progress.setCancelled();
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				progress.setStatus("停止");
				break;
			default:
				progress.setStatus("未处理的错误: " + errorCode+"页面3秒后刷新");
				this.debug("错误代码: " + errorCode + ", 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
				break;
			}
		} catch (ex) {
	        this.debug(ex);
	    }

		setTimeout(function () {
			$("#file_upload_modal").modal('hide');
			window.location.href='';
		}, 2000);
	}

	return {
		edit_btn : edit_btn,
		toggle_btn : toggle_btn,
		cancel_btn : cancel_btn,
		upload_modal_btn : upload_modal_btn,
		uploadSuccess:uploadSuccess,
		uploadError:uploadError,
		save_btn:save_btn,
		show_btn:show_btn,
		delete_btn:delete_btn,
		order_btn:order_btn
	}
}($,ad_config));