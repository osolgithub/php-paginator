<?php
/*! 
 *\class OSOLUtils::Helpers::PHPPaginator
 * \brief Eases creation of pagination links
 * \details This comes in OSOLUtils\Helpers\PHPPaginator 
 *\par Usage:
 <pre>
 $otpPageNav = \OSOLUtils\Helpers\PHPPaginator::getInstance();
 $sql = "SELECT * FROM table";

 $recordsOfPage = $otpPageNav->fetch_records($sql);//returns the records as an array

 echo $otpPageNav->page_nav."<br />"//displays like 1,2,3,4
 echo $otpPageNav->display_rec_nums."<br />";//displays like 1 to 10 of 25
 </pre>
 */
namespace OSOLUtils\Helpers;
class PHPPaginator extends OSOLBaseParentClass
{
 var $lastReferedInstName="pn";// formerly varname
 var $rs=array();
 var $options=array("pagelinksperpage"=>20,"rows_per_page"=>5,"class"=>"");
 var $row_count=0;
 var $page_nav="";
 var $display_rec_nums="";
 var $fpnlLinks = "";
 var $script_uri = "";
 var $database = null;
 private $instancesOptions =  array();
 private static $inst =  null;
 
 protected function __construct()//,$options=array())
 {
   $arguments = func_get_args();
   $this->database = \OSOLUtils\Helpers\OSOLMySQL::getInstance(...$arguments/* $dbDetails */);
   $this->activateInst($this->lastReferedInstName);
   
 }//private function __construct()
 function activateInst($refName)
 {
  $this->lastReferedInstName=$refName;
  if(!isset($this->instancesOptions[$refName]))
   {
      $this->instancesOptions[$refName] = $this->options;
   }//if(!isset($this->instances[$refName]))
  return $this;
 }//function activateInst($refName)
 function setOptions($options)
 {
   foreach($options as $keyName => $keyVal)
   {
      $this->instancesOptions[$this->lastReferedInstName][$keyName] = $keyVal;
   }//foreach($options as $keyName => $keyVal)
  return $this;
 }//function setOptions($refName,$options)
 function getOptions($refName)
 {
    return $this->instancesOptions[$refName];
 }//function getOptions($refName)
 function fetch_records(...$bindparams)
 {
   $sql = $bindparams[0];
   
  //global $db;//$db is an instace of MYSQL class in the attacjhed db_class.php
  
  $dd_total_rows_var_name=$this->lastReferedInstName."_tot";
  if(isset($_GET[$dd_total_rows_var_name]) && $_GET[$dd_total_rows_var_name] != "")
  {
    $this->row_count = $_GET[$dd_total_rows_var_name];
  }
  else
  {
    
    //$count_sql=preg_replace("/select (.+) from (.+)/i","select count(*) as tot from $2",$sql);

    // above regexp replace when there are multiple selects as in 
    //select * from `table1` where  field1InTable1 = ?   and  _id in (select `_id` from `tabl2` where `field1InTable2` = '2')  order by _id desc
    $splitSQLArray = preg_split("/from/i",$sql);
    $sqlBeforeFrom = $splitSQLArray[0];
    $splitSQLArray[0] = $replacesSQLBeforeFrom = preg_replace("/select (.+) /i","select count(*) as tot",$sqlBeforeFrom );
    $count_sql= join(" from",$splitSQLArray);



    if(count($bindparams) > 1)
    {
	  $types = $bindparams[1]; //...$bindparams
      $bindParams4Count = array_values($bindparams);
      $bindParams4Count[0] = $count_sql;
      //die(__FILE__ . " : " . __LINE__ . "<br />"  . "<pre>".print_r($bindParams4Count,true)."</pre>");
      /*$queriesRun = call_user_func_array(array($this->database, 'getReplacedSQLAdv'), $bindParams4Count);
                                 
      die(__FILE__ . " : " . __LINE__ . "<br />" .$queriesRun);*/
      //die(__FILE__ . " : " . __LINE__ . "<br />" . $queriesRun . "<pre>".print_r($bindParams4Count,true)."</pre>");
      //echo __FILE__. ":" . __LINE__ ."<pre>".$sql."</pre>";
      //echo __FILE__. ":" . __LINE__ ."<pre>"."/select (.+) from (.+)/i"."</pre>";
      //echo __FILE__. ":" . __LINE__ ."<pre>"."select count(*) as tot from $2"."</pre>";
      //echo __FILE__. ":" . __LINE__ ."<pre>".print_r($bindParams4Count,true)."</pre>";
      $count_rsSQL = call_user_func_array(array($this->database, 'getReplacedSQLAdv'), $bindParams4Count);
      
      
      $logMessage = "select_id_sql is \r\n " . $count_rsSQL;
      //\upkar\php\helpers\ClassLogHelper::doLog($logMessage);


      $count_rs = call_user_func_array(array($this->database, 'selectPS'), $bindParams4Count);
      //die(__FILE__ . " : " . __LINE__ . "<br />" . $queriesRun . "<pre>".print_r($count_rs,true)."</pre>");
    }
    else //if(count($bindparams) > 0)
    {
      $count_rs = $this->database->select_sql($count_sql);
    }//if(count($bindparams) > 0)
    $this->row_count = $count_rs[0]['tot'];
  }//if(isset($_GET[$dd_total_rows_var_name]) && $_GET[$dd_total_rows_var_name] != "")
  
  //$dd_total_rows_var_name=$this->lastReferedInstName."_tot";
  $dd_per_page_var_name=$this->lastReferedInstName."_per_page";
  $dd_page_num_var_name=$this->lastReferedInstName."_page_num";
  $pageNavOptions =  $this->getOptions($this->lastReferedInstName);
  if((!isset($_GET[$dd_per_page_var_name]))  || (isset($_GET[$dd_per_page_var_name]) && $_GET[$dd_per_page_var_name] !="all"))
  {
    $this->page_nav=$this->create_pagenav($this->row_count,$dd_total_rows_var_name,$dd_per_page_var_name,$dd_page_num_var_name,$pageNavOptions["class"]);
    $this->create_fpnlLinks($this->row_count,$dd_total_rows_var_name,$dd_per_page_var_name,$dd_page_num_var_name,$pageNavOptions["class"]);
    $this->display_rec_nums=$this->get_display_row_limit($dd_per_page_var_name,$dd_page_num_var_name).$this->row_count; 		  
    $sql.=" ".$this->get_row_limit($dd_per_page_var_name,$dd_page_num_var_name); 
  }
  else
  {
    
    $this->page_nav= "";
    $this->display_rec_nums= "Total {$this->row_count} items"; 		  
    //$sql.=" ".$this->get_row_limit($dd_per_page_var_name,$dd_page_num_var_name);

  }//(isset($_GET[$dd_per_page_var_name] && $_GET[$dd_per_page_var_name] !="all"))
  //echo $sql."<br />";
  //$this->rs=$this->database->select_sql($sql);  
  if(count($bindparams) > 1)
  {
    $bindparams[0] = $sql;
    /* $queriesRun = call_user_func_array(array($this->database, 'getReplacedSQLAdv'), $bindparams);                                 
    die(__FILE__ . " : " . __LINE__ . "<br />" .$queriesRun); */
    //$bindparams = $this->database->convertMultiLevelArray2Single( $bindparams);
    //echo "<pre>".print_r($bindparams,true)."</pre>";
    $this->rs = call_user_func_array(array($this->database, 'selectPS'), $bindparams);
  }
  else //if(count($bindparams) > 0)
  {
    $this->rs = $this->database->select_sql($sql);
  }//if(count($bindparams) > 0)
  return $this->rs;
		 
 }//function fetchrecords($sql)
 /* function get_row_limit($dd_per_page_var_name,$dd_page_num_var_name)
 {
     $pageNum = isset($_GET[$dd_page_num_var_name])?$_GET[$dd_page_num_var_name]:0;
     $perPage = isset($_GET[$dd_per_page_var_name])?$_GET[$dd_per_page_var_name]:$pageNavOptions['rows_per_page'];
     $rangeStart = $pageNum * $perPage;
     if($rangeStart == 0)$rangeStart = 1;
    return " limit ".$rangeStart.",".$perPage;
 }//function get_row_limit($dd_per_page_var_name,$dd_page_num_var_name) */
 function first_recno_for_pageno($numrows_var,$rows_per_page_var,$nav_var)
 {
  return ($_GET[$nav_var]*$_GET[$rows_per_page_var]);
 }
 
