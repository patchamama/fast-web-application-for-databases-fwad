<?php
// File           GiveMeLst.php / Phyllacanthus
// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//
require('./inc/script_start.inc.php');
require('head.html');
//echo html_body();

?>
<script language="JavaScript" type="text/JavaScript">
<!--

function openWindowSql() 
{

		if (document.Form.sql.value.length>0)
			{
			theURL = 'TableView.php?sql='+document.Form.sql.value+'&mod='+document.Form.mod.value;
			window.open(theURL);
			}

}


//-->
</script>

<?php
	
	$aResult = $xPath->evaluate("/Main/queries/query");

	if (isset($HTTP_PARAM_VARS['sqlid']))
		{
		$vsqlid = (int)$HTTP_PARAM_VARS['sqlid']+0;
		}
		
	if (count($aResult)>0)
		{
		echo '<form name="Form" method="get" action="PreQuery.php">';
		echo 'Consultas predise&ntilde;adas: ';
		echo '<select name="sqlid" size="1">';
		echo '<option value=""></option>';	
		echo '<option value="0"></option>';
		for ($t = 0; $t < count($aResult); $t++)
			{
			//echo '<option value="'.htmlentities(($xPath->getData($aResult[$t]))).'" >'.$xPath->getAttributes($aResult[$t], 'name').'</option>';			   
			if ($t==$vsqlid)
				{
				if (isset($HTTP_PARAM_VARS['doit']))
					{
					$HTTP_PARAM_VARS['sql'] = $xPath->getData($aResult[$t]);
					}
				echo '<option value="'.$t.'" selected>'.$xPath->getAttributes($aResult[$t], 'name').'</option>';			   
				}
			else	{
				echo '<option value="'.$t.'" >'.$xPath->getAttributes($aResult[$t], 'name').'</option>';			   
				}
			}
		echo '</select>';
		//echo '<input name="doit" onClick="javascript: openWindowSql();" type="button" value="Ejecutar" >';
		echo '<input name="doit" type="submit" value="Ejecutar" >';
		echo '<input name="mod" type="hidden" value="'.$HTTP_PARAM_VARS['mod'].'">';
		echo '</form>';
		}
		
	if (isset($HTTP_PARAM_VARS['sql']))
		{
		include_once('./lib/adodb/tohtml.inc.php');
		
		//fields need to work in the sql query and combobox...
		$vSql = urldecode(stripcslashes($HTTP_PARAM_VARS['sql']));  
		
		echo '<form name="Form">';
		
		//Query...
		//$rec = $dbhandle->Execute($vSql);
		//[orderbygo] => Ordernar por:
    		//[orderby] => Familia
    		
    		$temp = '';
    		$Sql = $vSql;
    		$SqlOrderBy = array();
    		$SqlHaving = '';
    		$SqlGroupBy = array();
    		$SqlWhere = '';
    		$SqlFrom = '';
    		$SqlSelect = array();
    		if (@eregi ('(.*) union (.*)', $vSql, $match))
    			{
    			//nothing to do...
    			}
    		else	{
			if (@eregi ('(.*) order by (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$temp = $match[2];
				while (@eregi ('(.*),(.*)', $temp, $match))
					{
					$temp = trim($match[1]);
					$SqlOrderBy[] = trim($match[2]);
					}
				$SqlOrderBy[] = $temp;
				$SqlOrderBy = array_reverse($SqlOrderBy); 
				}
			
			if (@eregi ('(.*) having (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$SqlHaving = $match[2];
				}
				
			if (@eregi ('(.*) group by (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$temp = $match[2];
				while (@eregi ('(.*),(.*)', $temp, $match))
					{
					$temp = trim($match[1]);
					$SqlGroupBy[] = trim($match[2]);
					}
				$SqlGroupBy[] = $temp;
				$SqlGroupBy = array_reverse($SqlGroupBy); 
				}
				
			if (@eregi ('(.*) where (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$SqlWhere = $match[2];
				}
				
			if (@eregi ('(.*) from (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$SqlFrom = $match[2];
				}
			
			if (@eregi ('(.*)select (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$temp = $match[2];
				while (@eregi ('(.*),(.*)', $temp, $match))
					{
					$temp = trim($match[1]);
					$SqlSelect[] = trim($match[2]);
					}
				$SqlSelect[] = $temp;
				$SqlSelect = array_reverse($SqlSelect); 
				}
			
			}
    		
    		
    		
    		if ( ((isset($HTTP_PARAM_VARS['orderasc'])) or
    		      (isset($HTTP_PARAM_VARS['orderdesc']))) 
    	              and
    		     ((isset($HTTP_PARAM_VARS['orderby'])) and
    		     (!empty($HTTP_PARAM_VARS['orderby']))) )
    		     {
    		     $torder = split( ":", $HTTP_PARAM_VARS['orderby']);
    		     $SqlOrderBy = array();
    		     
    		     
    		     for ($aa=0; $aa<count($SqlSelect); $aa++)
    		     	{
    		     	if (strpos($SqlSelect[$aa],$torder[1])>0)
    		     		{
    		     		$SqlOrderBy[0] = ($aa+1).' ';
    		     		$SqlOrderBy[0] .=  (isset($HTTP_PARAM_VARS['orderasc']))? 'ASC' : 'DESC';
    		     		}
    		     	}
    		     if (count($SqlOrderBy)==0)
    		     	{
    		     	$trec = $dbhandle->SelectLimit($vSql,1);
			if ($trec === FALSE) 
				{
				$error .= $dbhandle->ErrorMsg();
				}		
			else	{
				for ($i=0, $max=$trec->FieldCount(); ($i < $max) and (count($SqlOrderBy)==0); $i++) 
					{
					$fld = $trec->FetchField($i);
					if (($fld->name==$torder[1]) )
						{
						$SqlOrderBy[0] = ($torder[0]+1).' ';
						$SqlOrderBy[0] .=  (isset($HTTP_PARAM_VARS['orderasc']))? 'ASC' : 'DESC';
						}
					}
				}
				
			}
		     if (count($SqlOrderBy)==0)
			{
    		     	$vf = $syntax['field'];
    		     	$torder = $torder[1];
    		     	$torder = ereg_replace('#1',$torder, $vf);
    		     	$SqlOrderBy[0] = $torder.' ';
    		     	$SqlOrderBy[0] .=  (isset($HTTP_PARAM_VARS['orderasc']))? 'ASC' : 'DESC';
    		     	}
    		     
    		    
    		     }
    		     
    		     
		if ( (isset($HTTP_PARAM_VARS['groupbygo'])) 
    		      and
    		     ((isset($HTTP_PARAM_VARS['groupby'])) and
    		     (!empty($HTTP_PARAM_VARS['groupby']))) )
    		     {
    		     $SqlOrderBy = array();
    		     $SqlGroupBy = array();
    		     $SqlHaving = '';
    		     $SqlSelect = array();
    		     $tgroup = $HTTP_PARAM_VARS['groupby'];
    		     
			$vf = $syntax['field'];
			$tgroup = ereg_replace('#1',$tgroup, $vf);
			$SqlGroupBy[0] = $tgroup;
			$SqlSelect[0] = $tgroup;
			$SqlSelect[1] = 'count(*)';
			
			
		     }    		    
    		
    		if (count($SqlSelect)>0)
    			{
    			$vSql = 'select ';
    			for ($aa=0; $aa<count($SqlSelect); $aa++)
    		     		{
    		     		$vSql .= ($aa==0)? '': ', ';
    		     		$vSql .= $SqlSelect[$aa];
    		     		}
    		     	$vSql .= ' from '.$SqlFrom;
    		     	if (!empty($SqlWhere))
    		     		{
    		     		$vSql .= ' where '.$SqlWhere;
    		     		}
    		     	if (count($SqlGroupBy)>0)
    		     		{
    		     		$vSql .= ' group by ';
    		     		for ($aa=0; $aa<count($SqlGroupBy); $aa++)
    		     			{
    		     			$vSql .= ($aa==0)? '': ', ';
    		     			$vSql .= $SqlGroupBy[$aa];
    		     			}
    		     		}
    		     	if (!empty($SqlHaving))
				{
				$vSql .= ' having '.$SqlHaving;
    		     		}
    		     	if (count($SqlOrderBy)>0)
				{
				$vSql .= ' order by ';
				for ($aa=0; $aa<count($SqlOrderBy); $aa++)
					{
					$vSql .= ($aa==0)? '': ', ';
					$vSql .= $SqlOrderBy[$aa];
					}
    		     		}
    			}
    		echo '<hr>';
    		echo $vSql;
    		
    		
    		$vpos = 0;
		$vposMsg = '';
		if ((isset($HTTP_GET_VARS['pos'])) )
			{
			$vpos = (int)$HTTP_GET_VARS['pos'];
			$vpos = $vpos + 0;
			$vposi = ($vpos-100<=0) ? 0: $vpos-100;
			if  ($vpos>0)
				{
				$vposMsg = '<a href="'.$vpath.'/PreQuery.php?pos='.($vposi).'&mod='.$HTTP_GET_VARS['mod'].'&sql='.htmlentities(urlencode($vSql)).'">      &lt;&lt;</a>';
				}
			else	{
				$vposMsg = '      &lt;&lt;';
				}		
			}
		else	{
			$vposMsg = '      &lt;&lt;';
			}

		$vposMsg .= '<input name="pos" size="6" type="text" value="'.$vpos.'" onChange="javascript: document.Form.submit();">';
		echo '<input name="mod" size="6" type="hidden" value="'.$HTTP_GET_VARS['mod'].'">';
		$vposMsg .= '<a href="'.$vpath.'/PreQuery.php?pos='.($vpos+100).'&mod='.$HTTP_GET_VARS['mod'].'&sql='.htmlentities(urlencode($vSql)).'"> &gt;&gt;</a>';	
    		echo '<input name="sql" size="6" type="hidden" value="'.htmlentities(urlencode($vSql)).'">';
    		
		$rec = $dbhandle->SelectLimit($vSql,100,$vpos);
		if ($rec === FALSE) 
			{
			$error .= $dbhandle->ErrorMsg();
			}		
		else	{
			echo '<br><hr>';
			
			echo 'Ordernar por:&nbsp;<select name="orderby" size="1">';
			echo '<option value="" ></option>';			   				
			for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) 
				{
				$fld = $rec->FetchField($i);
				$type = $rec->MetaType($fld->type);
				echo '<option value="'.($i).':'.htmlentities($fld->name).'" >'.$fld->name.'</option>';			   				
				}
			echo '</select>';
			echo  '<input name="orderasc" type="submit" value="ASC" >';
			echo  '<input name="orderdesc" type="submit" value="DESC" >';
			
			if (@eregi ('(.*) from (.*)', $vSql, $match))
				{
				$tSql = 'select * from '.$SqlFrom;
				//if (!empty($SqlWhere))
				//	{
				//	$tSql .= ' where '.$SqlWhere;
    		     		//	}
				$trec = $dbhandle->SelectLimit($tSql,1);
				if ($trec === FALSE) 
					{
					$error .= $dbhandle->ErrorMsg();
					}		
				else	{
					echo  '&nbsp;&nbsp;<input name="groupbygo" type="submit" value="Agrupar por:" >';
					echo  '&nbsp;<select name="groupby" size="1">';
					echo  '<option value="" ></option>';			   				
					for ($i=0, $max=$trec->FieldCount(); $i < $max; $i++) 
						{
						$fld = $trec->FetchField($i);
						$type = $trec->MetaType($fld->type);
						echo '<option value="'.htmlentities($fld->name).'" >'.$fld->name.'</option>';			   				
						}
					echo '</select>';
					}
				}
			
			
			echo '<br>'.$vposMsg;
			echo '<br><hr>';
			echo rs2html($rec,true,false,true,false);
			}
		echo  '<input name="Submit" type="submit" onClick="window.close()" value="Cerrar" >';
		echo "</form>";		
		}
	
require('./inc/script_end.inc.php');
?>
