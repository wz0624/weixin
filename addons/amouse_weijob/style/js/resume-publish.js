window.onload=function () {
	var myform=document.myform;
	var jobname = myform.jobname;
	var description = myform.description;
	var workplace = myform.workplace;
	var person_mobiles = myform.person_mobile;
	var person_selfs = myform.person_self;
	var number = myform.number;
	var label1=document.getElementsByTagName("label")[1];
	var label2=document.getElementsByTagName("label")[16];
	var label3=document.getElementsByTagName("label")[20];
	var label4=document.getElementsByTagName("label")[6];
	var label5=document.getElementsByTagName("label")[12];
	myform.onsubmit=function () {
		if(jobname.value==""){
		label1.innerHTML="招聘岗位不能为空!";
		jobname.focus();
		return false;
		}else{
		label1.innerHTML="";
		}
		if(workplace.value==""){
		label2.innerHTML="工作地点不能为空!";
		workplace.focus();
		return false;
		}else{
		label2.innerHTML="";
		}
		if(description.value==""){
		label3.innerHTML="岗位详情不能为空!";
		description.focus();
		return false;
		}else{
		label3.innerHTML="";
		}
		
	}
}