function Game(row,col){
   this.con=document.getElementById('container');
   this.item=[];
   this.conwidth=300;
   this.conheight=300;
   this.row=row||3;
   this.col=col||3;
   this.minwidth=this.conwidth/this.col;
   this.minheight=this.conheight/this.row;
   this.num=this.row*this.col;
   this.arr=[];//初始化数组;
   this.newarr=[];//随机图片数组;
   this.pos=[];//存放位置的
   this.init();
	   this.len=this.arr.length;
   this.minIndex=10; 	
};
Game.prototype.init=function(){
	  for(var i=1;i<=this.num;i++){
		this.arr.push(i);
	  };
	  this.newarr=this.arr.slice(0);
	  var oFrag=document.createDocumentFragment();
	  for(var i=0;i<this.num;i++){
		 var div=document.createElement('div');  
		 div.style.cssText='cursor:move;background:url('+bgimg+') no-repeat -'+(i%this.col)*this.minwidth+'px -'+Math.floor((i)/this.col)*this.minheight+'px;float:left;height:'+this.minheight+'px;width:'+this.minwidth+'px;';
		 this.item.push(div);
		 oFrag.appendChild(div);
	  };
	  this.con.appendChild(oFrag);
};
Game.prototype.isSuccess=function(){
   for(var i=0;i<this.len-1;i++){
	  if(this.newarr[i]!=this.arr[i])
	  { 
		 return false;
	  }
   };
   return true;
};
Game.prototype.startGame=function(){
   this.newarr.sort(function(a,b){
	  return Math.random() > 0.5 ? 1 :-1;
   });
   for(var i=0;i<this.len;i++){

	   this.pos[i]=[this.item[i].offsetLeft,this.item[i].offsetTop];
   };
   for(var i=0;i<this.len;i++){
	  var n=this.newarr[i]-1;
	  this.item[i].style.left=this.pos[i][0]+'px';
	  this.item[i].style.top=this.pos[i][1]+'px';
	  this.item[i].style.backgroundPosition='-'+(n % this.col)*this.minwidth+'px -'+Math.floor((n)/this.col)*this.minheight+'px';
	  this.item[i].style.position='absolute';
	  this.item[i].index=i;
	  this.drag(this.item[i]);
   }
}
Game.prototype.drag=function(o){
  var self=this,near=null;
  o.ontouchstart=function(e){
  	window.event.preventDefault();
	  var ev=e.touches[0],
	    disX=ev.clientX-o.offsetLeft,
		  disY=ev.clientY-o.offsetTop;
		  o.style.zIndex=self.minIndex++;
		  
		  document.ontouchmove=function(e){
			  var ev=e.touches[0],
			  l=ev.clientX-disX,
			  t=ev.clientY-disY;
			  
			  near=self.findNear(o);
			  if(near){
				  near.className='active';
			  }
			  o.style.left=l+'px';
			  o.style.top=t+'px';
		  };
		  document.ontouchend=function(){
			  if(near){
				 near.className='';
				 self.move(o,{left:self.pos[near.index][0],top:self.pos[near.index][1]});
				 self.move(near,{left:self.pos[o.index][0],top:self.pos[o.index][1]});

				 var temp=0;
				 temp=near.index;
				 near.index=o.index;
				 o.index=temp;

				 for(var i=0;i<self.len;i++){
					  self.arr[i]=(self.item[i].index+1);
				 }
					
				 if(self.isSuccess()){
				 		clearInterval(se);
						self.tips();
			   }

			  }else{
				 self.move(o,{left:self.pos[o.index][0],top:self.pos[o.index][1]})
			  }
			  
			  
			  console.log(self.arr);
			
			  o.releaseCapture && o.releaseCapture();
			  document.ontouchmove=null;
			  document.ontouchend=null;
			  return false;
		  }
		  this.setCapture && this.setCapture();
		  ev.preventDefault && ev.preventDefault();
  }
};
Game.prototype.move=function(o,json,fn){
   o.timer && clearInterval(o.timer);
   o.timer=setInterval(function(){
	   var bStop=true;
	   for(var i in json){
		   var iCur=css(o,i);
		   var iSpeed=(json[i]-iCur)/5;iSpeed=iSpeed>0 ? Math.ceil(iSpeed) : Math.floor(iSpeed);
		   if(json[i]!=iCur){
			  bStop=false;
		   };
			o.style[i]=(iCur+iSpeed)+'px';
		  
	   };

		 if(bStop){
			  clearInterval(o.timer);
			  typeof fn=='function' && fn();
		   };

   },10);

   function css(o,attr){
	  return o.currentStyle ? parseFloat(o.currentStyle[attr]) : parseFloat(getComputedStyle(o,false)[attr]);
   }
};
Game.prototype.checkPZ=function(o1,o2){
   if(o1==o2)return;
   var l1=o1.offsetLeft,t1=o1.offsetTop,r1=o1.offsetWidth+l1,b1=o1.offsetHeight+t1;
   var l2=o2.offsetLeft,t2=o2.offsetTop,r2=o2.offsetWidth+l2,b2=o2.offsetHeight+t2;
   if(l1>r2 || t1>b2 || r1<l2 || b1<t2){
	  return false;
   }
   else
   {
	  return true;
   }
};
Game.prototype.findNear=function(o){
   var iMin=99999,index=-1;
   for(var i=0;i<this.len;i++){
	   this.item[i].className='';
	   if(this.checkPZ(o,this.item[i])){
			var l=dis(o,this.item[i]);
			if(iMin>l)
			{
				 iMin=l;
				 index=i;
			};
	   }
   };
   if(index==-1){
	  return null;
   }
   else
   {
	 return this.item[index];
   };
   function dis(o1,o2){
	  var c1=o1.offsetLeft-o2.offsetLeft,c2=o1.offsetTop-o2.offsetTop;
	  return Math.sqrt(c1*c1+c2*c2);
   }
};
 