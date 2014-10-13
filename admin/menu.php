<?
IncludeModuleLangFile(__FILE__);
if ($USER->IsAdmin())
{
	$menu = array(
		"parent_menu" => "global_menu_settings",
		"section" => "terminal",
		"sort" => 1,
		"text" => GetMessage("SLOBEL_MENU_ITEM"),
		"url" => "slobel_terminal.php?lang=".LANGUAGE_ID,
		"icon" => "slobel_menu_icon",
		"page_icon" => "slobel_menu_icon",
		"items_id" => "menu_terminal",
	);
	return $menu;
}
else
{
	return false;
}
?>