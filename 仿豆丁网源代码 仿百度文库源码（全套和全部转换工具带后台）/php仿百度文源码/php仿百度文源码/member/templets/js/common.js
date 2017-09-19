function $() {
	var elements = new Array();
	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);
		if (arguments.length == 1)
			return element;
		elements.push(element);
	}
	return elements;
}

function stopBubble(e) { //阻止冒泡 kouyubo
    if ( e && e.stopPropagation )
     e.stopPropagation();
    else
     window.event.cancelBubble = true;
}
function stopDefault( e ) {
    if ( e && e.preventDefault )
     e.preventDefault();
    else
     window.event.returnValue = false;
    return false;
}

function getPosLeft(obj) 
{ 
	left = obj.offsetLeft; 
	while(obj = obj.offsetParent) 
	{ 
		left += obj.offsetLeft;
	} 
	return left; 
}

function getPosTop(obj) 
{ 
var t = obj.offsetTop; 
while(obj = obj.offsetParent) 
{ 
t += obj.offsetTop;
} 
return t; 
}

function getStyle(o,n){
	return o.currentStyle?o.currentStyle[n]:(document.defaultView.getComputedStyle(o,"").getPropertyValue(n))
}

function hasClass(ele,cls) {
  return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}
 
function addClass(ele,cls) {
  if (!this.hasClass(ele,cls)) ele.className += " "+cls;
}
 
function removeClass(ele,cls) {
  if (hasClass(ele,cls)) {
          var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
    ele.className=ele.className.replace(reg,' ');
  }
}

function ShowMemo(obj,id)
{
	$("Memo"+id).style.display = "";
}

function HideMemo(id)
{
	$("Memo"+id).style.display = "none";
}
function ltrim(s){
    return s.replace( /^\s*/, "");
}

function rtrim(s){
    return s.replace( /\s*$/, "");
}

function trim(s){
    return rtrim(ltrim(s));
}

function checkUserName(username){
	filter=/^[a-zA-Z0-9\u0391-\uFFE5]{2,20}/;
	if(!filter.test(trim(username))){
		return false;
	}else{
		return true;
	}
}

