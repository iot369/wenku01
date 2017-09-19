//举报
function   getXYWH(o){
var   nLt=0;
var   nTp=0;
  var   offsetParent   =   o;
  while   (offsetParent!=null   &&   offsetParent!=document.body)   {
  nLt+=offsetParent.offsetLeft;
  nTp+=offsetParent.offsetTop;
  offsetParent=offsetParent.offsetParent;
  }
  this.showL=nLt;
  this.showT=nTp;
  this.showW=o.offsetWidth;
  this.showH=o.offsetHeight;
}
function showFeekback(obj,elem){
 var d=document,
 o=d.getElementById(obj),
 s=d.getElementById(elem);
 if(!o||!s) return false;
  s.onclick=function(){
  var sObj=new getXYWH(this);
  _left=(sObj.showL>800)?("right:68px;"):("left:"+(sObj.showL-350)+"px;");
  o.style.cssText = ";top:"+(sObj.showT+10)+"px;"+_left;
  if(elem == "recommendMessageButtun"){
	  var productId = d.getElementById("productId").value;
  	  var sendUrl = "/app/jquery/isRecommendProduct?pid="+productId + "&rand="+new Date().getTime();
	  var result = trim(jQuery.ajax({url: sendUrl, async: false}).responseText); 
	  alert(result);
	  if(result == "true"){
	  	  d.getElementById("feekBackDiv").style.display = "block";
		  d.getElementById("recommendedMessageDiv").style.display = "block";
		  d.getElementById("reportMessageDiv").style.display = "none";
	  }else{
	  	  d.getElementById("recommendedMessageDiv").style.display = "none";
		  d.getElementById("reportMessageDiv").style.display = "none";
	  	  jQuery("#jquerydiv").load("/app/jquery/mingrenrecom?pid="+productId +"&date="+new Date().getTime()); 
	  }
	  //alert(" 1 "+elem);-
	 // alert("recommendedMessageDiv : " + d.getElementById("recommendedMessageDiv").style.display);
	 // alert("reportMessageDiv : " + d.getElementById("reportMessageDiv").style.display);
  }else{
  	  d.getElementById("feekBackDiv").style.display = "block";
  	  d.getElementById("recommendedMessageDiv").style.display = "none";
	  d.getElementById("reportMessageDiv").style.display = "block";
	 // alert(" 2 "+elem);
	 // alert("recommendedMessageDiv : " + d.getElementById("recommendedMessageDiv").style.display);
	 // alert("reportMessageDiv : " + d.getElementById("reportMessageDiv").style.display);
  }
  }
}
function hiddenFeekback(){
var d=document;
d.getElementById("subTip").innerHTML="输入与之重复的另一篇文档URL地址";
d.getElementById("subTip").style.color = "#000";
d.getElementById("purl").value="";
d.getElementById("feekBackDiv").style.display = "none";
}
//发送举报消息
function sendFeekBack(id){
	var d=document,
  content = "",
  type = 2 ,errs="";
 	var error3 = d.getElementById("error3").value;
	if(error3==""){	//没有填写举报内容testarea
		if(d.getElementById("error1").checked){
			type = type + 3;
			content = d.getElementById("error1").value;
		}else if(d.getElementById("error2").checked){
			type = type + 5;
//			content = d.getElementById("error2").value;
			content = d.getElementById("purl").value; 
			if(content==""||content.length<1||content=="输入与之重复的另一篇文档URL地址"){
				alert("请输入重复文档!");
				return ;
			}else{
				var begin=content.indexOf("-");
				var  end=content.lastIndexOf(".html");
				if(begin==-1 || end==-1){
					alert("输入正确网址");
					return;
				}else{
				var jbid=content.substring(begin+1,end);

				if(parseInt(jbid)>parseInt(id)){
				  alert("您的举报无效，请核实举报文档的上传时间");
				  return;
				}else if(parseInt(jbid)==parseInt(id)){
				 d.getElementById("subTip").innerHTML="输入文档链接有误，不能举报同一篇文档！";
				 d.getElementById("subTip").style.color = "#f00";
				 return;
				 }
			  }
			}
			
		}else if(d.getElementById("error4").checked){
			type = type + 4;
			content = d.getElementById("error4").value;
		}else if(d.getElementById("error5").checked){
			type=type+2;
			content = d.getElementById("error5").value;
			var mes=document.getElementsByName("errors");
			for(var i=0;i<mes.length;i++){
				if(mes[i].checked){
				content=content+mes[i]+" ";
				}
			}
		}else{
			alert("请添写或选择举报信息!");
			return;
		}
	}else{
		content = error3;
		if(d.getElementById("error2").checked){
			type = type + 5;
//			content = d.getElementById("error2").value;
			content = d.getElementById("purl").value; 
			if(content==""||content.length<1||content=="输入与之重复的另一篇文档URL地址"){
				alert("请输入重复文档!");
				return ;
			}else{
				var begin=content.indexOf("-");
				var  end=content.lastIndexOf(".html");
				if(begin==-1 || end==-1){
					alert("输入正确网址");
					return;
				}else{
				var jbid=content.substring(begin+1,end);

				if(parseInt(jbid)>parseInt(id)){
				alert("您的举报无效，请核实举报文档的上传时间");
				return;
				}else if(parseInt(jbid)==parseInt(id)){
				 d.getElementById("subTip").innerHTML="输入文档链接有误，不能举报同一篇文档！";
				 d.getElementById("subTip").style.color = "#f00";
				 return;
				 } 
			  }
			}
			
		}
	}
	var url = "/plus/jubao.php?content="+encodeURI(content)+
					"&aid="+id+"&type="+type+"&flag=sendFeekBack&date="+new Date().getTime();
		jQuery("#a5").load(url);
		alert("谢谢您对本网站的支持，我们会尽快处理您的建议！");
		hiddenFeekback();

}
 function changeRadio(){
 	var d = document;
 	var obj = d.getElementById("error2");
  d.getElementById("subTip").innerHTML="输入与之重复的另一篇文档URL地址";
  d.getElementById("subTip").style.color = "#000";
  d.getElementById("purl").value="";
  d.getElementById('repeatproduct').style.display=(obj.checked)?"block":"none";
 }