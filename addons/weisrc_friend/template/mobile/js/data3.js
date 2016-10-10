// boy1
function getData1() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "好无聊,今天晚上约吗？",
            "likes": ["林志玲", "Angelababy"],
            "pics": [],
            "commets": [{
                "nickname": "林志玲",
                "comment": "去哪里呢？"
            }, {
                "nickname": "Angelababy",
                "comment": "晓明今天在外面,我们好久不见了！"
            }, {
                "nickname": "王思聪",
                "comment": "兄弟,有什么心事吗,晚上我们一起玩吧～"
            }],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "wsc1.png",
            "nickname": "王思聪",
            "desc": "我承受着这个年纪不该有的帅气和机智",
            "pics": [
                BASE_IMG_URL + "wsc2.jpg"
            ],
            "likes": [],
            "commets": [{
                "nickname": "林志玲",
                "comment": "老公最帅!"
            }, {
                "nickname": "Angelababy",
                "comment": "你最帅啦！"
            }, {
                "nickname": user.nickname,
                "comment": "呵呵"
            }, {
                "nickname": "王思聪<span>回复</span>"+user.nickname,
                "comment": "今晚见！"
            }],
            "postTime": '2分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "xdd.png",
            "nickname": "习近平",
            "desc": "感觉自己萌萌的！ ",
            "pics": [
                BASE_IMG_URL + "xdd2.png"
            ],
            "likes": [],
            "commets": [],
            "postTime": '3分钟前'
        },
		{
            "id": 3,
            "headimgurl": BASE_IMG_URL + "my.png",
            "nickname": "马云",
            "desc": "梦想还是要有的,万一实现了呢？ ",
            "pics": [
                BASE_IMG_URL + "my.png"
            ],
            "likes": [],
            "commets": [{
                "nickname": user.nickname,
                "comment": "梦想要靠行动实现！"
            }, {
                "nickname": "马云<span>回复</span>"+user.nickname,
                "comment": "年轻人有前途！赞！"
            }],
            "postTime": '6分钟前'
        }, 
        {
            "id": 4,
            "headimgurl": BASE_IMG_URL + "zzj1.jpg",
            "nickname": "周杰伦",
            "desc": "我告诉你哦,只有中文歌才是最屌的！",
            "pics": [
                BASE_IMG_URL + "zjl2.jpg"
            ],
            "likes": [],
            "commets": [],
            "postTime": '1小时前'
        }, 
        {
            "id": 5,
            "headimgurl": BASE_IMG_URL + "hh.png",
            "nickname": "韩寒",
            "desc": user.nickname + "许久不见,甚是挂念！",
            "pics": [
                BASE_IMG_URL + "hh2.png"
            ],
            "likes": [],
            "commets": [{
                "nickname": user.nickname,
                "comment": "岳父想我啦？"
            }, {
                "nickname": "韩寒<span>回复</span>"+user.nickname,
                "comment": "是呢～"
            }],
            "postTime": '2小时'
        }, 
        {
            "id": 6,
            "headimgurl": BASE_IMG_URL + "zqd.png",
            "nickname": "张全蛋",
            "desc": "666666,Iphone6S(屎)",
            "pics": [
                BASE_IMG_URL + "zqd2.jpg"
            ],
            "likes": [],
            "commets": [],
            "postTime": '3小时'
        }, 
        {
            "id": 7,
            "headimgurl": BASE_IMG_URL + "cwt1.jpg",
            "nickname": "陈伟霆",
            "desc": "来吧,不要害羞。 ",
            "pics": [
                BASE_IMG_URL + "cwt2.jpg"
            ],
            "likes": [],
            "commets": [],
            "postTime": '3小时前'
        }],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}

