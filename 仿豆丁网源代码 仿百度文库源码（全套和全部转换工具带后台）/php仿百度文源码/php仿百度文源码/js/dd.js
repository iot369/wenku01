
formobj = document.getElementById('form2');
MonHead = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

var y = new Date().getFullYear();

formobj.YYYY.options.length=0; 
for (var i = (y-50); i < (y+1); i++) //以今年为准，前30年，后30年
{
formobj.YYYY.options.add(new Option(i,i)); 
}

//赋月份的下拉框
formobj.MM.options.length=0; 
for (var i = 1; i < 13; i++)
{
formobj.MM.options.add(new Option(i,i)); 
}

formobj.YYYY.value = y;

formobj.MM.value = new Date().getMonth() + 1;
var n = MonHead[new Date().getMonth()];
if (new Date().getMonth() ==1 && IsPinYear(YYYYvalue)) n++;
writeDay(n); //赋日期下拉框


formobj.DD.value = new Date().getDate();

function YYYYMM(str) //年发生变化时日期发生变化(主要是判断闰平年)
{
	var MMvalue = formobj.MM.options[formobj.MM.selectedIndex].value;
	if (MMvalue == ""){DD.outerHTML = strDD; return;}
	var n = MonHead[MMvalue - 1];
	if (MMvalue ==2 && IsPinYear(str)) n++;
	writeDay(n)
}

function MMDD(str) //月发生变化时日期联动
{
	var YYYYvalue = formobj.YYYY.options[formobj.YYYY.selectedIndex].value;
	if (str == ""){DD.outerHTML = strDD; return;}
	var n = MonHead[str - 1];
	if (str ==2 && IsPinYear(YYYYvalue)) n++;
	writeDay(n)
}

function writeDay(n) //据条件写日期的下拉框
{
	formobj.DD.options.length=0; 
	for (var i=1; i<(n+1); i++)
	formobj.DD.options.add(new Option(i,i)); 
}

function IsPinYear(year)//判断是否闰平年
{
	return(0 == year%4 && (year%100 !=0 || year%400 == 0))
}
