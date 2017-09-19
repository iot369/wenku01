// JavaScript Document

function xy1( o, mark )
{
	if( mark=='Tab' || mark=='Tab2')
	{
		for(var i=1;i<=2;i++)
		{  
		  var divx = document.getElementById(mark + '_' + i + '_overlay' );
		  var divy = document.getElementById(mark + '_' + i );
		  divx.style.display="none";
		  divy.style.background='url(images/index/righttable_2.gif)';
		}
		var x = document.getElementById( o.id + '_overlay');
		var y = document.getElementById( o.id);
		x.style.display="block";
		y.style.background='url(images/index/righttable_1.gif)';
	}	
	if( mark=='Tab3' )
	{
		for(var i=1;i<=3;i++)
		{  
		  var divx = document.getElementById(mark + '_' + i + '_overlay' );
		  var divy = document.getElementById(mark + '_' + i );
		  divx.style.display="none";
		  divy.style.background='url(images/index/righttable_4.gif)';
		}
		var x = document.getElementById( o.id + '_overlay');
		var y = document.getElementById( o.id);
		x.style.display="block";
		y.style.background='url(images/index/righttable_3.gif)';
	}	
	
	if( mark=='Tab4' )
	{
		for(var i=1;i<=2;i++)
		{  
		  var divx = document.getElementById(mark + '_' + i + '_overlay' );
		  var divy = document.getElementById(mark + '_' + i );
		  divx.style.display="none";
		  divy.style.background='url(images/index/table_2.gif)';
		}
		var x = document.getElementById( o.id + '_overlay');
		var y = document.getElementById( o.id);
		x.style.display="block";
		y.style.background='url(images/index/table_1.gif)';
	}		

	if( mark=='Tab5' )
	{
		for(var i=1;i<=2;i++)
		{  
		  var divx = document.getElementById(mark + '_' + i + '_overlay' );
		  var divy = document.getElementById(mark + '_' + i );
		  divx.style.display="none";
		  divy.style.color='#000000';
		}
		var x = document.getElementById( o.id + '_overlay');
		var y = document.getElementById( o.id);
		x.style.display="block";
		y.style.color='red';
	}

	if( mark=='Tab6' )
	{
		for(var i=1;i<=3;i++)
		{  
		  var divx = document.getElementById(mark + '_' + i + '_overlay' );
		  var divy = document.getElementById(mark + '_' + i );
		  divx.style.display="none";
		  divy.style.color='#000000';
		}
		var x = document.getElementById( o.id + '_overlay');
		var y = document.getElementById( o.id);
		x.style.display="block";
		y.style.color='red';
	}



}
