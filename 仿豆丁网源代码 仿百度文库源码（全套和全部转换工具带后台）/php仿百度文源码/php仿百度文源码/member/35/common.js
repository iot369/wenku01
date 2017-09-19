/*
KesionCMS通用脚本函数，最后更新于2008-9-28
*/
//容错脚本
ResumeError=function ()
{
        return true;
}
window.onerror = ResumeError;

 //检查是否中文字符
is_zw=function(str){
	exp=/[0-9a-zA-Z_.,#@!$%^&*()-+=|\?/<>]/g;
	if(str.search(exp) != -1)
	{
		return false;
	}
	return true;
}
//验证是否包含逗号
CheckBadChar=function (Obj,AlertStr)
{
	exp=/[,，]/g;
	if(Obj.value.search(exp) != -1)
	{   alert(AlertStr+"不能包含逗号");
	    Obj.value="";
		Obj.focus();
		return false;
	}
	return true;
}
// 检查是否有效的扩展名
IsExt=function(FileName, AllowExt){
		var sTemp;
		var s=AllowExt.toUpperCase().split("|");
		for (var i=0;i<s.length ;i++ ){
			sTemp=FileName.substr(FileName.length-s[i].length-1);
			sTemp=sTemp.toUpperCase();
			s[i]="."+s[i];
			if (s[i]==sTemp){
				return true;
				break;
			}
		}
		return false;
}
//检查是否数字方法一
is_number=function(str){
	exp=/[^0-9()-]/g;
	if(str.search(exp) != -1)
	{
		return false;
	}
	return true;
}
//检查数字方法二
CheckNumber=function(Obj,DescriptionStr){
	if (Obj.value!='' && (isNaN(Obj.value) || Obj.value<0))
	{
		alert(DescriptionStr+"应填有效数字！");
		Obj.value="";
		Obj.focus();
		return false;
	}
	return true;
}
//检查电子邮件有效性
is_email=function(str){ 
if((str.indexOf("@")==-1)||(str.indexOf(".")==-1)){
	return false;
	}
	return true;
}
//检查日期格式是否为2008-01-01 13:01:01
is_date=function(str){   
var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/; 
var r = str.match(reg); 
if(r==null)return is_shortdate(str); 
var d= new Date(r[1], r[3]-1,r[4],r[5],r[6],r[7]); 
var v=(d.getFullYear()==r[1]&&(d.getMonth()+1)==r[3]&&d.getDate()==r[4]&&d.getHours()==r[5]&&d.getMinutes()==r[6]&&d.getSeconds()==r[7]);
if (v==false)
  return is_shortdate(str)
 else
 return true;
}
////检查日期格式是否为2008-01-01
is_shortdate=function(str){
var r = str.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/); 
if(r==null)return false; 
var d= new Date(r[1], r[3]-1, r[4]); 
return (d.getFullYear()==r[1]&&(d.getMonth()+1)==r[3]&&d.getDate()==r[4]);	
}

/* 点击选中表单li */
function chk_iddiv(id){
	var objc=document.getElementById("c"+id); //多选框
	var obju=document.getElementById("u"+id);//ul
	if (objc.checked==''){
		objc.checked='checked';
		obju.style.background='#EEF8FE';
		//obju.className='listmouseover';
	}else{
		objc.checked='';
		obju.style.background='';
		//obju.className='list';
	}
}
/**/
function chk_idBatch(form,askString){
	var bCheck;
	bCheck=false;
	for (var i=0;i < form.elements.length;i++)
    {
    var e = form.elements[i];
    if (e.name == "id"){
       if (e.checked ==1){
       		bCheck=true;
       		break;
       	}
		}
	}
	
	if (bCheck==false){
		alert("请选择要操作的内容!")
		return false;
		}
	else{
		return confirm('确认要'+askString+"?");
		}
}
function get_Ids(form)
{
	var ids='';
	for (var i=0;i < form.elements.length;i++)
	{
			var e = form.elements[i];
			if (e.name == "id"){
			   if (e.checked ==1){
			      if (ids=='')
				   ids=e.value;
				  else
					ids+=","+e.value;
				  }
				}
	}
	return ids;
}
function Select(flag)
{  
  $("input[type=checkbox]").each(function(){
  if ($(this).attr("name")=="id"){
	var objc=$("#c"+$(this).val()); 
	var obju=$("#u"+$(this).val());
	switch (flag){
	  case 0:  //全选
	   objc.attr("checked",true);
	   obju.attr("style","background:#eef8fe");
	   break;
	  case 1: //反选
		if (objc.attr("checked")==false){
			objc.attr("checked",true);
			obju.attr("style","background:#eef8fe");
		}else{
			objc.attr("checked",false);
	    	obju.attr("style","background:");
		}
		break;
	 case 2:  //不选
		objc.attr("checked",false);
	    obju.attr("style","background:");
		break;
	 }
  }
 })
}


