window.onload=function () {
	var myform=document.myform;
	var person_names = myform.person_name;
	var person_ages = myform.person_age;
	var person_major = myform.person_major;
	var person_mobiles = myform.person_mobile;
	var person_selfs = myform.person_self;
	var college = myform.college;
	var person_skill = myform.person_skill;
	var person_direction = myform.person_direction;
	var person_email = myform.person_email;
	var person_home = myform.person_home;
	var label1=document.getElementsByTagName("label")[1];
	var label2=document.getElementsByTagName("label")[6];
	var label3=document.getElementsByTagName("label")[8];
	var label4=document.getElementsByTagName("label")[10];
	var label5=document.getElementsByTagName("label")[12];
	var label6=document.getElementsByTagName("label")[14];
	var label7=document.getElementsByTagName("label")[16];
	var label8=document.getElementsByTagName("label")[18];
	var label9=document.getElementsByTagName("label")[19];
	var label10=document.getElementsByTagName("label")[21];
	myform.onsubmit=function () {
		if(person_names.value==""){
		label1.innerHTML="姓名不能为空!";
		person_names.focus();
		return false;
		}else{
		label1.innerHTML="";
		}
		if(person_ages.value==""){
		label2.innerHTML="出生年月不能为空!";
		person_ages.focus();
		return false;
		}else{
		label2.innerHTML="";
		}
		if(person_major.value==""){
		label3.innerHTML="专业不能为空!";
		person_major.focus();
		return false;
		}else{
		label3.innerHTML="";
		}
		if(college.value==""){
		label4.innerHTML="毕业院校不能为空!";
		college.focus();
		return false;
		}else{
		label4.innerHTML="";
		}
		if(person_mobiles.value==""){
		label5.innerHTML="手机号码不能为空!";
		person_mobiles.focus();
		return false;
		}else{
		label5.innerHTML="";
		}
		if(person_email.value==""){
		label6.innerHTML="邮箱不能为空!";
		person_email.focus();
		return false;
		}else{
		label6.innerHTML="";
		}
		if(person_skill.value==""){
		label7.innerHTML="专业技能不能为空!";
		person_skill.focus();
		return false;
		}else{
		label7.innerHTML="";
		}
		if(person_selfs.value==""){
		label9.innerHTML="自我评价不能为空!";
		person_selfs.focus();
		return false;
		}else{
		label9.innerHTML="";
		}
		if(person_direction.value==""){
		label10.innerHTML="求职意向不能为空!";
		person_direction.focus();
		return false;
		}else{
		label10.innerHTML="";
		}
	}
}