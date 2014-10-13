<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Class slobel_terminal extends CModule
{
		const MODULE_ID = 'slobel.terminal';
		var $MODULE_ID = 'slobel.terminal';
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;

	function __construct()
		{
			$arModuleVersion = array();
			include(dirname(__FILE__)."/version.php");
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME = GetMessage("SLOBEL_MODULE_NAME");
			$this->MODULE_DESCRIPTION = GetMessage("SLOBEL_MODULE_DESC");

			$this->PARTNER_NAME = GetMessage("SLOBEL_PARTNER_NAME");
			$this->PARTNER_URI = GetMessage("SLOBEL_PARTNER_URI");
		}
	
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->InstallDB();
                $this->InstallFiles();
                $GLOBALS["errors"] = $this->errors;
                $APPLICATION->IncludeAdminFile(GetMessage("SLOBEL_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/step1.php");
        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->UnInstallDB();
                $this->UnInstallFiles();
                $APPLICATION->IncludeAdminFile(GetMessage("SLOBEL_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/unstep1.php");
        }
        function InstallDB()
        {
                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                RegisterModule(self::MODULE_ID);
        }
        function UnInstallDB($arParams = array())
        {
                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                UnRegisterModule(self::MODULE_ID);

                return true;

        }
        function InstallFiles($arParams = array())
        {
        	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
        	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
        	return true;
        }
        
        function UnInstallFiles()
        {
        	if($_ENV["COMPUTERNAME"]!='BX')
        	{
        		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
        		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js");
        	}
        	return true;
        }   
}
?>