// utility function called by getCookie( )
 function getCookieVal(offset) {
			var endstr = document.cookie.indexOf (";", offset);
			if (endstr == -1) {
				endstr = document.cookie.length;
			}
		    return unescape(document.cookie.substring(offset, endstr));
}
// primary function to retrieve cookie by name
function getCookie(name) {
			var arg = name + "=";
			var alen = arg.length;
			var clen = document.cookie.length;
			var i = 0;
			while (i < clen) {
				var j = i + alen;
				if (document.cookie.substring(i, j) == arg) { 
					return getCookieVal(j);
				}
				i = document.cookie.indexOf(" ", i) + 1;
				if (i == 0) break; 
			}
			return "";
}
// store cookie value with optional details as needed
function setCookie(name, value) {
			document.cookie = name + "=" + escape (value)
}
// remove the cookie by setting ancient expiration date
function deleteCookie(name,path,domain) {
			if (getCookie(name)) {
				document.cookie = name + "=" 
			}
}

function CheckAll(form)
{
	 for (var i=0;i<form.elements.length;i++)
	 {
		var e = form.elements[i];
		if (e.Name != 'chkAll'&&e.disabled==false)
		e.checked = form.chkAll.checked;
	}
 } 
function OpenWindow(Url,Width,Height,WindowObj){
	var ReturnStr=showModalDialog(Url,WindowObj,'dialogWidth:'+Width+'pt;dialogHeight:'+Height+'pt;status:no;help:no;scroll:no;status:0;help:0;scroll:0;');
	return ReturnStr;
}
var obj=null;
function OpenThenSetValue(Url,Width,Height,WindowObj,SetObj){
	if (document.all){
	var ReturnStr=showModalDialog(Url,WindowObj,'dialogWidth:'+Width+'pt;dialogHeight:'+Height+'pt;status:no;help:no;scroll:no;status:0;help:0;scroll:0;');
	if (ReturnStr!='' && ReturnStr!=undefined) SetObj.value=ReturnStr;
	return ReturnStr;
	}else{
	 obj=SetObj;
	 Width=Width+180;
	 Height=Height+80;
	 window.open(Url,'newWin','modal=yes,width='+Width+',height='+Height+',resizable=no,scrollbars=no');
	}
}
function setVal(v)
{ 
 obj.value=v;
}
function CheckEnglishStr(Obj,DescriptionStr)
{
	var TempStr=Obj.value,i=0,ErrorStr='',CharAscii;
	if (TempStr!='')
	{
		for (i=0;i<TempStr.length;i++)
		{
			CharAscii=TempStr.charCodeAt(i);
			if (CharAscii>=255||CharAscii<=31)
			{
				ErrorStr=ErrorStr+TempStr.charAt(i);
			}
			else
			{
				if (!CheckErrorStr(CharAscii))
				{
					ErrorStr=ErrorStr+TempStr.charAt(i);
				}
			}
		}
		if (ErrorStr!='')
		{
			alert("出错信息:\n\n"+DescriptionStr+'发现非法字符:'+ErrorStr);
			Obj.focus();
			return false;
		}
		if (!(((TempStr.charCodeAt(0)>=48)&&(TempStr.charCodeAt(0)<=57))||((TempStr.charCodeAt(0)>=65)&&(TempStr.charCodeAt(0)<=90))||((TempStr.charCodeAt(0)>=97)&&(TempStr.charCodeAt(0)<=122))))
		{
			alert(DescriptionStr+'首字符只能够为数字或者字母');
			Obj.focus();
			return false;
		}
	}
	return true;
}
function CheckErrorStr(CharAsciiCode)
{
	var TempArray=new Array(34,47,92,42,58,60,62,63,124);
	for (var i=0;i<TempArray.length;i++)
	{
		if (CharAsciiCode==TempArray[i]) return false;
	}
	return true;
}
//Obj单击的对象,OpStr--BottomFrame显示当前操作的提示信息,ButtonSymbol按钮状态,MainUrl--MainFrame的链接
function SelectObjItem1(Obj,OpStr,ButtonSymbol,MainUrl,ChannelID)
{   if (OpStr!='')
    {
		window.parent.parent.frames['BottomFrame'].location.href='KS.Split.asp?ChannelID='+escape(ChannelID)+'&OpStr='+escape(OpStr)+'&ButtonSymbol='+escape(ButtonSymbol);
		}
	if(MainUrl!='')
	{window.parent.parent.frames['MainFrame'].location.href=MainUrl;
	}

}
function FolderClick(Obj,el)
{   	var i=0;
  for (var i=0;i<document.all.length;i++)
	   {
		if (document.all(i).className=='FolderSelected') document.all(i).className='';
	    }
	         Obj.className='FolderSelected';
	  
              for (i=0;i<DocElementArr.length;i++)
			{
				if (el==DocElementArr[i].Obj)
				{
					if (DocElementArr[i].Selected==false)
					{
						DocElementArr[i].Obj.className='FolderSelectItem';
						DocElementArr[i].Selected=true;
					}
					else
					{
						DocElementArr[i].Obj.className='FolderItem';
						DocElementArr[i].Selected=false;
					}
				}
			}
}
function InsertKeyWords(obj,KeyWords)
{ 
	if (KeyWords!='')
	{
		if (obj.value.search(KeyWords)==-1)
		{
			if (obj.value=='') obj.value=KeyWords;
			else obj.value=obj.value+','+KeyWords;
			
		}
	}
	if (KeyWords == 'Clean')
	{
		obj.value = '';
	}
	return;
}
//发送参数给各个Frames窗口
function SendFrameInfo(MainUrl,LeftUrl,ControlUrl){
	location.href=MainUrl;
    parent.frames['LeftFrame'].LeftInfoFrame.location.href=LeftUrl;
	 $(parent.document).find('#BottomFrame')[0].src=ControlUrl;
}

