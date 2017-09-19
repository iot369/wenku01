//中国站使用的搜索  高级搜索中关键词搜索
function searchProduct(keyword){
	keyword=trim(keyword);
	if(keyword.length!=0){
		//filter=/^[^`~!@#$%^&*+=|\\\][\]\{\}:;\,<>/?]{1}[^`~!@$%^&+=|\\\][\]\{\}:;\,<>?]{0,19}$/;
		filter=new RegExp('^[^`~!@#$%^&*+=|\\\][\]\{\}:;\,<>/?]{1}[^`~!@$%^&+=|\\\][\]\{\}:;\,<>?]{0,19}$');
		if(!filter.test(keyword) || keyword=="在一亿文档库里搜索文档"){
			//alert("请输入正确的关键字");
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
		//alert("请输入要搜索的关键词!");
		document.getElementById("topsearch").focus();
		return false;
	}
	var searchType_banner = document.getElementById("searchType_banner");
	if(searchType_banner != null && searchType_banner.value == "u"){
		document.getElementById("searchUser").value="2";
	}
	return true;
}

function searchiphone(){
	var keyword = trim(document.getElementById("topsearch").value);
	if(keyword=="" || keyword.length==0 || keyword=="输入关键词搜索"){
		//alert("请输入要搜索的关键词!");
		return false;
	}
}

//edu使用的搜索  关键词搜索
function search_edu(){
	keyword = trim(document.getElementById("eduSearchKey").value);
	if(keyword=="" || keyword.length==0 || keyword=="输入文档关键词搜索"){
		//alert("请输入要搜索的关键词!");
		return false;
	}
	return true;
}

//中国站使用的搜索  关键词搜索
function searchNewDown(){
	keyword = trim(document.getElementById("topsearchZh").value);
	if(keyword=="" || keyword.length==0 || keyword=="输入关键词搜索"){
		//alert("请输入要搜索的关键词!");
		return false;
	}
	return true;
}

//-----------------搜索下拉列表模拟--------------------//
function boxSelect(){
	var d = document,
		o = document.getElementById('Search-listbtn'),
		s = document.getElementById('Listtags');
	if(!o){ return false};
	if(!s){ return false};
	o.onclick=function(e){
  stopBubble(e);
		s.style.display = (s.style.display=="block")?"":"block";
	};
 	d.getElementsByTagName("html")[0].onclick = function(){
		s.style.display="";
	};
	for(i=0;i<s.getElementsByTagName("li").length;i++){
		s.getElementsByTagName("li")[i].onclick=function(){
			s.style.display="block";
			o.getElementsByTagName("h5")[0].innerHTML=this.childNodes[0].childNodes[0].nodeValue;
		}
	}

}
boxSelect();
function stopBubble(e) { //阻止冒泡
    if ( e && e.stopPropagation )
     e.stopPropagation();
    else
     window.event.cancelBubble = true;
}