// boy2
function getData2() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "今天又跑了10公里",
            "likes": ["林志玲", "Angelababy"],
            "pics": [],
            "commets": [{
                "nickname": "苍井空",
                "comment": "今天晚上我好想运动啊"
            }, {
                "nickname": user.nickname+"<span>回复</span>苍老师",
                "comment": "那我们今晚一起运动把"
            }, {
                "nickname": "Angelababy",
                "comment": "呵呵"
            }, {
                "nickname": "范冰冰",
                "comment": "呵呵"
            }],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "wsc1.png",
            "nickname": "王思聪",
            "desc": "我承受着这个年纪不该有的帅气和机智",
            "pics": [
                BASE_IMG_URL + "wsc2.jpg"
            ],
            "likes": [],
            "commets": [{
                "nickname": "林志玲",
                "comment": "老公最帅!"
            }, {
                "nickname": "Angelababy",
                "comment": "你最帅啦！"
            }, {
                "nickname": user.nickname,
                "comment": "呵呵"
            }, {
                "nickname": "王思聪<span>回复</span>"+user.nickname,
                "comment": "今晚见！"
            }],
            "postTime": '2分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "xdd.png",
            "nickname": "习近平",
            "desc": "感觉自己萌萌的！ ",
            "pics": [
                BASE_IMG_URL + "xdd2.png"
            ],
            "likes": [],
            "commets": [],
            "postTime": '3分钟前'
        },
		{
            "id": 3,
            "headimgurl": BASE_IMG_URL + "my.png",
            "nickname": "马云",
            "desc": "梦想还是要有的,万一实现了呢？ ",
            "pics": [
                BASE_IMG_URL + "my.png"
            ],
            "likes": [],
            "commets": [{
                "nickname": user.nickname,
                "comment": "梦想要靠行动实现！"
            }, {
                "nickname": "马云<span>回复</span>"+user.nickname,
                "comment": "年轻人有前途！赞！"
            }],
            "postTime": '6分钟前'
        }, 
        {
            "id": 4,
            "headimgurl": BASE_IMG_URL + "zzj1.jpg",
            "nickname": "周杰伦",
            "desc": "我告诉你哦,只有中文歌才是最屌的！",
            "pics": [
                BASE_IMG_URL + "zjl2.jpg"
            ],
            "likes": [],
            "commets": [],
            "postTime": '1小时前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "cangjk1.jpg",
            "nickname": "苍井空",
            "desc": "睡不着，有人出来聊一下天吗",
            "pics": [ BASE_IMG_URL + "cangjk21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "老师我来了"}
			],
            "postTime": '30分钟前'
        }, {
            "id": 5,
            "headimgurl": BASE_IMG_URL + "hh.png",
            "nickname": "韩寒",
            "desc": user.nickname + "许久不见,甚是挂念！",
            "pics": [
                BASE_IMG_URL + "hh2.png"
            ],
            "likes": [],
            "commets": [{
                "nickname": user.nickname,
                "comment": "岳父想我啦？"
            }, {
                "nickname": "韩寒"+user.nickname,
                "comment": "是呢～"
            }],
            "postTime": '2小时'
        },
        {
            "id": 6,
            "headimgurl": BASE_IMG_URL + "js2.jpg",
            "nickname": "叫兽易小星",
            "desc": "彦祖果然没有我帅!!! ",
            "pics": [
                BASE_IMG_URL + "js1.jpg"
            ],
            "likes": [],
            "commets": [],
            "postTime": '4小时'
        }],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}

// boy3
function getData3() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "每天都被自己帅醒了！",
            "likes": [ ],
            "pics": [ ],
            "commets": [
            	{"nickname": "古天乐","comment": "今天你照镜子了吗？"},
				{"nickname": "吴彦祖","comment": "今天你照镜子了吗？"},
				{"nickname": "张智霖","comment": "今天你照镜子了吗？"}
			],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "liuyf1.jpg",
            "nickname": "刘亦菲",
            "desc": user.nickname+",今晚约吗？",
            "pics": [],
            "likes": ["宋承宪", "林志玲"],
            "commets": [{
                "nickname": "林志玲",
                "comment": "宋承宪好帅哦~"
            }, {
                "nickname": "黄晓明",
                "comment": "baby外拍去了,有空多聚聚！"
            }],
            "postTime": '2分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "yiyqx1.jpg",
            "nickname": "易烊千玺",
            "desc": "九百万粉丝福利来咯～这次不像800w那么傻了,分享一组两周年前夜兴奋的睡不着的我谢谢大家的关注",
            "pics": [
                BASE_IMG_URL + "yiyqx2.jpg"
            ],
            "likes": [],
            "commets": [{
                "nickname": "EXO",
                "comment": "明明已拉黑~"
            }, {
                "nickname": "Angelababy",
                "comment": "千玺弟弟,加油哦！"
            }, {
                "nickname": "王思聪",
                "comment": "兄弟,你比我还要屌~"
            }],
            "postTime": '4分钟前'
        }, {
            "id": 3,
            "headimgurl": BASE_IMG_URL + "fangbb1.jpg",
            "nickname": "范冰冰",
            "desc": "我不管,宝贝最帅！",
            "pics": [
                BASE_IMG_URL + "fangbb2.jpg"
            ],
            "likes": [],
            "commets": [
            	{ "nickname": "高圆圆", "comment": "哈哈,大黑牛~" },
            	{ "nickname": "王祖蓝", "comment": "问问他下一季跑男还来不来~" },
            	{ "nickname": "范冰冰<span>回复</span>王祖蓝", "comment": "累了,他在睡觉,明天回你~" }
            ],
            "postTime": '40分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "gaoyy1.jpg",
            "nickname": "高圆圆",
            "desc": "工作再忙,也要休息~",
            "pics": [
                BASE_IMG_URL + "gaoyy2.jpg"
            ],
            "likes": ['彭于晏','何炅','汪涵'],
            "commets": [
            	{ "nickname": "彭于晏", "comment": "今晚见！" },
            	{ "nickname": "钟汉良<span>回复</span>彭于晏", "comment": "快来围观~" },
            	{ "nickname": "周杰伦<span>回复</span>钟汉良", "comment": "哎哟,不错哦！" },
            ],
            "postTime": '45分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "gaoxs1.jpg",
            "nickname": "高晓松",
            "desc": "曾经,帅过~",
            "pics": [
                BASE_IMG_URL + "gaoxs2.jpg"
            ],
            "likes": ['高圆圆','王俊凯'],
            "commets": [
            	{ "nickname": "罗永浩", "comment": "网友老说我跟你像~" },
            	{ "nickname": "高晓松<span>回复</span>罗永浩", "comment": "自从用了坚果,扔了苹果,呵呵~" }
            ],
            "postTime": '1小时前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "yangm1.jpg",
            "nickname": "杨幂",
            "desc": user.nickname + ",说好的电影,星爸爸都喝了2杯怎么还没到？！",
            "pics": [
                BASE_IMG_URL + "yangm21.jpg"
            ],
            "likes": ['马云','赵薇','郭敬明'],
            "commets": [
            	{ "nickname": "汪峰", "comment": "上不了头条,没心情看电影~" },
            	{ "nickname": "郭敬明", "comment": "恺威造吗？" },
            	{ "nickname": "杨幂<span>回复</span>郭敬明", "comment": "让他在家带孩子~" }
            ],
            "postTime": '1小时前'
        }],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}

