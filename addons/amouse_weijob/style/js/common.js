$(document).ready(function() {
	// 是否登录---所有页面公用
	is_login();

	// 注册页面--协议选中，则可点击“完成”按钮，否则不能点击
	$("#regist-protocol").change(function() {
		$(".shade").toggle();
	});

	//  滑动固定
	$(window).scroll(function(){
		if($(window).scrollTop()>70){
			$(".fix").addClass("fixed");
		}else{
			$(".fix").removeClass("fixed");
		}
		var height=$(window).height()/3;
		// console.log(height);
		if($(window).scrollTop()>height){
			$(".go-top").show();
		} else{
			$(".go-top").hide();
		}
	});

	$(".go-top").click(function(){
		$("html,body").stop().animate({scrollTop:0},500);
	});

	// 我的简历 模块点击显示
	$(".resume-tabs > a").click(function() {
		var cur_id = $(this).attr("id");
		var show_div = $(".item-" + cur_id);
		$(this).toggleClass("select").siblings("a").removeClass("select");
		$(".tab-item").not(show_div).hide();
		if ($(this).hasClass("select")) {
			show_div.slideDown("slow");
		} else {
			show_div.hide();
		}
	});

	// 我的账户 模块点击显示
	$(".personal-tabs > a").click(function() {
		var cur_id = $(this).attr("id");
		var show_div = $(".item-" + cur_id);
		$(this).toggleClass("select").siblings("a").removeClass("select");
		$(".tab-item").not(show_div).hide();
		if ($(this).hasClass("select")) {
			show_div.slideDown("slow");
		} else {
			show_div.hide();
		}
	});


	// 删除记录、删除收藏
	$(".delet").bind("click", function (e) {
		var cur_width = $(window).width() - 50;
		var is_ccollect=$("input[name='is_ccollect']").val();
		var ids=[];
		var tourl="";
		var flag=2;
		if (is_ccollect == 'collect') {
			$("input[name='collect_item']:checked").each(function() {
				ids.push(this.value);
			});
			tourl="index.php?g=job&m=index&a=deletecollection";
		} else {
			$("input[name='record_item']:checked").each(function() {
					ids.push(this.value);
				});
			tourl="index.php?g=job&m=index&a=deletedelivery";
		}
		e.preventDefault();
		$.Zebra_Dialog("确定删除已选择的职位信息吗？", {
			'width': cur_width,
			'custom_class': 'is_delete',
			'type': 'question',
			'show_close_button':0,
			'buttons': [{
				caption: '确定',
				callback: function() {
					$.ajax({
						url:tourl,
						data:{ids:ids},
						dataType:"json",
						success:function(data){
							var cur_widthx = $(window).width() - 50;
							if (data.status>0) {
								if (is_ccollect == 'collect') {
									$("input[name='collect_item']:checked").each(function() {
											$(this).parent().hide();
									});
								} else {
									$("input[name='record_item']:checked").each(function() {
											$(this).parent().hide();
										});
								}
								flag=1;
								// $.Zebra_Dialog(data.info, {
								// 	'width': cur_widthx,
								// 	'custom_class': 'resume_ok',
								// 	'buttons': 0,
								// 	'auto_close':'2000'
								// });
							}else{
								flag=0;
								// $.Zebra_Dialog(data.info, {
								// 	'width': cur_widthx,
								// 	'custom_class': 'resume_error',
								// 	'buttons': 0,
								// 	'type':'error'
								// });
							}
						}
					});
				}
			}, {
				caption: '取消',
				callback: function() {return;}
			}]
		});
		
		if(flag==0){
			$.Zebra_Dialog(data.info, {
				'width': cur_widthx,
				'custom_class': 'resume_ok',
				'buttons': 0,
				'auto_close':'2000'
			});
		}else if(flag==1){
			$.Zebra_Dialog(data.info, {
				'width': cur_widthx,
				'custom_class': 'resume_error',
				'buttons': 0,
				'type':'error'
			});
		}else{

		}
	});
	
	// 月薪点击li变色
	$("ul.salary-item li").click(function(){
		$("ul.salary-item li").removeClass("select");
		$(this).addClass("select");
		// console.log("ul.salary-item li");
	});


});