function checkPassWord(username){
	filter=/^[a-zA-Z0-9\u0391-\uFFE5]{2,20}/;
	if(!filter.test(trim(username))){
		return false;
	}else{
		return true;
	}
}
function checkDate(dateStr){
	filter=/^\d{4}-((0[1-9]{1})|(1[0-2]{1}))-((0[1-9]{1})|([1-2]{1}[0-9]{1})|(3[0-1]{1}))$/;
	if(!filter.test(trim(dateStr))){
		return false;
	}else{
		return true;
	}
	
}
function checkNumber(num){
	//filter=/^[0-9\+-\.]{1,10}$/;
	filter=/^-?([1-9][0-9]*|0)(\.[0-9]+)?$/;
	if(!filter.test(trim(num))){
		return false;
	}else{
		return true;
	}
}
function checkNumberInt(num){
	//filter=/^[0-9\+-\.]{1,10}$/;
	filter=/^-?([1-9][0-9]*|0)$/;
	if(!filter.test(trim(num))){
		return false;
	}else{
		return true;
	}
}
function checkPositiveNumber(num){
	//filter=/^[0-9\+-\.]{1,10}$/;
	filter=/^([1-9][0-9]*|0)$/;
	if(!filter.test(trim(num))){
		return false;
	}else{
		return true;
	}
}
function checkNumber2(num){
	//filter=/^[0-9\+-\.]{1,10}$/;
	filter=/^-?([1-9][0-9]*|0)?(\.[0-9]{1,2})?$/;
	if(!filter.test(trim(num))){
		return false;
	}else{
		return true;
	}
}
function checkEmail(email){
	filter=/^([a-zA-Z0-9_\-\.\+]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	if(!filter.test(trim(email))){
		return false;
	}else{
		return true;
	}
}
function getLength(str)//
{
   count = 0;
   for (i = 0; i < str.length; i++) 
   {
      if (((str.charCodeAt(i) >= 0x3400) && (str.charCodeAt(i) < 0x9FFF)) || (str.charCodeAt(i) >= 0xF900))
      {
         count+=2;
      }else{
      	 count++;
      }
   }
   return count;
}

function getLeft(str,len){
	i=0;
	for(i=0;i<len;i++){
		 if (((str.charCodeAt(i) >= 0x3400) && (str.charCodeAt(i) < 0x9FFF)) || (str.charCodeAt(i) >= 0xF900))
      {
         len--;
      }
	  
	}
	str=str.substr(0,i);
	str+="..";
	return str;
}

function left(str,len){
	
	if(getLength(str)>len){
		str=getLeft(str,len-2);
	}
	return str;
}
function checkNumberAndString(str){
	filter=/^[a-zA-Z0-9]{10,50}$/;
	if(!filter.test(trim(str))){
		return false;
	}else{
		return true;
	}
}
function getCurrentDate(c){
	 d = new Date();
	 s="";
	 year=d.getFullYear();    
     month=1+d.getMonth();   
     date=d.getDate();       
     if(month<10){
     	month="0"+month;
     }
     if(date<10){
     	date="0"+date;
     }
     s=year+c+month+c+date;
	 return s;
}
function getCurrentTime(c){
		  var d, s = "";
  		  d = new Date();
 		  s += d.getHours() + c;
 		  s += d.getMinutes() + c;
 		  s += d.getSeconds() + c;
  		  s += d.getMilliseconds();
  		  return s;
}
function getAbsoluteHeight(ob){
	return ob.offsetHeight;
}
function getAbsoluteTop(ob){
	var s_el=0;
	el=ob;
	while(el){
		s_el=s_el+el.offsetTop ;
		el=el.offsetParent;
	}; 
	return s_el;
}
function getAbsoluteLeft(ob){
	var s_el=0;el=ob;
	while(el){
		s_el=s_el+el.offsetLeft;
		el=el.offsetParent;
	};
	return s_el;
}
function setCookie2008_1(name, value,day) {
	str = name + "=" + escape(value);
	if(day>0){
		expires = day*24*60;
		exp=new Date(); 
		exp.setTime(exp.getTime() + expires*60*1000);
		str += "; expires="+exp.toGMTString();
		str += "; path=/";
		if(location.href.indexOf("docin.com")==-1){
			str += "; domain=.vonibo.com";
		}else{
			str += "; domain=.docin.com";
		}
	}
	document.cookie = str;
} 
//get cookie by cookie's name
//name(String): cookie's name
function getCookie2008_1(name){
	var tmp, reg = new RegExp("(^| )"+name+"=([^;]*)(;|$)","gi");
	if( tmp = reg.exec( unescape(document.cookie) ) ) return(tmp[2]);
	return null;
}

 
//dynamic include another js file
function include_js(path,reload)
{
	var scripts = document.getElementsByTagName("script");
	if (reload==null || !reload)
	for (var i=0;i<scripts.length;i++){
		if (scripts[i].src && scripts[i].src.toLowerCase() == path.toLowerCase() ) 
			return;
	}
	var sobj = document.createElement('script');
	sobj.type = "text/javascript";
	sobj.src = path;
	var headobj = document.getElementsByTagName('head')[0];
	headobj.appendChild(sobj);
}

//hidden element by id
//function hidden(id){
//	if($(id)!=null) $(id).style.display="none";
//}
//set element visible
//function show(id){
//	if($(id)!=null) $(id).style.display="block";
//}
//韩日站实用的搜索
function topsearch_jp(pid){
	keyword=trim(document.getElementById("topsearch").value);
	if(keyword.length!=0){
		filter=/^[^`~!@#$%^&*()+=|\\\][\]\{\}:;\,.<>/?]{1}[^`~!@$%^&()+=|\\\][\]\{\}:;\,.<>?]{0,19}$/;
		if(!filter.test(keyword)){
			alert("キーワードを正確に入力してください");
		}else{
			url="/app/searchinter?keyword="+encodeURI(keyword);
			if(pid!=0){
				url+="&pid="+pid;
			}
			location.href=url;
		}
	}
}
function topsearch_kr(pid){
	keyword=trim(document.getElementById("topsearch").value);
	if(keyword.length!=0){
		filter=/^[^`~!@#$%^&*()+=|\\\][\]\{\}:;\,.<>/?]{1}[^`~!@$%^&()+=|\\\][\]\{\}:;\,.<>?]{0,19}$/;
		if(!filter.test(keyword)){
			alert("키워드를 입력해주세요");
		}else{
			url="/app/searchinter?keyword="+encodeURI(keyword);
			if(pid!=0){
				url+="&pid="+pid;
			}
			location.href=url;
		}
	}
}
//中国站使用的搜索  高级搜索中关键词搜索
function searchProduct(keyword){
	keyword=trim(keyword);
	if(keyword.length!=0){
		filter=/^[^`~!@#$%^&*+=|\\\][\]\{\}:;\,<>/?]{1}[^`~!@$%^&+=|\\\][\]\{\}:;\,<>?]{0,19}$/;
		if(!filter.test(keyword) || keyword=="在一亿文档库里搜索文档"){
			alert("请输入正确的关键字");
		}else{
			url="/app/docsearch?keyword="+encodeURI(keyword);
			location.href=url;
		}
	}
}

