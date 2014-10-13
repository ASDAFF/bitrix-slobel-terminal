<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
ignore_user_abort(true);
set_time_limit(0);
$MODULE_ID = 'slobel.terminal';
$hist="";
$select="";
if(function_exists('shell_exec') && function_exists('proc_open')){
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$MODULE_ID."/classes/main.php");
	$terminal = new SlobelTerminal;
	
	$callback['history']=$terminal->draw('history');
	$callback['output']=$terminal->draw('output');
	$callback['fullcwd']=$terminal->draw('fullcwd');
	$callback['os']=$terminal->draw('os');
	
	for($i=0; $i<=count($callback['history']);$i++){
		if($i==count($callback['history'])-1)
			$select="data-hist='this'";
	
		if($i==count($callback['history'])-2)
			$select="data-hist='prev'";
	
		if(!empty($callback['history'][$i]))
			$hist.="<option id='item-".$i."' value='".$i."' ".$select." >".$callback['history'][$i]."</option>\n";
	}
	
	$callback['history']=$hist;
	
	echo json_encode($callback);
};
?>
