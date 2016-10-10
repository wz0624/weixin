$(function(){

	App.controller('index_page', function (page) {
		/**背景音乐控制**/
		var music_bg = new Audio();
		music_bg.style.display='none';
		music_bg.preload='auto';
		music_bg.autoplay='autoplay';
		music_bg.loop='loop';
		music_bg.src = $('#music_bg').val();//'{MODULE_URL}/static/res/GandL.mp3';
		//var music_bg=$('#music_bg')[0];
		$(page).find('#music_bg_control').on('tap',function(){
			if(music_bg.paused){                 
				music_bg.play();
				$(this).addClass('on');
			}else{
				music_bg.pause();
				$(this).removeClass('on');
			}
		});

				
		// 助力获体力
		$(page).find('#gethelp_btn').on('tap',function(){
			$(page).find('#dialog_gethelp').show();
		});
		$(page).find('#dialog_gethelp_close').on('tap',function(){
			$(page).find('#dialog_gethelp').hide();
		});
		$(page).find('#dialog_gethelp_done').on('tap',function(){
			location.reload();
		});

		// 未开始
		if(_ACTIVITY_STATUS==0){
			// 活动开始倒计时
			var unstart_timer=$(page).find('#unstart_timer');
			var unstart_timer_counter = function(){
				var t = parseInt(unstart_timer.data('time'));
				if(t<=0){
					unstart_timer.html('正在加载礼物...');
					location.reload();
				}else{
					t--;
					unstart_timer.data('time',t);
					unstart_timer.html(VP_TIME_FORMAT(t)+'后开抢');
				}
			}
			setInterval(unstart_timer_counter,1000);
		}

		// 进行中
		if(_ACTIVITY_STATUS==1){
			
			if(_MINE_YAO==0){
				// 冷却倒计时
				var cold_timer=$(page).find('#cold_timer');
				var cold_timer_counter = function(){
					var t = parseFloat(cold_timer.data('time'));
					if(t<=0.5){
						revived();
					}else{
						t=t-0.1;
						cold_timer.data('time',t);
						cold_timer.find('.progress').width(200- parseFloat(200*t/parseInt(cold_timer.data('cold'))));
						cold_timer.find('.progress_txt').html('歇会儿，'+VP_TIME_FORMAT(parseInt(t))+'后再摇');
					}
				}
				var cold_Interval = setInterval(cold_timer_counter,100);

				// 复活
				function revived(){
					clearInterval(cold_Interval);
					$(page).find('#yao_wait').hide();
					$(page).find('#yao_start').show();	
				}
			}
			
			// 舞台切换
			var stage='main'; // 默认在主舞台
			$(page).find('#yao_btn').on('tap',function(){
				stage='play';
				$(page).find('#stage_main').hide();
				$(page).find('#stage_play').show();
			});
			$(page).find('#cancel_btn').on('tap',function(){
				stage='main';
				$(page).find('#stage_play').hide();
				$(page).find('#stage_main').show();
			});
			
			// 打开礼物
			var gift=null;
			$(page).find('#gift_btn').on('tap',function(){
				if(gift==null){
					alert('礼盒里空空哒，重摇吧！');
					location.reload();
					return false;
				}
				// 根据礼物类型进入领取页面
				wx.addCard({
					cardList: [{
						cardId:gift.card.cardId,
						cardExt:gift.card.cardExt 
					}], // 需要添加的卡券列表
					success: function (res) {
						gift=null;
						location.reload();
					}
				});
			});

			// 摇一摇相关开始
			var yao_shake = $(page).find('#yao_shake');
			var SHAKE_THRESHOLD = 3000;  
			var last_update = 0;  
			var x = y = z = last_x = last_y = last_z = 0;  
			function deviceMotionHandler(eventData) { 
				if(stage!='play'){
					return false;
				}
				var acceleration = eventData.accelerationIncludingGravity;  
				var curTime = new Date().getTime();  
				//yao_shake.css({'top':yao_shake.offset().top+acceleration.y});   
				if ((curTime - last_update) > 100) {  
					var diffTime = curTime - last_update;  
					last_update = curTime;  
					x = acceleration.x;  
					y = acceleration.y;  
					z = acceleration.z;  
					var speed = Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000;
					if (speed > SHAKE_THRESHOLD) { 
						if(yao_shake.data('status')!='1'){ // 摇过了
							return false;
						}
						yao_shake.data('status','0');
						$(page).find('#audio_shake_ing')[0].play(); 
						$(page).find('#loading_gift').show(); 
						get_gift();
					}  
					last_x = x;  
					last_y = y;  
					last_z = z;  
				}  
			}
			function get_gift(){
				$.post(yao_shake.data('url'),{},function(resp) {
					$(page).find('#loading_gift').hide(); 
					yao_shake.removeClass('shake_swing');
					if(resp.status!=1){
						return alert(resp.info);
					}else{
						$(page).find('#audio_shake_done')[0].play(); 
						gift=resp.data;
						$(page).find('#dialog_gift').show();
					}
				});
			}
			wx.ready(function(){
				if (window.DeviceMotionEvent) {  
					window.addEventListener('devicemotion', deviceMotionHandler, false);  
				} else {  
					alert('not support mobile event');  
				}
			});
			// 摇一摇相关结束
		}

	});


	// 规则页初始化
	App.controller('rule_page', function (page) {
		this.transition = 'scale-in';
	});
	// 奖品页初始化
	App.controller('awards_page', function (page) {
		this.transition = 'scale-in';

		// 奖品列表
		var list=$(page).find('#award_list');
		var award_list_load=$(page).find('#award_list_load');
		var award_list_tpl=baidu.template($(page).find('#award_list_tpl').html());
		var loadAwardList = function(){
			if(0==list.data('more')){
				return;
			}
			award_list_load.removeClass('more');
			award_list_load.addClass('loading');
			award_list_load.find('.text').html('正在加载...');
			award_list_load.show();
			$.get(list.data('url'),{
				start:list.data('start')
			}).done(function(resp) {
				award_list_load.hide();
				if(!resp){
					alert('加载失败，请检查网络后重试');
					return;
				}
				if(resp.status!=1){
					alert(resp.info);
					return;
				}
				var data=resp.data;
				data.index=list.data('start');
				list.data('start',data.start);
				list.data('more',data.more);
				var html=award_list_tpl(data);
				list.html(list.html()+html);
			});
		};
		$(page).find('#award_list_load').on('tap',function(){
			loadAwardList();
		});
		loadAwardList();
	});

	App.load('index_page','fade');
});