function InsertFileFromUp(FileList,fileSize,maxId,title)
{   var files=FileList.split('/');
	var file=files[files.length-1];
	var fileext = FileList.substring(FileList.lastIndexOf(".") + 1, FileList.length).toLowerCase();
    if (fileext=="gif" || fileext=="jpg" || fileext=="jpeg" || fileext=="bmp" || fileext=="png")
	  {
		 insertHTMLToEditor('<img src="'+FileList+'" border="0"/><br/>');	
	  }else{
	  var str="<div class=\"quote\">[UploadFiles]"+maxId+","+fileSize+","+fileext+","+title+"[/UploadFiles]</div><p></p><br/>";
	     insertHTMLToEditor(str);	
	 }
}
//选择附件
function PopInsertAnnex(){
	new KesionPopup().PopupCenterIframe('选择附件插入','../plus/selectAnnex.asp',690,300,'no')
}
function Getcolor(img_val,Url,input_val){
	var arr = showModalDialog(Url, "", "dialogWidth:18.5em; dialogHeight:17.5em; status:0; help:0");
	if (arr != null){
		document.getElementById(input_val).value = arr;
		img_val.style.backgroundColor = arr;
		}
}
function OpenImgCutWindow(deloriginphoto,installdir,photourl)
{
	OpenImgCutWindows(deloriginphoto,installdir,photourl,$('#PhotoUrl')[0]);
}
function OpenImgCutWindows(deloriginphoto,installdir,photourl,obj)
{
	OpenThenSetValue(installdir+'plus/ImgCut.asp?del='+deloriginphoto+'&photourl='+photourl,680,380,window,obj);
}
function scrollDoor(){
}
scrollDoor.prototype = {
	sd : function(menus,divs,openClass,closeClass){
		var _this = this;
		if(menus.length != divs.length)
		{
			alert("菜单层数量和内容层数量不一样!");
			return false;
		}				
		for(var i = 0 ; i < menus.length ; i++)
		{	
			_this.$(menus[i]).value = i;				
			_this.$(menus[i]).onmouseover = function(){
					
				for(var j = 0 ; j < menus.length ; j++)
				{						
					_this.$(menus[j]).className = closeClass;
					_this.$(divs[j]).style.display = "none";
				}
				_this.$(menus[this.value]).className = openClass;	
				_this.$(divs[this.value]).style.display = "block";				
			}
		}
		},
	$ : function(oid){
		if(typeof(oid) == "string")
		return document.getElementById(oid);
		return oid;
	}
}
