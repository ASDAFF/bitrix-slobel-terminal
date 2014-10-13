<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);
$MODULE_ID = 'slobel.terminal';

$APPLICATION->SetTitle(GetMessage("MODULE_STEP1"));
$APPLICATION->AddHeadScript("/bitrix/js/".$MODULE_ID."/jquery-2.0.3.min.js");
$APPLICATION->AddHeadScript("/bitrix/js/".$MODULE_ID."/main.js");

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
$aTabs = array(array("DIV" => "main", "TAB" => GetMessage("TAB1"), "TITLE" => GetMessage("TAB1")),);



$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin();
$tabControl->BeginNextTab();

if(function_exists('shell_exec') && function_exists('proc_open')):
?>
<tr id="slobel-console-promt">
	<td width="100%" colspan="2">
		<textarea cols="60" id="log" rows="25" wrap="OFF" readonly></textarea>
		<select id="history"></select>
		<div for="command" id="container">
			<label for="command" id="cwd"></label>
			<input type="text" name="command" id="command" autocomplete="off" autofocus>
			<label id="os-version"></label>
		</div>
	</td>
</tr>
<?else:?>
<tr class="heading" id="slobel-error"><td colspan="2"><?=GetMessage("FUNC_ERR")?></td></tr>
<?endif;
$tabControl->Buttons();?>
<input type="hidden" name="go" value="Y">
<div align="left" id="slobel-button">
        <input class="adm-btn-save" type="button" <?if(!$USER->IsAdmin()):?>disabled<?endif;?> id="go" name="go" value="<?=GetMessage("MAIN_SAVE")?>">
		<input type="button" <?if(!$USER->IsAdmin()):?>disabled<?endif;?> id="reset" name="reset" value="<?=GetMessage("MAIN_RESET")?>">
		<input type="button" <?if(!$USER->IsAdmin()):?>disabled<?endif;?> id="history-command" name="history-command" value="<?=GetMessage("MAIN_HISTORY")?>">
</div>

<?
$tabControl->End();
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>