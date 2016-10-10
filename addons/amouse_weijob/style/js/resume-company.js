window.onload=function () {
	var myform=document.myform;
	var company_names = myform.companyname;
	var shortnames = myform.shortname;
	var link_man = myform.link_man;
	var company_emails = myform.company_email;
	var company_mobiles = myform.company_mobile;
	var address = myform.address;
	var label1=document.getElementsByTagName("label")[1];
	var label2=document.getElementsByTagName("label")[3];
	var label3=document.getElementsByTagName("label")[4];
	var label4=document.getElementsByTagName("label")[6];
	var label5=document.getElementsByTagName("label")[8];
	var label6=document.getElementsByTagName("label")[12];
	myform.onsubmit=function () {
		if(company_names.value==""){
		label1.innerHTML="公司名称不能为空!";
		company_names.focus();
		return false;
		}else{
		label1.innerHTML="";
		}
		if(link_man.value==""){
		label3.innerHTML="联系人不能为空!";
		link_man.focus();
		return false;
		}else{
		label3.innerHTML="";
		}
		if(company_emails.value==""){
		label4.innerHTML="邮箱不能为空!";
		company_emails.focus();
		return false;
		}else{
		label4.innerHTML="";
		}
		if(company_mobiles.value==""){
		label5.innerHTML="手机号码不能为空!";
		company_mobiles.focus();
		return false;
		}else{
		label5.innerHTML="";
		}
		if(address.value==""){
		label6.innerHTML="公司地址不能为空!";
		address.focus();
		return false;
		}else{
		label6.innerHTML="";
		}
	}
}