// boy4
function getData4() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "每天都被自己帅醒了！",
            "likes": [ ],
            "pics": [ ],
            "commets": [
            	{"nickname": "古天乐","comment": "今天你照镜子了吗？"},
				{"nickname": "吴彦祖","comment": "今天你照镜子了吗？"},
				{"nickname": "张智霖","comment": "今天你照镜子了吗？"}
			],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "liuyf1.jpg",
            "nickname": "刘亦菲",
            "desc": user.nickname+",今晚约吗？",
            "pics": [],
            "likes": ["宋承宪", "林志玲"],
            "commets": [{
                "nickname": "林志玲",
                "comment": "宋承宪好帅哦~"
            }, {
                "nickname": "黄晓明",
                "comment": "baby外拍去了,有空多聚聚！"
            }],
            "postTime": '2分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "yiyqx1.jpg",
            "nickname": "易烊千玺",
            "desc": "九百万粉丝福利来咯～这次不像800w那么傻了,分享一组两周年前夜兴奋的睡不着的我谢谢大家的关注",
            "pics": [
                BASE_IMG_URL + "yiyqx2.jpg"
            ],
            "likes": [],
            "commets": [{
                "nickname": "EXO",
                "comment": "明明已拉黑~"
            }, {
                "nickname": "Angelababy",
                "comment": "千玺弟弟,加油哦！"
            }, {
                "nickname": "王思聪",
                "comment": "兄弟,你比我还要屌~"
            }],
            "postTime": '4分钟前'
        }, {
            "id": 3,
            "headimgurl": BASE_IMG_URL + "fangbb1.jpg",
            "nickname": "范冰冰",
            "desc": "我不管,宝贝最帅！",
            "pics": [
                BASE_IMG_URL + "fangbb2.jpg"
            ],
            "likes": [],
            "commets": [
            	{ "nickname": "高圆圆", "comment": "哈哈,大黑牛~" },
            	{ "nickname": "王祖蓝", "comment": "问问他下一季跑男还来不来~" },
            	{ "nickname": "范冰冰<span>回复</span>王祖蓝", "comment": "累了,他在睡觉,明天回你~" }
            ],
            "postTime": '40分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "gaoyy1.jpg",
            "nickname": "高圆圆",
            "desc": "工作再忙,也要休息~",
            "pics": [
                BASE_IMG_URL + "gaoyy2.jpg"
            ],
            "likes": ['彭于晏','何炅','汪涵'],
            "commets": [
            	{ "nickname": "彭于晏", "comment": "今晚见！" },
            	{ "nickname": "钟汉良<span>回复</span>彭于晏", "comment": "快来围观~" },
            	{ "nickname": "周杰伦<span>回复</span>钟汉良", "comment": "哎哟,不错哦！" },
            ],
            "postTime": '45分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "gaoxs1.jpg",
            "nickname": "高晓松",
            "desc": "曾经,帅过~",
            "pics": [
                BASE_IMG_URL + "gaoxs2.jpg"
            ],
            "likes": ['高圆圆','王俊凯'],
            "commets": [
            	{ "nickname": "罗永浩", "comment": "网友老说我跟你像~" },
            	{ "nickname": "高晓松<span>回复</span>罗永浩", "comment": "自从用了坚果,扔了苹果,呵呵~" }
            ],
            "postTime": '1小时前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "yangm1.jpg",
            "nickname": "杨幂",
            "desc": user.nickname + ",说好的电影,星爸爸都喝了2杯怎么还没到？！",
            "pics": [
                BASE_IMG_URL + "yangm21.jpg"
            ],
            "likes": ['马云','赵薇','郭敬明'],
            "commets": [
            	{ "nickname": "汪峰", "comment": "上不了头条,没心情看电影~" },
            	{ "nickname": "郭敬明", "comment": "恺威造吗？" },
            	{ "nickname": "杨幂<span>回复</span>郭敬明", "comment": "让他在家带孩子~" }
            ],
            "postTime": '1小时前'
        }],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}

