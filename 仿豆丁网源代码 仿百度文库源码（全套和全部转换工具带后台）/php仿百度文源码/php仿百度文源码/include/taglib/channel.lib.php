<?php
function lib_channel(&$ctag,&$refObj)
{
    global $_sys_globals,$envs,$dsql;
    $attlist = "typeid|0,reid|0,row|100,col|1,type|son,currentstyle|";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);
    $innertext = $ctag->GetInnerText();
    if(empty($typeid) && $envs['typeid']!=0)
    {
        $typeid = $envs['typeid'];
        $reid = $envs['reid'];
    }else{
        $reid=0;
    }

    if($type==''||$type=='sun') $type="son";
    if($innertext=='') $innertext = GetSysTemplets("channel_list.htm");

    if($reid==0 && $typeid>0)
    {
        $dbrow = $dsql->GetOne("Select reid From #@__arctype where id='$typeid' ");
        if(is_array($dbrow)) $reid = $dbrow['reid'];
    }

    $likeType = '';
    if($type=='top')
    {
        $sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath,description
          From #@__arctype where reid=0 And ishidden<>1 order by sortrank asc limit 0,$row";
    }
    else if($type=="son")
    {
        //if($_sys_globals['typeid']>0) $typeid = $_sys_globals['typeid'];
        if($typeid==0) {
            return '';
        }
        $sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath,description
          From #@__arctype where reid='$typeid' And ishidden<>1 order by sortrank asc limit 0,$row";
    }
    else if($type=="self")
    {
        if($reid==0) {
            return '';
        }
        $sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath,description
            From `#@__arctype` where reid='$reid' And ishidden<>1 order by sortrank asc limit 0,$row";
    }
    //And id<>'$typeid'
    $dtp2 = new DedeTagParse();
    $dtp2->SetNameSpace("field","[","]");
    $dtp2->LoadSource($innertext);
    $dsql2 = clone $dsql;
    $dsql->SetQuery($sql);
    $dsql->Execute();

    $line = $row;
    $GLOBALS['autoindex'] = 0;
    for($i=0;$i < $line;$i++)
    {
        if($col>1) $likeType .= "<dl>\r\n";
        for($j=0;$j<$col;$j++)
        {
            if($col>1) $likeType .= "<dd>\r\n";
            if($row=$dsql->GetArray())
            {
                if($type=='self' && $row['id']==$typeid) //处理同级栏目中，当前栏目的样式
                {
                    if($currentstyle!='')
                    {
                        $linkOkstr = $currentstyle;
                        $row['typelink'] = GetOneTypeUrlA($row);
                        $linkOkstr = str_replace("~typelink~",$row['typelink'],$linkOkstr);
                        $linkOkstr = str_replace("~typename~",$row['typename'],$linkOkstr);
                        $likeType .= $linkOkstr;
                    }
                }else
                {
                    $row['typelink'] = $row['typeurl'] = GetOneTypeUrlA($row);
                    if(is_array($dtp2->CTags))
                    {
                        foreach($dtp2->CTags as $tagid=>$ctag){
                            if(isset($row[$ctag->GetName()]))
                            {
                                $dtp2->Assign($tagid,$row[$ctag->GetName()]);
                            }
                            elseif (preg_match('/^sonchannel[0-9]*$/',$ctag->GetName()))
                            {
                                $dtp2->Assign($tagid,lib_channel_son($ctag,$row['id'],$dsql2));
                            }
                        }
                    }
                    $likeType .= $dtp2->GetResult();
                }
            }
            if($col>1) $likeType .= "</dd>\r\n";
            $GLOBALS['autoindex']++;
        }//Loop Col
        if($col>1)
        {
            $i += $col - 1;
            $likeType .= "    </dl>\r\n";
        }
    }//Loop for $i
    reset($dsql2);
    $dsql->FreeResult();
    return $likeType;
}
function lib_channel_son($ctag,$typeid = 0,$dsql2)
{
    $attlist = "row|100,col|1,currentstyle|";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);
    $innertext = $ctag->GetInnerText();
    $dsql3 = clone $dsql2;
    $likeType = '';

    //if($_sys_globals['typeid']>0) $typeid = $_sys_globals['typeid'];
    if($typeid==0) {
        return '';
    }
    $sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath,description
          From #@__arctype where reid='$typeid' And ishidden<>1 order by sortrank asc limit 0,$row";
    //And id<>'$typeid'
    $dtp2 = new DedeTagParse();
    $dtp2->SetNameSpace("field","[","]");
    $dtp2->LoadSource($innertext);
    $dsql2->SetQuery($sql);
    $dsql2->Execute();
    $line = $row;

    for($i=0;$i < $line;$i++)
    {
        if($col>1) $likeType .= "<dl>\r\n";
        for($j=0;$j<$col;$j++)
        {
            if($col>1) $likeType .= "<dd>\r\n";
            if($row=$dsql2->GetArray())
            {
                $row['typelink'] = $row['typeurl'] = GetOneTypeUrlA($row);
                if(is_array($dtp2->CTags))
                {
                    foreach($dtp2->CTags as $tagid=>$ctag){
                        if(isset($row[$ctag->GetName()]))
                        {
                            $dtp2->Assign($tagid,$row[$ctag->GetName()]);
                        }
                        elseif (preg_match('/^sonchannel[0-9]*$/',$ctag->GetName()))
                        {
                            $dtp2->Assign($tagid,lib_channel_son($ctag,$row['id'],$dsql3));
                        }
                    }
                }
                $likeType .= $dtp2->GetResult();
            }
            if($col>1) $likeType .= "</dd>\r\n";
        }//Loop Col
        if($col>1)
        {
            $i += $col - 1;
            $likeType .= "    </dl>\r\n";
        }
    }//Loop for $i
    reset($dsql3);
    $dsql2->FreeResult();
    return $likeType;
}
?>