 function getInfoForPageNum($tot_rows,$numrows_var,$rows_per_page_var,$nav_var,$class)
 {
	 $infoForPageNum = new \stdClass();
  //die(__FILE__ . " : " . __LINE__ ."  in create_pagenav") ;
      $pageNavOptions =  $this->getOptions($this->lastReferedInstName);
         //set defaults if not declared aleady
		  if(!( isset($_GET[$rows_per_page_var]) && $_GET[$rows_per_page_var]>0)) $_GET[$rows_per_page_var]=$pageNavOptions['rows_per_page'];
		  if(!isset($_GET[$nav_var])) $_GET[$nav_var]=0;
		  if(!isset($_GET[$numrows_var])) $_GET[$numrows_var]=$tot_rows;
		 //set defaults if not declared aleady  ends here
     /* echo(
            __FILE__ . " : " . __LINE__ ."  in create_pagenav ".$_GET[$rows_per_page_var]."\r\n".
            __FILE__ . " : " . __LINE__ ."  in create_pagenav ".$_GET[$nav_var]."\r\n"
        ) ; */

      $maxpagelinks=$pageNavOptions['pagelinksperpage'];

      $infoForPageNum->currentPage = $_GET[$nav_var];
      $infoForPageNum->tot_pages=ceil($_GET[$numrows_var]/$_GET[$rows_per_page_var])-1;
      $infoForPageNum->firstpage=$_GET[$nav_var]>$maxpagelinks?$_GET[$nav_var]-$maxpagelinks:0;
      $infoForPageNum->lastpage=($infoForPageNum->tot_pages>=($_GET[$nav_var]+$maxpagelinks))?($_GET[$nav_var]+$maxpagelinks):$infoForPageNum->tot_pages;
	  //$tot_rows,$numrows_var,$rows_per_page_var,$nav_var,$class=""
      $infoForPageNum->tot_rows = $tot_rows;
      $infoForPageNum->numrows_var = $numrows_var;
      $infoForPageNum->rows_per_page_var = $rows_per_page_var;
      $infoForPageNum->nav_var = $nav_var;
      $infoForPageNum->className = $class;
     /* die(
      __FILE__ . " : " . __LINE__ ."  in create_pagenav ".$tot_pages."\r\n".
      __FILE__ . " : " . __LINE__ ."  in create_pagenav ".$firstpage."\r\n".
      __FILE__ . " : " . __LINE__ ."  in create_pagenav ".$lastpage."\r\n"
      ) ; */
      
      
      $infoForPageNum->skippedQString = $this->sp_skip_param(array($numrows_var,$nav_var,$rows_per_page_var));
      //die(__FILE__ . " : " . __LINE__. " <br />".$skippedQString);
      
      $infoForPageNum->script_uri = $this->getScriptURI();//(isset($pageNavOptions['script_uri']) && $pageNavOptions['script_uri'] != "")?$pageNavOptions['script_uri']:$_SERVER['SCRIPT_URI'];
	  return $infoForPageNum;
 }//function getInfoForPageNum($tot_rows,$numrows_var,$rows_per_page_var,$nav_var)
 function create_fpnlLinks($tot_rows,$numrows_var,$rows_per_page_var,$nav_var,$class="")
 {
     $infoForPageNum = $this->getInfoForPageNum($tot_rows,$numrows_var,$rows_per_page_var,$nav_var,$class);
	 // first page
	  $fpnlPageNum = 0;
	  $linkText = "&lt;&lt;";
	  $firstPageLink = "&lt;&lt;";
	  $previousPageLink = "&lt;";
	  $nextPageLink = "&gt;";
	  $lastPageLink = "&gt;&gt;";
	  if($infoForPageNum->currentPage > 0)
	  {
		  $firstPageLink  = $this->getPageLink($infoForPageNum, 0, $firstPageLink);
		  $previousPageLink  = $this->getPageLink($infoForPageNum, ($infoForPageNum->currentPage - 1),$previousPageLink );
	  }//if($infoForPageNum->currentPage > 0)
	  if($infoForPageNum->currentPage < $infoForPageNum->tot_pages)
	  {
		  $nextPageLink = $this->getPageLink($infoForPageNum, ($infoForPageNum->currentPage + 1), $nextPageLink );
		  $lastPageLink = $this->getPageLink($infoForPageNum, $infoForPageNum->tot_pages, $lastPageLink );
	  }//if($infoForPageNum->currentPage < $infoForPageNum->tot_pages)
      
	  $this->fpnlLinks = $firstPageLink . "&nbsp;&nbsp;&nbsp;" . $previousPageLink . "&nbsp;&nbsp;&nbsp;" . $nextPageLink . "&nbsp;&nbsp;&nbsp;". $lastPageLink;
 }
 function getPageLink($infoForPageNum, $pageNum, $linkText)
 {
	 return  " <a class=\"". $infoForPageNum->className . "\"  href=\"{$infoForPageNum->script_uri}?".//{$_SERVER['SCRIPT_URI']}
                            $infoForPageNum->skippedQString.
                            (($infoForPageNum->skippedQString!="")?"&":"").
                            $infoForPageNum->rows_per_page_var."=".$_GET[$infoForPageNum->rows_per_page_var].
                            "&".$infoForPageNum->numrows_var."=".$_GET[$infoForPageNum->numrows_var].
                            "&".$infoForPageNum->nav_var."=".$pageNum.
                            "\">".$linkText."</a>";
 }//function getPageLink($infoForPageNum, $pageNum, $linkText)
 function create_pagenav($tot_rows,$numrows_var,$rows_per_page_var,$nav_var,$class="")
 {
     $infoForPageNum = $this->getInfoForPageNum($tot_rows,$numrows_var,$rows_per_page_var,$nav_var,$class);
	  
	  $page_nav_num = "";				
      for($i=($infoForPageNum->firstpage);$i<=$infoForPageNum->lastpage;$i++)
      {
		  if($i == $infoForPageNum->currentPage)
		  {
			  $page_nav_num .= "&nbsp;" . ($i+1);
		  }
		  else
		  {
            $page_nav_num .= "&nbsp;" . "<a class=\"$class\"  href=\"{$infoForPageNum->script_uri}?".//{$_SERVER['SCRIPT_URI']}
                            $infoForPageNum->skippedQString.
                            (($infoForPageNum->skippedQString!="")?"&":"").
                            $rows_per_page_var."=".$_GET[$rows_per_page_var].
                            "&".$numrows_var."=".$_GET[$numrows_var].
                            "&".$nav_var."=".$i.
                            "\">".($i+1)."</a>";
		  }//if($i == $infoForPageNum->currentPage)
      
      }//for($i=0;$i<=$numrows;$i++)
      if($infoForPageNum->firstpage!=0) $page_nav_num = "..." .  $page_nav_num;
      if($infoForPageNum->lastpage!=$infoForPageNum->tot_pages) $page_nav_num="$page_nav_num...";
	  
      return $page_nav_num;
 
 }//function pagenav($tot_rows,$numrows_var,$rows_per_page_var,$nav_var,$class="")
 private function getScriptURI()
 {
  $pageNavOptions =  $this->getOptions($this->lastReferedInstName);
  $script_uri = (isset($pageNavOptions['script_uri']) && $pageNavOptions['script_uri'] != "")?$pageNavOptions['script_uri']:$_SERVER['SCRIPT_NAME'];
  return $script_uri;
 }
 private function sp_skip_param($params2Skip)
 {
    $skippedQueryString = "";
    $splittedQStringArray = preg_split("/&/",$_SERVER['QUERY_STRING']);
    foreach($splittedQStringArray as $splitVals)
    {
      $split2KeyVarArray = preg_split("/=/",$splitVals);
      if(!in_array($split2KeyVarArray[0],$params2Skip))
      {
        $skippedQueryString .= $splitVals."&";
      }//if(!in_array($split2KeyVarArray[0],$params2Skip))
    }//foreach($splittedQStringArray as $splitVals)
    $skippedQueryString = $skippedQueryString !=""?substr($skippedQueryString,0,-1):"";
    return $skippedQueryString;
    
 }//private function sp_skip_param($params2Skip)
 function get_display_row_limit($rows_per_page_var,$nav_var)
 {
        $pageNavOptions =  $this->getOptions($this->lastReferedInstName);
        $maxRows = (!($_GET[$rows_per_page_var]>0))?$pageNavOptions[rows_per_page]:$_GET[$rows_per_page_var];
        $pageNum = (!isset($_GET[$nav_var]))?0:$_GET[$nav_var];
        $startRow = ($pageNum * $maxRows)+1;
		$maxRows=$startRow + $maxRows-1;
		$maxRows=$maxRows<$this->row_count?$maxRows:$this->row_count;
		$limit="$startRow  to $maxRows of ";	
		return $limit;
 }//function get_row_limit($rows_per_page_var,$nav_var)
 function get_row_limit($rows_per_page_var,$nav_var)
 {
        $pageNavOptions =  $this->getOptions($this->lastReferedInstName);
        $maxRows = (!($_GET[$rows_per_page_var]>0))?$pageNavOptions[rows_per_page]:$_GET[$rows_per_page_var];
        $pageNum = (!isset($_GET[$nav_var]))?0:$_GET[$nav_var];
        $startRow = $pageNum * $maxRows;
		$limit="limit $startRow ,$maxRows";	
		return $limit;
 }//function get_row_limit($rows_per_page_var,$nav_var)