// girl 1
function getData5() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "没睡的来点个赞",
            "likes": ["鹿晗", "吴亦凡", "杨洋", "EXO","邓超","王大鹏"],
            "pics": [],
            "commets": [
            	{"nickname": "张全蛋","comment": "怎么还没睡呀"},
				{"nickname": "刘马松","comment": "宝贝怎么了？"}
			],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "wuyf1.jpg",
            "nickname": "吴亦凡",
            "desc": "今天下午和宝贝去逛街啦！晚上还一起看了易烊千玺的排练,哈哈哈！开心！",
            "pics": [ BASE_IMG_URL + "wuyf2.jpg" ],
            "likes": ['罗玉凤','芙蓉姐姐','汪峰','王宝强'],
            "commets": [
            	{"nickname": "罗玉凤","comment": "秒赞！"},
            	{"nickname": "罗玉凤","comment": "哎呀我是罗玉凤啦,上次在你摄影棚给你买水的那个啦！"},
            	{"nickname": "吴亦凡<span>回复</span>罗玉凤","comment": "哦"},
            	{"nickname": "罗玉凤<span>回复</span>吴亦凡","comment": "我要给你生个猴子 !"}
			],
            "postTime": '2分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "liyf1.jpg",
            "nickname": "李易峰",
            "desc": "刚刚和美女聊了好久,好激动睡不着啦！我喜欢她！",
            "pics": [ BASE_IMG_URL + "liyf2.jpg" ],
            "likes": ['易烊千玺','王俊凯','王源',user.nickname],
            "commets": [
            	{"nickname": user.nickname,"comment": "害羞"},
            	{"nickname": "易烊千玺","comment": "祝哥哥早日成功哈！"},
            	{"nickname": "李易峰<span>回复</span>"+user.nickname,"comment": "嘿嘿嘿,什么时候能约你吃饭呀！！"},
			],
            "postTime": '2分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "dengc1.jpg",
            "nickname": "邓超",
            "desc": "明天想带孙俪和孩子去三亚玩几天,想叫"+user.nickname+",居然不给面子！哼！上次说好的跟我们一起去旅行的呢！",
            "pics": [ BASE_IMG_URL + "dengc2.jpg" ],
            "likes": ['王宝强','徐峥','周星驰','李晨'],
            "commets": [
            	{"nickname": "徐峥","comment": "你得了吧！上次"+user.nickname+"还陪你们一家去玩呢，我都叫她多少次了都没来！！！！"},
            	{"nickname": "王宝强","comment": "我都不敢约。。。"}
			],
            "postTime": '2分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "yangy1.jpg",
            "nickname": "杨洋",
            "desc": "。",
            "pics": [ BASE_IMG_URL + "yangy2.jpg" ],
            "likes": ['罗玉凤','易烊千玺',user.nickname,'邓超'],
            "commets": [
            	{"nickname": "罗玉凤","comment": "这是在哪拍的呀！好帅好帅！"},
            	{"nickname": "杨洋<span>回复</span>罗玉凤","comment": "教室。"},
            	{"nickname": "罗玉凤<span>回复</span>杨洋","comment": "什么时候拍的呀！下次还拍吗，我去看！我想看！！"},
            	{"nickname": "杨洋<span>回复</span>罗玉凤","comment": "不。"},
            	{"nickname": "罗玉凤<span>回复</span>杨洋","comment": "。。。别这样嘛小哥！"},
            	{"nickname": user.nickname,"comment": "小哥好帅哦。"},
            	{"nickname": "杨洋<span>回复</span>"+user.nickname,"comment": "谢谢美女啦！！！嘿嘿好开心哦！谢谢夸我！"},
            	{"nickname": "罗玉凤<span>回复</span>杨洋","comment": "。。。。。。"}
			],
            "postTime": '2分钟前'
        }, {
            "id": 2,
            "headimgurl": BASE_IMG_URL + "yiyqx1.jpg",
            "nickname": "易烊千玺",
            "desc": "排舞的时候看到吴亦凡和姐姐来看我们啦！嘿嘿~~~谢谢姐姐和吴亦凡哥哥送的礼物！",
            "pics": [
                BASE_IMG_URL + "yiyqx2.jpg"
            ],
            "likes": ['王俊凯','王源',user.nickname,'吴亦凡'],
            "commets": [
            	{"nickname": "王俊凯","comment": "姐姐好漂亮！"},
            	{"nickname": "王源","comment": "姐姐好漂亮！！！"},
            	{"nickname": "吴亦凡","comment": "嘿嘿你们喜欢就好！"},
            	{"nickname": user.nickname,"comment": "加油哦！"},
            	{"nickname": "王俊凯<span>回复</span>"+user.nickname,"comment": "谢谢姐姐！"},
            	{"nickname": "王源<span>回复</span>"+user.nickname,"comment": "谢谢！嘿嘿~~"}
			],
            "postTime": '4分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "wangzt1.jpg",
            "nickname": "黄子韬",
            "desc": "明天要公开一个大事件！大家都注意了！先放张图！",
            "pics": [ BASE_IMG_URL + "wangzt2.jpg" ],
            "likes": ['吴亦凡',user.nickname,'杨洋','邓超'],
            "commets": [
            	{"nickname": "汪峰","comment": "我擦！！"}
			],
            "postTime": '2分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "wangf1.jpg",
            "nickname": "汪峰",
            "desc": "明天发新专辑！阅兵也过了，节日也过了，明天一定能上头条！送一张专辑封面！",
            "pics": [ BASE_IMG_URL + "wangf2.jpg" ],
            "likes": ['章子怡','那英','周杰伦'],
            "commets": [],
            "postTime": '1小时前'
        }
        ],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}