//中国站使用的搜索  关键词搜索
function searchNew(){
	keyword = trim(document.getElementById("topsearch").value);
	if(keyword=="" || keyword.length==0 || keyword=="在一亿文档库里搜索文档"){
		alert("请输入要搜索的关键词!");
		document.getElementById("topsearch").focus();
		return false;
	}
	var searchType_banner = document.getElementById("searchType_banner");
	if(searchType_banner != null && searchType_banner == "u"){
		document.getElementById("fn").value = "user";
	}
	return true;
}

function searchiphone(){
	var keyword = trim(document.getElementById("topsearch").value);
	if(keyword=="" || keyword.length==0 || keyword=="输入关键词搜索"){
		alert("请输入要搜索的关键词!");
		return false;
	}
}

//edu使用的搜索  关键词搜索
function search_edu(){
	keyword = trim(document.getElementById("eduSearchKey").value);
	if(keyword=="" || keyword.length==0 || keyword=="输入文档关键词搜索"){
		alert("请输入要搜索的关键词!");
		return false;
	}
	return true;
}

//中国站使用的搜索  关键词搜索
function searchNewDown(){
	keyword = trim(document.getElementById("topsearchZh").value);
	if(keyword=="" || keyword.length==0 || keyword=="输入关键词搜索"){
		alert("请输入要搜索的关键词!");
		return false;
	}
	return true;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
	 var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
	 if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	 d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
	if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

//---------------------------------------------- cookies star -------------------------------------------------------//
		
	//----------------uuid file ------------------------//
		/*
		http://www.af-design.com/services/javascript/uuid/
		
		uuid.js - Version 0.3
		JavaScript Class to create a UUID like identifier
		
		Copyright (C) 2006-2008, Erik Giberti (AF-Design), All rights reserved.
		
		This program is free software; you can redistribute it and/or modify it under 
		the terms of the GNU General Public License as published by the Free Software 
		Foundation; either version 2 of the License, or (at your option) any later 
		version.
		
		This program is distributed in the hope that it will be useful, but WITHOUT ANY 
		WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A 
		PARTICULAR PURPOSE. See the GNU General Public License for more details.
		
		You should have received a copy of the GNU General Public License along with 
		this program; if not, write to the Free Software Foundation, Inc., 59 Temple 
		Place, Suite 330, Boston, MA 02111-1307 USA
		
		The latest version of this file can be downloaded from
		http://www.af-design.com/resources/javascript_uuid.php
		
		HISTORY:
		6/5/06 	- Initial Release
		5/22/08 - Updated code to run faster, removed randrange(min,max) in favor of
		          a simpler rand(max) function. Reduced overhead by using getTime() 
		          method of date class (suggestion by James Hall).
		9/5/08	- Fixed a bug with rand(max) and additional efficiencies pointed out 
			  by Robert Kieffer http://broofa.com/
		
		KNOWN ISSUES:
		- Still no way to get MAC address in JavaScript
		- Research into other versions of UUID show promising possibilities 
		  (more research needed)
		- Documentation needs improvement
		
		*/
		
		// On creation of a UUID object, set it's initial value
		function UUID(){
			this.id = this.createUUID();
		}
		
		// When asked what this Object is, lie and return it's value
		UUID.prototype.valueOf = function(){ return this.id; }
		UUID.prototype.toString = function(){ return this.id; }
		
		//
		// INSTANCE SPECIFIC METHODS
		//
		
		UUID.prototype.createUUID = function(){
			//
			// Loose interpretation of the specification DCE 1.1: Remote Procedure Call
			// described at http://www.opengroup.org/onlinepubs/009629399/apdxa.htm#tagtcjh_37
			// since JavaScript doesn't allow access to internal systems, the last 48 bits 
			// of the node section is made up using a series of random numbers (6 octets long).
			//  
			var dg = new Date(1582, 10, 15, 0, 0, 0, 0);
			var dc = new Date();
			var t = dc.getTime() - dg.getTime();
			var h = '';
			var tl = UUID.getIntegerBits(t,0,31);
			var tm = UUID.getIntegerBits(t,32,47);
			var thv = UUID.getIntegerBits(t,48,59) + '1'; // version 1, security version is 2
			var csar = UUID.getIntegerBits(UUID.rand(4095),0,7);
			var csl = UUID.getIntegerBits(UUID.rand(4095),0,7);
		
			// since detection of anything about the machine/browser is far to buggy, 
			// include some more random numbers here
			// if NIC or an IP can be obtained reliably, that should be put in
			// here instead.
			var n = UUID.getIntegerBits(UUID.rand(8191),0,7) + 
					UUID.getIntegerBits(UUID.rand(8191),8,15) + 
					UUID.getIntegerBits(UUID.rand(8191),0,7) + 
					UUID.getIntegerBits(UUID.rand(8191),8,15) + 
					UUID.getIntegerBits(UUID.rand(8191),0,15); // this last number is two octets long
			return tl + h + tm + h + thv + h + csar + csl + h + n; 
		}
		
		
		//
		// GENERAL METHODS (Not instance specific)
		//
		
		
		// Pull out only certain bits from a very large integer, used to get the time
		// code information for the first part of a UUID. Will return zero's if there 
		// aren't enough bits to shift where it needs to.
		UUID.getIntegerBits = function(val,start,end){
			var base16 = UUID.returnBase(val,16);
			var quadArray = new Array();
			var quadString = '';
			var i = 0;
			for(i=0;i<base16.length;i++){
				quadArray.push(base16.substring(i,i+1));	
			}
			for(i=Math.floor(start/4);i<=Math.floor(end/4);i++){
				if(!quadArray[i] || quadArray[i] == '') quadString += '0';
				else quadString += quadArray[i];
			}
			return quadString;
		}
		
		// Replaced from the original function to leverage the built in methods in
		// JavaScript. Thanks to Robert Kieffer for pointing this one out
		UUID.returnBase = function(number, base){
			return (number).toString(base).toUpperCase();
		}
		
		// pick a random number within a range of numbers
		// int b rand(int a); where 0 <= b <= a
		UUID.rand = function(max){
			return Math.floor(Math.random() * (max + 1));
		}
		
	// end of UUID class file
	
	//----------------uuid file end-----------------------//
	
	//-----------------cookies file -----------------------//
		function CookieClass()
		{
		this.expires = 0 ; //有效时间,以分钟为单位 
		this.path = ""; //设置访问路径 
		this.domain = ""; //设置访问主机 
		this.secure = false; //设置安全性
		
		this.setCookie = function(name,value)
		{ 
		   var str = name+"="+escape(value); 
		   if (this.expires>0)
		   { 
		    //如果设置了过期时间 
		    var date=new Date(); 
		    var ms=this.expires * 60 * 1000; //每分钟有60秒，每秒1000毫秒 
		    date.setTime(date.getTime()+ms); 
		    str+="; expires="+date.toGMTString(); 
		   } 
		  
		   if(this.path!="")str+="; path="+this.path; //设置访问路径 
		   if(this.domain!="")str+="; domain="+this.domain; //设置访问主机 
		   if(this.secure!="")str+="; true"; //设置安全性
		
		   document.cookie=str; 
		}
		
		this.getCookie=function(name)
		{ 
		   var cookieArray=document.cookie.split("; "); //得到分割的cookie名值对 
		   var cookie=new Object(); 
		   for(var i=0;i<cookieArray.length;i++)
		   { 
		    var arr=cookieArray[i].split("="); //将名和值分开 
		    if(arr[0]==name)return unescape(arr[1]); //如果是指定的cookie，则返回它的值 
		   } 
		   return ""; 
		}
		
		this.deleteCookie=function(name)
		{ 
		   var date=new Date(); 
		   var ms= 1 * 1000; 
		   date.setTime(date.getTime() - ms); 
		   var str = name+"=no; expires=" + date.toGMTString(); //将过期时间设置为过去来删除一个cookie 
		   document.cookie=str; 
		}
		
		this.showCookie=function()
		{ 
		   alert(unescape(document.cookie)); 
		}
		}
		
		//使用例子 
		//var cook = new CookieClass(); 
		//cook.expires =1;//一分钟有效 
		//cook.setCookie("01","5556666666666555");//写 
		//alert(cook.getCookie("01"));//读 
		//cook.showCookie();
	 
	//-----------------cookies file end-----------------------//
	
	//向用户端写一个uuid 
	function write_cookie_uuid(){	
		var cook = new CookieClass(); 
		var c_name="cookie_id"; 
		var c_value=cook.getCookie(c_name);//读
		if( c_value==""){
			c_value=new UUID();
			cook.expires =60*24*365;//一年有效 
			cook.domain=".docin.com";
			cook.path="/";
			cook.setCookie(c_name,c_value);//写
			
			var now= new Date();
			var year=now.getFullYear();
			var month=now.getMonth()+1;
			var day=now.getDate();
			var hour=now.getHours();
			var minute=now.getMinutes();
			var second=now.getSeconds();
    		var time = year + "" + month + "" + day + "" + hour + "" + minute + "" + second;
			var t_name = "time_id";
			var t_value = time;
			var tcook = new CookieClass();
			tcook.expires =60*24*365;//一年有效 
			tcook.domain=".docin.com";
			tcook.path="/";
			tcook.setCookie(t_name,t_value);//写
		}		 
		//alert(c_value);
	}
	
	//终极页专用
	//终极页判断如果有这个cookie就说明是第二次来，所以不记ip。
	function w_p_end_cookie(){
		var cook = new CookieClass(); 
		var c_name="p_end"; 
		var c_value=cook.getCookie(c_name);//读
		if( c_value==""){		
			c_value="1";
			cook.expires =60*24*365;//一年有效 
			cook.domain=".docin.com";
			cook.path="/";
			cook.setCookie(c_name,c_value);//写
		}		 
		//alert("p_end:"+c_value);	
	}
	
	//----------------- ajax -------------------//
		var httpRequest=false;    
		function createRequest()    
		{    
		 
		   var request = false;

		    try{    
		        request=new XMLHttpRequest();    
		    }catch(trymicrosoft){    
		        try{    
		            request=new ActiveXObject("Msxml2.XMLHTTP");    
		        }catch(othermicrosoft){    
		            try{    
		                request=new ActiveXObject("Microsoft.XMLHTTP");    
		            }    
		            catch(failed)    
		            {    
		                request=false;    
		            }    
		        }    
		    }
			//alert(request);    
		    if(!request)    
		    {    
		      // alert("err Happend!");    
		       return null;    
		    }           
			
		    return request;
		/*
			var request = false;
			try {
			  request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
			  try {
			    request = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (e2) {
			    request = false;
			  }
			}
			if (!request && typeof XMLHttpRequest != 'undefined') {
			  request = new XMLHttpRequest();
			}		
			//alert(request);
			return request;
			*/
		}    
		httpRequest=createRequest();    
		function inser_cookie_ip()    
		{    
			
			if(httpRequest==null){
				return;
			}
			var cook = new CookieClass();
		    var c_name="cookie_id"; 
			var c_value=cook.getCookie(c_name);//读
			if( c_value==""){
				return ;//没有cookieid,不发送
			}
			var c_name_p="p_end"; 
			var c_value_p=cook.getCookie(c_name_p);//读
			if(c_value_p!=""){
				return;//有p_end说明已发送过一次，不再发送
			}
			
		    var url='/app/cookies/insertCookies?tmp='+getCurrentTime('-');    
			//alert(url);
		    httpRequest.open("GET",url,true);    
		    //下面相当于是一个隐性的循环，在函数中规定只有都接收完毕数据后才做处理    
		    //onreadystatechange有5个值：    
		    // 0:未初始化    
		    // 1:初始化    
		    // 2:发送数据    
		    // 3:接收数据中    
		    // 4:数据接收完毕    
		    //另外还要注意就是在注册回调函数onreadystatechange时，后面的函数不能够带参数    
		    //如下disResult是一个函数，不能够带参。    
		    httpRequest.onreadystatechange=disResult;//隐性的循环    
		    httpRequest.send(null);    
		}    
		function disResult()    
		{    
		    //1.一定要确定readystate==4的完成状态才做下面的事，否则会在建立连接即readystate==1的时候就开始，    
		    //  然后会在readystate==2，readystate==3，readystate==4的时候都会执行，不信你可以alert("")一个    
		    //  提示信息试试。    
		    //2.服务器通知完成了，并且还要保证是正确完成的，得到的是我们需要的结果才能够继续，这里常用响应码有：    
		    //  200:成功执行    
		    //  401:未授权    
		    //  403:禁止    
		    //  404:没有找到文件    
			//alert(httpRequest.readyState);
			
		    if(httpRequest.readyState==4)    
		    {    
				//alert(httpRequest.status);
		        if(httpRequest.status==200)    
		        {    
		          w_p_end_cookie();//插入cookie_ip成功,存一个cookie(p_end)
		        }         
		    }    
		}    
	//----------------- ajax end -------------------//
	
	
	//----------------- 执行 star -------------------//
	write_cookie_uuid();
	//----------------- 执行 end -------------------//



//---------------------------------------------- cookies end -------------------------------------------------------//

//---------------- 更改所有连接到终极页的target  start ---------------//

	/*
var _a_os=document.getElementsByTagName('A');
_a_o_l=_a_os.length;
for(i=0;i<_a_o_l;i++){
	var _a_o=_a_os[i];
	_a_o_href=_a_o.href;
	if(_a_o_href.indexOf("/p-")>0 || _a_o_href.indexOf("/product-")>0 || _a_o_href.indexOf("/p?")>0 || _a_o_href.indexOf("/product?")>0){
		_a_o.setAttribute("target","docin_p_end");	
	}	
	
}
*/
	
//---------------- 更改所有连接到终极页的target  end ---------------//

//---------------0730 终极页播放器缩进------------------//
 function hideEndList(){
	var d = document,
		o = d.getElementById("DocinViewer"),
		s = d.getElementById("sider"),
		i = d.getElementById("indent-flash");
	s.style.display="none";
	addClass(o,"doc-player-tips");
	i.style.display="block";
 }
 function showEndList(){
	var d = document,
		o = d.getElementById("DocinViewer"),
		s = d.getElementById("sider"),
		i = d.getElementById("indent-flash");
	s.style.display="block";
	removeClass(o,"doc-player-tips");
	i.style.display="none";
 }

 function getCookieId(){
	 var cc = new CookieClass();
	 var cookieId = cc.getCookie("cookie_id");
	 return cookieId;
 }
//--------------倒数时间跳转-----------------//
/*
var __n=4;
function auditTime(obj,url,time){
	if(!obj||!$(obj)) return false;
	if(!url||url=="") url="/";
	if(obj.audit){
		clearTimeout(obj.audit);
	}
	if(__n<1){
		location.href=url;
		__n=4;
		return false;
	}else{
		$(obj).innerHTML=__n;
	}
	__n--;
	var t="auditNum('"+obj+"','"+url+"','"+time+"')";
	obj.audit=setTimeout(t,time);
}
auditTime("audit","/",1000)
*/
//-----------------下拉列表模拟--------------------//
function boxSelect(obj,elem){
	var nmove,
		d = document,
		o = d.getElementById(obj),
		s = d.getElementById(elem);
	if(!o){ return false};
	if(!s){ return false};
	o.onmousedown=function(){
		clearTimeout(nmove);
		o.className = (o.className=="cur")?"":"cur";
	}
	s.onmouseover=function(){
		clearTimeout(nmove);
	}
	o.onmouseout=function(){
		nmove=setTimeout(function(){o.className=""},1000);
	}
	for(i=0;i<s.getElementsByTagName("li").length;i++){
		s.getElementsByTagName("li")[i].onmouseover=function(){
			this.className="ahover";	
		}
		s.getElementsByTagName("li")[i].onmouseout=function(){
			this.className="";	
		}
		s.getElementsByTagName("li")[i].onmousedown=function(){
			o.className="cur";
			o.childNodes[0].childNodes[0].nodeValue=this.childNodes[0].childNodes[0].nodeValue;
		}
	}

}
boxSelect("search-listbtn","search-listtags");

/*share box*/
function boxShare(obj,elem){
	var nmove,mmove,
		d = document,
		o = d.getElementById(obj);
		s = d.getElementById(elem);
	if(!o){ return false};
	if(!s){ return false};
	
	s.onmouseover=function(){
		clearTimeout(nmove);
		s.style.display="block";
	}
	o.onmouseover=function(){
		clearTimeout(nmove);
		mmove=setTimeout(function(){s.style.display="block";},100);
	}
	o.onmouseout=function(){
		clearTimeout(mmove);
		nmove=setTimeout(function(){s.style.display="none";},500);
	}
	s.onmouseout=function(){
		nmove=setTimeout(function(){s.style.display="none";},500);
	}
	s.onmousedown=function(e){
		stopBubble(e);
		return false;
	}
}
boxShare("scmore","sctips");