 function getDropDownHTML()
 {
    $script_uri = $this->getScriptURI();
    $pageNavOptions =  $this->getOptions($this->lastReferedInstName);
    $rows_per_page_var = $this->lastReferedInstName."_per_page";
    $nav_var = $this->lastReferedInstName."_page_num";
    $numrows_var = $this->lastReferedInstName."_tot";
    if(!isset($_GET[$numrows_var]))$_GET[$numrows_var] = $this->row_count;
    $skippedQString = $this->sp_skip_param(array($numrows_var,$nav_var,$rows_per_page_var));
    $qString = $skippedQString . 
              (($skippedQString!="")?"&":"").
              $numrows_var."=".$_GET[$numrows_var].
                        "&".$nav_var."=0".
                        "&".$rows_per_page_var."=";//.$_GET[$rows_per_page_var].;
    $newURI = $script_uri."?".$qString;
    $dropdownFunctionName = $this->lastReferedInstName."_onRangeChanged()";
    $selectRangeId = $this->lastReferedInstName . "_dropdown";
    $selectedRangeVarName = $this->lastReferedInstName."_selectedRange";
    $dropdownHTML = "<select style=\"display:inline\" id=\"{$selectRangeId}\" onchange=\"{$dropdownFunctionName}\">\r\n";
    $dropdownHTML .= "<option value=\"\">Select</a>\r\n";
    $dropdownHTML .= "<option value=\"all\">All</a>\r\n";
    $dropdownHTML .= "<option value=\"10\">10</a>\r\n";
    $dropdownHTML .= "<option value=\"25\">25</a>\r\n";
    $dropdownHTML .= "<option value=\"50\">50</a>\r\n";
    $dropdownHTML .= "</select>\r\n";

    $dropdownScript = "";//"\r\n<script>\r\n";
    $dropdownScript .= "var newPageNavURI = '{$newURI}';\r\n";
    $dropdownScript .= "function " .$dropdownFunctionName."{\r\n" ;
    $dropdownScript .=    "var {$selectedRangeVarName} = document.getElementById('{$selectRangeId}').value;\r\n";
    $dropdownScript .=    " window.location.assign(newPageNavURI +{$selectedRangeVarName}) \r\n";
    $dropdownScript .= "}\r\n";
    //$dropdownScript .= "</script>\r\n";
    return  array("js" => $dropdownScript, "html" => $dropdownHTML) ;
 }//function getDropDownHTML()
}//class pagenav
?>