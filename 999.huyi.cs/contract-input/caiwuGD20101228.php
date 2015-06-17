<? 
header("content-type:text/html; charset=utf-8");
include("conn.php");
require_once("class/mysqldb.inc.php");

session_start();
if ($ses_PersonnelID==""){
	header("location: ../index.php");
}

//是否管理员 /陈子伟
if(in_array($ses_ID, array('122', '418837'))){
	$isManager = true;
}else{
	$isManager = false;
}

$db=new mysqldb();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>审核合同 完善财务 注册入库 生成工单</title>
<script language="javascript" type="text/javascript">
function showCont(tar){
	if(document.getElementById(tar).style.display=="none"){
		document.getElementById(tar).style.display="";
	}else{
		document.getElementById(tar).style.display="none";
	}
}
function isOK(ok,msg,toUrl){
var bln=false;
if(ok){
	bln =true;
}else{
	bln = window.confirm(msg);
}
if(bln){
		window.location=toUrl;
}
}
function isSH(types,msg,VcontractID){
toUrl="caiwu-toShenhe.php?VcontractID="+VcontractID+"&types="+types;
bln = window.confirm(msg);
if(bln){
	window.location.href=toUrl;	
	window.location.reload();
	}
}
/*
function isSH(msg,contractNO){
toUrl="A-toShenhe.php?contractNO="+contractNO;
bln = window.confirm(msg);
if(bln){
	location=toUrl;	
	}
}*/

var orderType="s1";
function  searchShow(tar){
	orderType=tar;
	if(orderType=='s1'){
		document.getElementById("search1").style.display="";
		document.getElementById("search2").style.display="none";
	}else{
		document.getElementById("search1").style.display="none";
		document.getElementById("search2").style.display="";
	}
}

function checkSearch(){
	/*
	if(orderType=='s1'){
		if(document.getElementById("keys").value==""){
			alert("请输入搜索内容!");
			document.getElementById("keys").focus();
			return false;
		}
	}
	*/

	if(orderType=='s2'){
		if(document.getElementById("time1").value==""){
			alert("请输入开始时间!");
			document.getElementById("time1").focus();
			return false;
		}
			if(document.getElementById("time2").value==""){
			alert("请输入截止时间!");
			document.getElementById("time2").focus();
			return false;
		}
	}

}
</script>
<script language="javascript" type="text/javascript" src="/js/Calendar.js"></script>
<link href="caiwu.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="pading00">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" id="table17" style="border-collapse: collapse">
    <tr>
      <td width="1%"><img border="0" src="images/left_boon_bg_01.jpg" width="37" height="40" /></td>
      <td width="98%" valign="top" background="images/left_boon_bg_02.jpg" class="font-Title" >添加财务实收，产品入库，生成工单</td>
      <td width="1%"><img border="0" src="images/left_boon_bg_03.jpg" width="7" height="40" /></td>
    </tr>
  </table>
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC" class="margin-top">
    <form method="post" onsubmit="return checkSearch();">
      <tr>
        <th height="20" align="left" bgcolor="#ECF1F4" class="font3">定义合同搜索</th>      </tr>
      <tr>
        <td bgcolor="#FFFFFF"><strong>搜索条件</strong>：<label>
            <input name="orders" type="radio" value="orders0" checked="checked" onclick="searchShow('s1')"/>
            所有合同
        </label>
          <label>
            <input name="orders" type="radio" value="orders1"  onclick="searchShow('s1')"/>
            合同号 </label>
            <label>
            <input type="radio" name="orders" value="orders2" onclick="searchShow('s1')"/>
              业务员名</label>
            <label>
            <input type="radio" name="orders" value="orders3" onclick="searchShow('s2')"/>
              时间段</label>&nbsp;&nbsp;&nbsp;&nbsp;
            <strong>合同类型</strong>：
          <label>
            <input type="radio" name="mytypes" id="types1" value="A" />
            A类 </label>
    <label>
              <input type="radio" name="mytypes" id="types3" value="BS" />
              BS类 </label>
          <label>
          <input type="radio" name="mytypes" id="types2" value="CC" />
CC类 </label>
          <label>
          <input type="radio" name="mytypes" id="types4" value="ICP" />
          ICP
          类 </label>
          <label>
          <input type="radio" name="mytypes" id="types5" value="w" />
          W类 </label><label>
		  <input type="radio" name="mytypes" id="types6" value="LT" />
          LT类 </label>
          <label>
          <input type="radio" name="mytypes" id="types7" value="K" />