// 投简历
function resume_send() {
	var position_id = $("#position_id").val();
	$.ajax({
		url: 'index.php?g=Job&m=index&a=dodelivery',
		type: 'post',
		dataType: 'json',
		data: {
			'position_id': position_id
		},
		success: function(data) {
			// console.log(data['success']);
			var cur_width = $(window).width() - 50;
			if (data.status>0) {
				// console.log("投简成功resume_send");
				$.Zebra_Dialog('投简成功', {
					'width': cur_width,
					'custom_class': 'resume_ok',
					'buttons': 0,
					'auto_close':'2000'
				});
			}else{
				// console.log("投简失败resume_send");
				if(data.status==-1){
					$.Zebra_Dialog('<p class="no-resume">还木有简历噢,请到“我的简历”页面添加个人简历...</p>', {
					'width': cur_width,
					'buttons': 0,
					'custom_class': 'resume_no',
					});
				}else if(data.status==-2){
					$.Zebra_Dialog('<p class="top-times">今日投简次数已达上限，请明日再投...</p>',{
						width:cur_width,
						buttons:0,
						custom_class:'resume_no'
					});
				}else if(data.status==0){
					$.Zebra_Dialog('投简失败，', {
						'width': cur_width,
						'custom_class': 'resume_error',
						'buttons': 0,
						'auto_close':'2000'
					});
				}else{
					$.Zebra_Dialog('投简失败，该公司邮箱暂时无法接收邮件', {
						'width': cur_width,
						'custom_class': 'resume_error',
						'buttons': 0,
						'auto_close':'2000'
					});
				}
			}
		}
	});
}

// 简历收藏
function resume_collect() {
	// console.log("resume_collect");
	var position_id = $("#position_id").val();
	$.ajax({
		url: 'index.php?g=Job&m=index&a=docollection',
		type: 'post',
		dataType: 'json',
		data: {
			'collection_aboutid': position_id,
			'collection_kind':2
		},
		success: function(data) {
			var cur_width = $(window).width() - 50;
			// console.log(data['success']);
			if (data.status>0) {
				// console.log("收藏成功resume_collect");
				$.Zebra_Dialog('收藏成功', {
					'width': cur_width,
					'custom_class': 'resume_ok',
					'buttons': 0,
					'auto_close':'2000'
				});
			}else{
				// console.log("收藏失败resume_collect");
				$.Zebra_Dialog(data.info, {
					'width': cur_width,
					'custom_class': 'resume_error',
					'buttons': 0,
					'type':'error'
				});
			}
		}
	});
}

function is_login() {
	var is_login = $("input[name='is_login']").val();
	if (is_login == '1') {
		$("li.personal-item").removeClass("quit");
		$(".personal .quit").show();
		$(".personal .login").hide();
	} else {
		$("li.personal-item").addClass("quit");
		$(".personal .quit").hide();
		$(".personal .login").show();
		if ($("li.personal-item").hasClass("quit")) {
			$("li.personal-item >a").unbind('click').click(function() {
				console.log("click:false");
				return false;
			});
		}
	}
}



$("a.see-more").click(function(){
		var defuat_lists_num=1;
		var page_num=$(this).attr("page-num");
		page_num++;
		$(this).attr("page-num",page_num);
		console.log(page_num);
		$.ajax({
			url:"text.json",
			type:"get",
			data:{page_num:page_num},
			dataType:"json",
			success:function(data){
				var data_length=data.length;
				// data_length是返回的数据长度，如果小于defuat_lists_num的值，则隐藏加载按钮
				if(data_length<defuat_lists_num){
					$("a.see-more").hide();
				}

				// 以下测试用的if,到时候需要删除
				if(page_num>3){
					$("a.see-more").hide();
				}
				// console.log(data_length);
				$.each(data,function (i,n){
					var src='<li><a class="clearfix" href="';
					src+=n["url"]+'">';
					src+='<p class="fl job-item-tit">'+n["job_tit"]+'</p><p class="fl job-item-intro"><span class="fl salary">'+n["salary"]+'</span><span class="fl place">'+n["place"]+'</span><span class="fl company-name last">'+n["company_name"]+'</span></p></a></li>';
					$(".job-item").append(src);
				});
			}
		});
	});




