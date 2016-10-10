var KaoShi = {
	//初始化
	web_root:'',//根路径
	answer:'',//题目答案
	tid:'',//题目id
	randid:'',//随机出题的当前顺序id
	ans_content:'',//答案内容
	rand_obj:'',//随机出题的题号数组
	t_info:'',//考题信息存储缓存
	type:'',//考试类型 随机，模拟，顺序
	num:
	{
		total:0,//总共有多少道题目
		success:0,
		wrong:0
	},
	init:function(data,type,num)//type用来标记是哪种答题类型
	{
		data = arguments[0]?data:'';
		KaoShi.type = type;
		KaoShi.num.total=num;
		if(data)//获取初始化参数，是否按照参数顺序出题
		{
			var obj = eval("("+data+")");
			KaoShi.getinfo(obj[0]);
			//设置初次题号为
			KaoShi.tid=obj[0];
			KaoShi.randid=0;
			KaoShi.rand_obj = obj;
		}
		else
		{
			//获取第一道题目
			KaoShi.getinfo(1);
			//设置初次题号为1
			KaoShi.tid=1;
		}
		//初始化本地存储
		localStorage["sx_"] = '';
		localStorage["sj_"] = '';
		localStorage["mn_"] = '';
		localStorage["ct_now_"] = '';
		
		
	},
	refresh:function()
	{
		//界面初始化
		$("#title").html("");
		$("#title").html("");
		$("#pic").html("");
		$("#items").html("");
		$("#alert").html("");
	},
	//获取题目
	getinfo:function(id)
	{
		KaoShi.refresh();
		$.ajax({
			type:'post',
			url:KaoShi.web_root+'app/index.php?i='+window.sysinfo.uniacid+'&c=entry&do=sequentajax&m=jiakao_system&rand='+Math.random(),
			data:{"t_id":id},
			success:function(data)
			{
				//填充内容
				KaoShi.appendinfo(data);
			}
		})
	},
	//将获取的内容添加到页面上
	appendinfo:function(data)
	{
		var str = "<ul class='list-group'>";
		var obj = eval("("+data+")");
		//共享题目信息
		KaoShi.t_info=obj;
		//答案
		KaoShi.answer=obj.answer;
		//答案内容
		switch(obj.answer)
		{
			case 'A':
				KaoShi.ans_content=obj.s_a;
				break;
			case 'B':
				KaoShi.ans_content=obj.s_b;
				break;
			case 'C':
				KaoShi.ans_content=obj.s_c;
				break;
			case 'D':
				KaoShi.ans_content=obj.s_d;
				break;
			default:
				KaoShi.ans_content='';
		}
		
		//题目内容
		var title = "<strong>"+obj.t_id+"、 "+obj.title+"</strong>";
		$("#title").html(title);
		if(obj.type_desc == 0)//选择题
		{
			if(obj.s_a!='')
			{
				str += "<li class=\"list-group-item\" id=\"s_A\" onclick=\"KaoShi.confirm(this)\"><strong>A</strong> "+obj.s_a+"</li>";
			}
			if(obj.s_b!='')
			{
				str += "<li class=\"list-group-item\" id=\"s_B\" onclick=\"KaoShi.confirm(this)\"><strong>B</strong> "+obj.s_b+"</li>";
			}
			if(obj.s_c!='')
			{
				str += "<li class=\"list-group-item\" id=\"s_C\" onclick=\"KaoShi.confirm(this)\"><strong>C</strong> "+obj.s_c+"</li>";
			}
			if(obj.s_d!='')
			{
				str += "<li class=\"list-group-item\" id=\"s_D\" onclick=\"KaoShi.confirm(this)\"><strong>D</strong> "+obj.s_d+"</li>";
			}
		}
		if(obj.type_desc == 1)//判断题
		{
			str += "<li class=\"list-group-item\" id=\"s_Y\" onclick=\"KaoShi.confirm(this)\">正确</li>";
			str += "<li class=\"list-group-item\" id=\"s_N\" onclick=\"KaoShi.confirm(this)\">错误</li>";
		}
		str += "</ul>";
		$("#items").html(str);
		//添加图片
		if(obj.pic_url != '')
		{
			var img = "<img style=\"max-width:270px\" src=\""+obj.pic_url+"\">";
			$("#pic").html(img);
		}
		//标签
		$("#t_sort").html(obj.sort);
		$("#t_type").html(obj.type);
		
		//检测是否之前答过这个题目
		//要从本地存储中读取数据状态
		if(localStorage[KaoShi.type])
		{
			var ls_obj = eval("("+localStorage[KaoShi.type]+")");
			if(ls_obj && ls_obj[obj.t_id])
			{
				//开始写入状态
				var ans_before = ls_obj[obj.t_id]["ans"];
				
				if(KaoShi.type != "mn_")
				{
					if(ans_before == KaoShi.answer)
					{
						//将正确答案置为绿色
						KaoShi.confirm_success(ans_before);
					}
					else
					{
						//将答案置为红色
						KaoShi.confirm_wrong(ans_before);
						//进行提示
						KaoShi.alert_warn(1);
					}
				}
				else
				{
					//将答案置为蓝色
					KaoShi.confirm_click(ans_before);
				}
			}
		}
		
	},
	//点击答案
	confirm:function(obj)
	{
		var id = $(obj).attr("id");
		var ans = id.split("_")[1];//获取被点击的答案
		$("[id^=s_]").attr("class","list-group-item");
		if(KaoShi.type!="mn_")
		{
			if(ans == KaoShi.answer)//验证答案
			{
				KaoShi.confirm_success(ans);
			}
			else
			{
				KaoShi.confirm_wrong(ans);
			}
		}
		else
		{
			KaoShi.confirm_click(ans);
		}

		//记录答题状态
		KaoShi.createStorage(KaoShi.type, KaoShi.t_info['t_id'], ans);
	},
	confirm_success:function(ans)
	{
		//标绿正确答案
		//跳转到下一题目
		$("#s_"+ans).attr("class","list-group-item list-group-item-success");
		KaoShi.alert_warn(0);
	},
	confirm_wrong:function(ans)
	{
		KaoShi.createStorage("ct_",KaoShi.t_info.t_id,ans);//通用错题日志
		//标红错误答案
		//提示正确答案
		$("#s_"+ans).attr("class","list-group-item list-group-item-danger");
		KaoShi.alert_warn(1);
	},
	confirm_click:function(ans)
	{
		//判断结果是否错误如果错误，记录下来
		if(ans != KaoShi.answer)
		{
			KaoShi.createStorage("ct_",KaoShi.t_info.t_id,ans);//通用错题日志
			//考试错题日志
			KaoShi.createStorage("ct_now_",KaoShi.t_info.t_id,ans);
			KaoShi.num.wrong++;//考试错题数量
		}
		else
		{
			KaoShi.num.success++;
		}
		//标蓝答案
		//跳转到下一题目
		$("#s_"+ans).attr("class","list-group-item list-group-item-info");
	},
	alert_warn:function(is_show)
	{
		if(is_show)
		{
			if(KaoShi.answer=="Y")
			{
				var warn = "正确";
			}
			if(KaoShi.answer=="N")
			{
				var warn = "错误";
			}

			var html = "<div class=\"alert alert-info\" role=\"alert\"><strong>错误：</strong>答案应该为“"+KaoShi.answer+KaoShi.ans_content+"”</div>";
		}
		else
		{
			html = "";
		}

		$("#alert").html(html);

	},
	//提示某些信息(id:信息id,msg：信息内容,time：倒计时时间)
	alert_message:function(id,msg,time)
	{
		var id = "alert_message_"+id;
		var html = "<div class=\"alert alert-info\" id=\""+id+"\" role=\"alert\">"+msg+"</div>";
		$("#alert").append(html);
		if(time)//打开计时器
		{
			var i = setTimeout(function(){
				$("#"+id).remove();
			},time);
		}
	},
	//选择任意题目
	changeitem:function(id)
	{
		if(id && id>=1 && id<=898)
		{
			KaoShi.getinfo(id);
		}
		else
		{
			return;
		}
	},
	//向上翻页
	prev:function()
	{
		if(KaoShi.rand_obj)//如果是随机
		{
			if(KaoShi.randid==0)
			{
				KaoShi.alert_message("prev","已经是第一题",800);
				return;
			}
			KaoShi.randid = KaoShi.randid-1;
			KaoShi.getinfo(KaoShi.rand_obj[KaoShi.randid]);
		}
		else
		{
			if(KaoShi.tid==1)
			{
				KaoShi.alert_message("prev","已经是第一题",800);
				return;
			}
			KaoShi.tid = KaoShi.tid-1;
			KaoShi.getinfo(KaoShi.tid);
		}
		
	},
	//向下翻页
	next:function(data)
	{
		if(KaoShi.rand_obj)//如果是随机
		{
			if(KaoShi.randid==KaoShi.num.total-1)
			{
				KaoShi.alert_message("next","已经是最后一题",800);
				return;
			}
			KaoShi.randid = KaoShi.randid+1;
			KaoShi.getinfo(KaoShi.rand_obj[KaoShi.randid]);
		}
		else
		{
			if(KaoShi.tid==KaoShi.num.total)
			{
				KaoShi.alert_message("next","已经是最后一题",800);
				return;
			}
			KaoShi.tid = KaoShi.tid+1;
			KaoShi.getinfo(KaoShi.tid);
		}
	
	},
	//使用localStorage来存储答题结果（1，2，3允许通过重新出题来清空答题结果）
	//1、存储顺序答题结果，sx_  生成对象来进行存储 sx_:{1:{ans:A}}
	//2、存储随机十题结果,sj_ sj_:{122:{ans:Y}}
	//3、存储模拟考试结果,mn_  mn_:{1:{ans:Y}}
	//4、存储我的错题（不允许清空答题结果）,ct_ {1,2,3,4}
	//生成状态的方法createstorage:
	createStorage:function(prefix,id,value)
	{
		var obj = {};		
		if(localStorage[prefix] && localStorage[prefix]!='undefined')
		{
			obj = eval("("+localStorage[prefix]+")");
		}
		//更改原对象的值
		obj[id] = {"ans":value};
		//将对象字符化保存起来
		localStorage[prefix] = JSON.stringify(obj);		
	},
	//清空对象
	clearStorage:function(prefix,id)
	{
		localStorage.removeItem(prefix);
	},
	//点击模拟考试交卷
	handpaper:function()
	{
		//清空ct_kaoshi_
		localStorage["ct_kaoshi_"] = '';
		//统计错题数量，和错题列表
		if(localStorage['ct_now_'])
		{
			var obj = eval("("+localStorage["ct_now_"]+")");
			//将分数写入html
			$("#kaoshi_result_num").html(KaoShi.num.success+"分");
		}
		else
		{
			$("#kaoshi_result_num").html("0分");
		}
		if(KaoShi.num.success>=90)
		{
			$("#kaoshi_result").html("恭喜您，考试通过^^");
		}
		else
		{
			$("#kaoshi_result").html("很抱歉，不及格哦，再接再厉！");
		}
	},
	//点击查看我的本次错题
	see_ct:function()
	{
		localStorage["ct_kaoshi_"] = localStorage["ct_now_"];
		location.href=KaoShi.web_root+'app/index.php?i='+window.sysinfo.uniacid+'&c=entry&do=myct&m=jiakao_system&rand='+Math.random()
	}
}