K类 </label><br />
            <strong>关 键 词</strong>：<span style="display: " id="search1">
            <input name="keys" type="text" id="keys" size="40" />
            </span> <span id="search2" style="display:none;">
            <input name="time1" type="text" id="time1" size="16" readOnly onClick="setDay(this);"/>
              —
              <input name="time2" type="text" id="time2" size="16" readOnly onClick="setDay(this);"/>
            </span>
            <input name="Submit" type="submit" class="searchInput" value="查询" />
            <input name="page" type="hidden" id="page" value="1" /></td>
      </tr>
    </form>
  </table>
<?

if($mytypes){
	$sql_S1=" and types like '$mytypes%' ";
}

$myUrl="caiwuGD.php?mytypes=$mytypes";
if($orders!="" and ($keys!="" || ($time1!=""&&$time2!=""))){
	switch($orders){
		case "orders0":
			if($keys){
				$sql_S1.=" and ContractNO like '%$keys%' ";
			}	
			if($mytypes){
				$sql_S1.=" and types like '%$mytypes%' ";
			}
			$myUrl="caiwuGD.php?orders=orders0&keys=$keys&mytypes=$mytypes";
		break;
		case "orders1":
			$sql_S1.=" and ContractNO='$keys' ";
			$myUrl="caiwuGD.php?orders=orders1&keys=$keys&mytypes=$mytypes";
		break;
		case "orders2":
			$sql_S1.=" and yewuyuan like '%$keys%' ";
			$myUrl="caiwuGD.php?orders=orders2&keys=$keys&mytypes=$mytypes";
		break;	
		case "orders3":
			$sql_S1.=" and ( contTime>='$time1' and contTime<='$time2' ) ";
			$myUrl="caiwuGD.php?orders=orders3&time1=$time1&time2=$time2&mytypes=$mytypes";
		break;
	}
}

//搜索权限设置
$sql_pur="";
$typeList=isTypes($db,$ses_PersonnelID);
if(!empty($typeList)){
	$sql_pur.=" and types in (".$typeList.") ";
}

$departmentList=isDepartment($db,$ses_PersonnelID);
if(!empty($departmentList)){	
	$sql_pur.=" and DepartmentID in (".$departmentList.") ";
}else{
	$sql_pur.=" and DepartmentID in (".$ses_did.") ";
}


?>
  <table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC" class="margin-top">
    <tr>
      <th height="20" align="left" bgcolor="#ECF1F4">合同列表：</th>
    </tr>
    <tr id="cont3">
      <td bgcolor="#FFFFFF"><table width="100%" border="1" cellpadding="3" cellspacing="0" bordercolor="#e3e3e3"  style="border-collapse: collapse">
        <tr bgcolor="#f3f3f3">
          <th width="20%">合同号</th>
          <th width="15%">业务员</th>
          <th width="20%">时间</th>
          <th width="15%">退回</th>
          <th width="15%">查看</th>
          <th width="15%">操作</th>
          </tr>
<?
// 搜索总记录数