// girl 2
function getData6() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "心情不好求安慰",
            "likes": [],
            "pics": [],
            "commets": [
            	{"nickname": "刘马松","comment": "宝贝怎么了？"},
				{"nickname": "王力宏","comment": "抱抱"},
				{"nickname": "吴彦祖","comment": "么么哒"},
				{"nickname": "彭丽媛","comment": "阅兵你怎么没来？"}
			],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "dengc1.jpg",
            "nickname": "邓超",
            "desc": "一家四口，邓超最丑。是世界上最大的笑话",
            "pics": [ BASE_IMG_URL + "dengc3.jpg" ],
            "likes": ['嬛嬛','王祖蓝','鹿晗',user.nickname],
            "commets": [
            	{"nickname": user.nickname,"comment": "呵呵"},
            	{"nickname": "小花妹妹","comment": "一家四口，邓超最丑"},
            	{"nickname": "王祖蓝","comment": "一家四口，邓超最丑"},
            	{"nickname": "邓超","comment": "..."}
			],
            "postTime": '40分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "huanhuan1.jpg",
            "nickname": "嬛嬛",
            "desc": "贱人就是矫情！",
            "pics": [ BASE_IMG_URL + "huanhuan2.jpg" ],
            "likes": ['邓超','杨幂','anglebaby'],
            "commets": [
            	{"nickname": "Anglebaby","comment": "娘娘，赐超哥一丈红..."},
            	{"nickname": "邓超<span>回复</span>嬛嬛","comment": "孙俪，we are伐木累。"}
			],
            "postTime": '50分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "xiaohr1.jpg",
            "nickname": "小黄人Bob",
            "desc": "！@#￥*&￥……%*&）（&……￥%￥@* banana banana！",
            "pics": [ ],
            "likes": ['Kevin','Bob'],
            "commets": [
            	{"nickname": "Bob","comment": "banana，banana！！！"}
			],
            "postTime": '7分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "tfboys1.jpg",
            "nickname": "TFboys",
            "desc": user.nickname+"，来，跟着我左手右手一个慢动作，左手右手慢动作重播！",
            "pics": [ BASE_IMG_URL + "tfboys2.jpg" ],
            "likes": [user.nickname,'王凯俊','千玺'],
            "commets": [
            	{"nickname": user.nickname,"comment": "阿姨好喜欢你们哦 口水ing..."},
            	{"nickname": "王俊凯<span>回复</span>"+user.nickname,"comment": "爱你。"}
			],
            "postTime": '55分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "tiancxxm1.jpg",
            "nickname": "天才小熊猫",
            "desc": "敢爆出来吗  你们谁买了脑残粉~~~",
            "pics": [ BASE_IMG_URL + "tiancxxm2.jpg" ],
            "likes": ['杨幂'],
            "commets": [
            	{"nickname": "杨幂","comment": "最喜欢粉色了，么么哒(づ￣ 3￣)づ"},
            	{"nickname": user.nickname+"<span>回复</span>杨幂","comment": "我也喜欢，啊哦~"}
			],
            "postTime": '1小时前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "sunyz1.jpg",
            "nickname": "孙燕姿",
            "desc": "今晚演唱会，要来哦，VVIP座位！！！"+user.nickname,
            "pics": [ BASE_IMG_URL + "sunyz2.jpg" ],
            "likes": [user.nickname,'周杰伦','王力宏'],
            "commets": [
            	{"nickname": user.nickname,"comment": "啊啊啊啊啊啊啊... So cool ！"},
            	{"nickname": "小松松","comment": "女神，女神"},
            	{"nickname": "周杰伦","comment": "哎哟  不错哦！"},
            	{"nickname": "孙燕姿<span>回复</span>周杰伦","comment": "带上小周周一起来..."}
			],
            "postTime": '2小时前'
        }
        ],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}
