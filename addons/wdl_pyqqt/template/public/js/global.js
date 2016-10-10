Zepto(function($){
	//fonIndex.html
	$('#imgGroup img').on('tap',function(e){
		$('.imageValue').remove();
		// Be careful to the DOM executeing order !
		$(this).after('<div class="imageValue">'+this.alt.split(',')[0]+''+this.alt.split(',')[1]+'</div>');
		$('.imageValue').css({position:'absolute',fontSize:'0.6rem'});
		var left = $(this).offset().left+$(this).offset().width/2-$('.imageValue').offset().width/2-16;
		var top = '-5';
		$('.imageValue').css({
			marginLeft:(left+'px'),
			marginTop:(top+'rem'),
		});
	});

	//fonMyself.html
	$('.rank_select span').on('tap',function(){
		$(this).siblings().removeClass('select_checked');
		$(this).attr('class','select_checked');
		if($(this).text() == "好友乞丐排行"){
			$('#rank_list').css('height',rank_list_show_height);
			$('.rank_list_show').css({
				'transform':'translateX(0px)',
				'-moz-transform':'translateX(0px)',
				'-webkit-transform':'translateX(0px)'
			});
			$('.rank_list_hide').css({
				'transform':'translateX(0px)',
				'-moz-transform':'translateX(0px)',
				'-webkit-transform':'translateX(0px)'
			});
		}else if($(this).text() == "全国乞丐排行"){
			$('#rank_list').css('height',rank_list_hide_height);
			$('.rank_list_hide').css({
				'transition':'all 0.5s linear',
				'-moz-transition':'all 0.5s linear',
				'-webkit-transition':'all 0.5s linear'
			});
			$('.rank_list_show').css({
				'transform':'translateX(-'+rank_list_left+'px)',
				'-moz-transform':'translateX(-'+rank_list_left+'px)',
				'-webkit-transform':'translateX(-'+rank_list_left+'px)'
			});
			$('.rank_list_hide').css({
				'transform':'translateX(-'+rank_list_left+'px)',
				'-moz-transform':'translateX(-'+rank_list_left+'px)',
				'-webkit-transform':'translateX(-'+rank_list_left+'px)'
			});
		}
	});
	$('.share button').on('tap',function(e){
		$('#bodyAssist').css({
			'background-color':'#000',
			'opacity':'.85',
			'visibility':'visible'
		})
		$('#arrow').css('visibility','visible');
		$('#shareWords').css('visibility','visible');
		$('#bodyAssist').on('tap',function(e){
			$(this).css('visibility','hidden');
			$('#arrow').css('visibility','hidden');
			$('#shareWords').css('visibility','hidden');
		});
	});

	//fonIndexFuzzy.html
	$('.sleep_him').on('tap',function(e){
		$('#bodyAssist').css({
			'background-color':'#000',
			'opacity':'.85',
			'visibility':'visible'
		}).on('tap',function(e){
			$(this).css('visibility','hidden');
			$('#packet').css({
				'transform':'translateY(-24.6rem)',
				'-moz-transform':'translateY(-24.6rem)',
				'-webkit-transform':'translateY(-24.6rem)',
				'opacity':'0',
				'visibility':'hidden'
			});
			$('#remind').css({
				'transform':'translateY(-25rem)',
				'-moz-transform':'translateY(-25rem)',
				'-webkit-transform':'translateY(-25rem)',
				'visibility':'hidden'		
			});
			$('#createPage').css({
				'transform':'translateY(3.55rem)',
				'-moz-transform':'translateY(3.55rem)',
				'-webkit-transform':'translateY(3.55rem)',			
				'visibility':'hidden'
			});		
		});
		$('#packet').css({
			'transform':'translateY(24.6rem)',
			'-moz-transform':'translateY(24.6rem)',
			'-webkit-transform':'translateY(24.6rem)',
			'opacity':'1',
			'visibility':'visible'
		});
		$('#remind').css({
			'transform':'translateY(25rem)',
			'-moz-transform':'translateY(25rem)',
			'-webkit-transform':'translateY(25rem)',
			'visibility':'visible'		
		});
		$('#createPage').css({
			'transform':'translateY(-3.55rem)',
			'-moz-transform':'translateY(-3.55rem)',
			'-webkit-transform':'translateY(-3.55rem)',			
			'visibility':'visible'
		});
	});
})