if($ses_ID==122 ){
	$sql_Vcount= "select count(id) as count from Vcontract where invalid=0 and status=3 ".$sql_S1;
}else{
	$sql_Vcount= "select count(id) as count from Vcontract where invalid=0 and status=3 ".$sql_S1.$sql_pur;
}
echo "<!-- ".$sql_Vcount." -->";
$res_Vcount= mysql_query($sql_Vcount,$dblink);
$row_Vcount = mysql_fetch_array($res_Vcount);
$count=$row_Vcount["count"];
if(empty($count)){
	echo "<tr><td colspan=5>没有符合条件的合同!</td></tr>";
}else{

//分页参数设置
$pageNumber=20;
$pageSize=ceil($count/$pageNumber);
if(empty($page)){
	$page=1;
}elseif($page>$pageSize){
	$page=$pageSize;
}
$minNumber=($page-1)*$pageNumber;
$maxNumber=$page*$pageNumber;
if($maxNumber>$count){
	$maxNumber=$count;
}

//分页的搜索 invalid 是否申请作废

if($ses_ID==122 ){
	$sql_Vw= "select * from Vcontract where invalid=0 and status=3 ".$sql_S1;
}else{
	$sql_Vw= "select * from Vcontract where invalid=0 and status=3 ".$sql_S1.$sql_pur;
}

//分页条件
$sql_Vw .=" order by contTime desc limit $minNumber,$pageNumber";

$res_Vw= mysql_query($sql_Vw,$dblink) or die(mysql_error()."<br>".$sql_Vw);
$i=0;
while($row_Vw = mysql_fetch_array($res_Vw)){
	$types=$row_Vw["types"];
	if(empty($row_Vw["ContractNO"])){
		$contractNO=sprintf("%s%08d",$types,$row_Vw["id"]);
	}else{
		$contractNO=$row_Vw["ContractNO"];
	}
	$status=$row_Vw["status"];
	$contTime=$row_Vw["contTime"];
	$yewuyuan=$row_Vw["yewuyuan"];
	$id=$row_Vw["id"];
	$sql_cw = "select ID from Vaccountitem where contractNO = '".$contractNO."'";
	$result = mysql_query($sql_cw, $dblink);
?>
        <tr align="center" bgcolor="#ffffff" onmouseover="this.style.backgroundColor='#f0fff0';" onmouseout="this.style.backgroundColor='#ffffff';">
          <td><?=$contractNO?></td>
          <td><?=$yewuyuan?></td>
          <td><?=$contTime?></td>
  		  <td>&nbsp;
<?
		if($isManager){
?>
		  <a href="backContract.php?contractNO=<?=$contractNO?>&types=<?=$types?>&isManager=<?=$isManager?>" onClick="return confirm('你确认要退回吗?')" class='pink width20' title='你是管理员,在入库前可以删除实收,退回给客服重新审核!'>退回处理</a>
<?
		}else{
			if(mysql_num_rows($result)<=0){
?>
		  <a href="backContract.php?contractNO=<?=$contractNO?>&types=<?=$types?>" onClick="return confirm('你确认要退回吗?')" class='pink width20' title='如果你在录入实收前发现错误, 可以退回给客服重新审核!'>退回处理</a>
<?
			}else{
			  echo "退回处理";
			}
		}
?>
		  </td>
          <td>
		  <? 
		 //判断是否是新的EIP合同
		if($row_Vw["contTime"]>'2010-02-24' && $row_Vw["types"] == "EIP" )
		{
		     echo "<a href='javascript:' onclick=\"window.open('EIPNEW-view.php?contractNO=".$contractNO."','','width=1020,height=740,scrollbars=yes,top=0,left=0')\" title='合同查看' class='green width20'>合同查看</a>"; 
		}
		else
		{
			 echo "<a href='javascript:' onclick=\"window.open('".$types."-view.php?contractNO=".$contractNO."','','width=1020,height=740,scrollbars=yes,top=0,left=0')\" title='合同查看' class='green width20'>合同查看</a>"; 
		}
		  ?>
          </td>
          <td>
		  <?
		   //判断是否是新的EIP合同
		if($row_Vw["contTime"]>'2010-02-24' && $row_Vw["types"] == "EIP" )
		{
			echo "<a href='EIPNEW-gongdanShow.php?contractNO=".$contractNO."' title='添加财务信息；产品注册；合同生成工单!' class='blue width20'>后续处理</a>";
		}
		else
		{
			echo "<a href='".$types."-gongdanShow.php?contractNO=".$contractNO."' title='添加财务信息；产品注册；合同生成工单!' class='blue width20'>后续处理</a>";
		}
         ?>
</td>
</tr>
<?
	}
//结束while
?>
       <tr align="center" bgcolor="#ffffff">
          <td colspan="5" bgcolor="#f3f3f9">共<?=$count?>条纪录，当前<?=$page?>／<?=$pageSize?>页； <?
if($page>1){
	echo "<a href='".$myUrl."&page=1'>首 页</a>　<a href='".$myUrl."&page=".($page-1)."'>上一页</a>　";
}else{
	echo "首 页　上一页　";
}

if($pageSize>1 && $pageSize>$page){
	echo "<a href='".$myUrl."&page=".($page+1)."'>下一页</a>　<a href='".$myUrl."&page=".$pageSize."'>末 页</a>";	
}else{
	echo "下一页　末 页";
}

?>              &nbsp;
            <select name="pages" id="pages" onchange="location='<?=$myUrl?>&page='+this.options[this.selectedIndex].value;">

<?
for($i=1;$i<=$pageSize;$i++){
?>
              <option value="<?=$i?>" <? if($page==$i){echo "selected";}?> ><?=$i?></option>
<?
}
?>
            </select>            </td>
          </tr>
<?
}
//结束if
?>
</table>
</td>
    </tr>
  </table>
</div>
</body>
</html>