// girl 3
function getData7() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "想去旅游，有谁一起的吗？",
            "likes": [],
            "pics": [],
            "commets": [
            	{"nickname": "李晨","comment": "约"},
            	{"nickname": "邓超","comment": "约"},
            	{"nickname": "张翰","comment": "约"},
				{"nickname": "范冰冰<span>回复</span>李晨","comment": "怒！"}
			],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "puj1.jpg",
            "nickname": "普京",
            "desc": "其实我也有温柔的一面",
            "pics": [ BASE_IMG_URL + "puj21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": "奥巴马","comment": "欢迎来白宫和我的萨摩玩"},
            	{"nickname": "习近平","comment": "好基友一辈子"}
			],
            "postTime": '2分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "liujs1.jpg",
            "nickname": "留几手",
            "desc": "负分滚粗",
            "pics": [ user.avatar ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "还能好好玩耍吗"}
			],
            "postTime": '2分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "kunling1.jpg",
            "nickname": "昆凌",
            "desc": "我的小公举你要健康成长哦",
            "pics": [ BASE_IMG_URL + "kunling21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": "周杰伦","comment": "听妈妈的话"}
			],
            "postTime": '4分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "angelababy1.jpg",
            "nickname": "Angelababy",
            "desc": user.nickname+"，我想让你当我的伴娘",
            "pics": [ BASE_IMG_URL + "angelababy21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "你不怕我抢你风头吗"}
			],
            "postTime": '5分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "tayor1.jpg",
            "nickname": "TaylorSwift",
            "desc": "New Album is  coming",
            "pics": [ BASE_IMG_URL + "tayor2.jpg" ],
            "likes": ['Adele'],
            "commets": [
            	{"nickname": "Beyonce","comment": "U break up with ur boybriend？"}
			],
            "postTime": '7分钟前'
        }, {
            "id": 8,
            "headimgurl": BASE_IMG_URL + "dongmz1.jpg",
            "nickname": "董明珠",
            "desc": "开机画面必须是我或者你",
            "pics": [],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "....."}
			],
            "postTime": '12分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "wuzy1.jpg",
            "nickname": "吴镇宇",
            "desc": "今日又再冲上云霄",
            "pics": [ BASE_IMG_URL + "wuzy2.jpg" ],
            "likes": [user.nickname],
            "commets": [
            	{"nickname": "张智霖","comment": "带埋我"},
            	{"nickname": "古天乐","comment": "我都去"}
			],
            "postTime": '20分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "tangyan1.jpg",
            "nickname": "唐嫣",
            "desc": user.nickname+"，周末闺蜜游吧",
            "pics": [  ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "要叫上大幂幂吗"},
            	{"nickname": "杨幂","comment": "周末我跟恺威带小糯米去玩，你们去吧"}
			],
            "postTime": '35分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "sijiali1.jpg",
            "nickname": "斯嘉丽",
            "desc": "最近拍戏好累啊",
            "pics": [ BASE_IMG_URL + "sijiali21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": "美国队长","comment": "和你一起拍戏是我的荣幸"}
			],
            "postTime": '40分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "guomm1.jpg",
            "nickname": "郭美美",
            "desc": "干爹又送了个包给我",
            "pics": [ BASE_IMG_URL + "guomm21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "你怎么出来了"},
            	{"nickname": "郭美美","comment": "感谢新干爹"},
			],
            "postTime": '45分钟前'
        }
        ],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}
