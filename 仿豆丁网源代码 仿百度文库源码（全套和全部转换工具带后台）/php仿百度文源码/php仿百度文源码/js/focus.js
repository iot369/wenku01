// JavaScript Document
// JavaScript Document

 <!--
 var focus_width=592
  var focus_height=180
 var text_height=2   
 var swf_height = focus_height+text_height //相加之和最好是偶数,否则数字会出现模糊失真的问题
 
 var pics='images/bannar1.jpg|images/bannar2.jpg|images/bannar3.jpg|images/bannar4.jpg'//定义图片
 var links=' | | | '//定义链接

var texts=' | | | '//定义下面输出
 
 document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="'+ focus_width +'" height="'+ swf_height +'">');
 document.write('<param name="allowScriptAccess" value="sameDomain"><param name="movie" value="images/pixviewer.swf"><param name=wmode value=transparent><param name="quality" value="high">');
 document.write('<param name="menu" value="false"><param name=wmode value="opaque">');
 document.write('<param name="FlashVars" value="pics='+pics+'&links='+links+'&texts='+texts+'&borderwidth='+focus_width+'&borderheight='+focus_height+'&textheight='+text_height+'">');
 document.write('<embed src="images/pixviewer.swf" wmode="opaque" FlashVars="pics='+pics+'&links='+links+'&texts='+texts+'&borderwidth='+focus_width+'&borderheight='+focus_height+'&textheight='+text_height+'" menu="false" bgcolor="#DADADA" quality="high" width="'+ focus_width +'" height="'+ swf_height +'" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');  document.write('</object>');
 
 //-->
						                                                              

                                                             
						 