// girl 4
function getData8() {
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "有人想去看电影吗？",
            "likes": [],
            "pics": [],
            "commets": [
            	{"nickname": "张全蛋","comment": "约"},
            	{"nickname": "王思聪","comment": "约"},
            	{"nickname": "王尼玛","comment": "约"},
            	{"nickname": "大鹏","comment": "约"},
            	{"nickname": user.nickname+"<span>回复</span>王思聪","comment": "么么哒"},
            	{"nickname": "刘马松","comment": "约"}
			],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "lmh1.jpg",
            "nickname": "李敏镐",
            "desc": "Hello,"+user.nickname,
            "pics": [ BASE_IMG_URL + "lmh2.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "欧巴"},
            	{"nickname": "李敏镐<span>回复</span>"+user.nickname,"comment": ":)"}
			],
            "postTime": '2分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "pengly1.jpg",
            "nickname": "彭丽媛",
            "desc": "我负责貌美如花 你负责君临天下！ ",
            "pics": [ BASE_IMG_URL + "pengly2.jpg" ],
            "likes": [],
            "commets": [],
            "postTime": '10分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "yangm1.jpg",
            "nickname": "杨幂",
            "desc": "好久不见闺蜜"+user.nickname+"很想你！",
            "pics": [ BASE_IMG_URL + "yangm21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "干嘛不放合照？"},
            	{"nickname": "杨幂<span>回复</span>"+user.nickname,"comment": "因为你太美了~"},
            	{"nickname": user.nickname+"<span>回复</span>杨幂","comment": "心机婊～～～"}
			],
            "postTime": '4分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "angelababy1.jpg",
            "nickname": "钟汉良",
            "desc": "朕今天好累。",
            "pics": [ BASE_IMG_URL + "angelababy21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "你不怕我抢你风头吗"}
			],
            "postTime": '5分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "tayor1.jpg",
            "nickname": "TaylorSwift",
            "desc": "New Album is  coming",
            "pics": [ BASE_IMG_URL + "tayor2.jpg" ],
            "likes": ['Adele'],
            "commets": [
            	{"nickname": "Beyonce","comment": "U break up with ur boybriend？"}
			],
            "postTime": '7分钟前'
        }, {
            "id": 8,
            "headimgurl": BASE_IMG_URL + "dongmz1.jpg",
            "nickname": "董明珠",
            "desc": "开机画面必须是我或者你",
            "pics": [],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "....."}
			],
            "postTime": '12分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "wuzy1.jpg",
            "nickname": "吴镇宇",
            "desc": "今日又再冲上云霄",
            "pics": [ BASE_IMG_URL + "wuzy2.jpg" ],
            "likes": [user.nickname],
            "commets": [
            	{"nickname": "张智霖","comment": "带埋我"},
            	{"nickname": "古天乐","comment": "我都去"}
			],
            "postTime": '20分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "tangyan1.jpg",
            "nickname": "唐嫣",
            "desc": user.nickname+"，周末闺蜜游吧",
            "pics": [  ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "要叫上大幂幂吗"},
            	{"nickname": "杨幂","comment": "周末我跟恺威带小糯米去玩，你们去吧"}
			],
            "postTime": '35分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "sijiali1.jpg",
            "nickname": "斯嘉丽",
            "desc": "最近拍戏好累啊",
            "pics": [ BASE_IMG_URL + "sijiali21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": "美国队长","comment": "和你一起拍戏是我的荣幸"}
			],
            "postTime": '40分钟前'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "guomm1.jpg",
            "nickname": "郭美美",
            "desc": "干爹又送了个包给我",
            "pics": [ BASE_IMG_URL + "guomm21.jpg" ],
            "likes": [],
            "commets": [
            	{"nickname": user.nickname,"comment": "你怎么出来了"},
            	{"nickname": "郭美美<span>回复</span>"+user.nickname,"comment": "感谢新干爹"}
			],
            "postTime": '4小时前'
        }
        ],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }],
            "postTime": '45分钟前'
        }
    }
}


function getLi(user) {
	var link = user.link;
	var nickname = user.nickname;

	var str = 
		'<li>' 
			+ '<table width="100%" border="0px" cellpadding="0" cellspacing="0" bordercolor="#000"'
			+ 'style="border-collapse:collapse">'
			+	'<tbody>'
			+		'<tr>'
			+			'<td class="CommentUserImgWidth" valign="top" align="left">'
			+				'<div class="CommentUserImg">'
			+					'<img src="'+user.headimgurl+'">'
			+				'</div>'
			+			'</td>'
			+			'<td valign="top" align="left">';
			
						if(link){
							str += '<div class="Tuiguang"></div><div style="font-size:1.2rem;float:right;color:#aaa;margin-right:2.0rem;">推广</div>';
						}
			
						str = str + '<div class="CommentUserName">' + nickname + '<span></span></div>'
							+ '<div class="CommentTxt">' + user.desc + '</div>';
							
						if(link){
							str += '<a href="' + link +'"><table><tbody><tr><td style="vertical-align:middle"><div class="CommentXiangqing">查看详情</div></td><td><div id="CommentXiangqingId"><img src="'+BASE_IMG_URL+'xq.png" width="30" height="30"></div></td></tr></tbody></table></a>';
						}
						
						if( user.pics.length ){
							str += '<div class="CommentImgArea2">'+'<img src="'+user.pics[0]+'">'+'</div>';
						}
							
						str += 	
							'<div class="CommentTime">'+user.postTime+'</div>'
							+ '<div class="CommentTopAndComBtn"></div>';
						
						if( user.likes.length || user.commets.length ){
							
							str += '<div class="CommentTopAndComArea" style="display:block;">'
								+	'<div class="CommentTriangleUp"></div>';
								
							if( user.likes.length ){
								str +='<div class="CommentTopArea"><table><tbody><tr><td><div class="CommentTopBtn"></div></td><td><div class="CommentTopUserName">';
								for ( key in user.likes ){
									str += user.likes[key] + ", "
								}
								str += '</div>'
									+'</td></tr></tbody></table></div>';
							}
										
							if( user.commets.length ) {
								
								str += '<div class="CommentComArea">';
									for( key in user.commets ){
										str += '<li>' + user.commets[key].nickname + '<span>：' + user.commets[key].comment + '</span></li>';
									}
									
								str += '</div>';
							}
							
							str += '</div>';
						}
			str += '</td></tr></tbody></table></li>';
	return str;
}	


function getDatatest(){
    return {
        "timeline": [{
            "id": 0,
            "headimgurl": user.avatar,
            "nickname": user.nickname,
            "desc": "羡慕妞妞和端午，吃饱了睡，睡饱了吃... 余生皆假期！",
            "likes": ["欢喜", "嬛嬛"],
            "pics": [],
            "commets": [
            	{"nickname": "范冰冰","comment": "妞妞端午好可爱（桃花状）"},
				{"nickname": "天才小熊猫","comment": "哈哈哈哈哈哈 我也是啊！"}
			],
            "postTime": '刚刚'
        }, {
            "id": 1,
            "headimgurl": BASE_IMG_URL + "xxxxxxxxx",
            "nickname": "xxxxxxxx",
            "desc": "xxxxxxxxxxxx",
            "pics": [ BASE_IMG_URL + "xxxxxxxxx" ],
            "likes": [],
            "commets": [
            	{"nickname": "xxxxx","comment": "xxxxxxxxx"},
            	{"nickname": "xxxxx","comment": "xxxxxxxxx"},
            	{"nickname": "xxxxx","comment": "xxxxxxxxx"},
            	{"nickname": "xxxxx","comment": "xxxxxxxxx"},
            	{"nickname": "xxxxx","comment": "xxxxxxxxx"},
            	{"nickname": "xxxxx","comment": "xxxxxxxxx"}
			],
            "postTime": '2分钟前'
        }
        ],
        
        "ad": {
            "id": 0,
            "headimgurl": LOGO_IMG_URL,
            "nickname": AD_NICKNAME,
            "desc": AD_DESC,
            "likes": ["叫兽易小星", "范冰冰", "郭德纲", "李晨", "霍建华"],
            "link": AD_URL,
            "pics": [QRCODE_IMG_URL],
            "commets": [{
                "nickname": "刘烨",
                "comment": "赶来围观~"
            }, {
                "nickname": "汪峰",
                "comment": "头条让给你！"
            }]
        }
    }
}


function getList(data){
	var htmlstr = "";
	for(key in data){
		console.log(data[key]);
		htmlstr += getLi(data[key]);
	}
	return htmlstr;
}
