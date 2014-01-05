<?php
/*
Developer Formater
http://wordpress.org/extend/plugins/devformatter/
Developer Formatter system to WordPress. Powered by <a href="http://qbnz.com/highlighter/">GeSHi</a> server-side highlight system.
Version: 2013.0.1.41
Author: Gilberto Saraiva
Author URI: http://gsaraiva.projects.pro.br/

Copyright (c) 2013 Gilberto Saraiva
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
*/

  function devfmt_Initializate(){
  	GLOBAL $DevFmt_Config, $DevFmt_SuppLangs, $table_prefix, $wpdb;

    devfmt_ReadConfig();
    if(IsSet($DevFmt_Config['version'])){
      switch(devfmt_Version($DevFmt_Config['version'])){
        case(2009000001029): // none
        case(2009000001030): // none
        case(2009000001031): // none
        case(2009000001032): // none
        case(2009000001033): // none
        case(2011000001034): // none
        case(2011000001035): // none
        case(2011000001036): // none
        case(2012000001037): // none
        case(2012000001038): // none
        case(2012000001039): // none
      }
    }else{
      if($wpdb->get_var("show tables like '".$table_prefix."devfmt_config'") != ""){
        $DevFmt_Config = $wpdb->get_row("SELECT * FROM `{$table_prefix}devfmt_config`", ARRAY_A);
        $DevFmt_Config['version'] = devfmt_Version();
        $DevFmt_SuppLangs = $wpdb->get_results("SELECT * FROM `{$table_prefix}devfmt_supplangs`", ARRAY_A);

        $FavLangs = Array();
        foreach($DevFmt_SuppLangs as $Lang)
          if($Lang['fav'])
            $FavLangs[] = $Lang['langname'];
        $DevFmt_Config["favLangs"] = $FavLangs;

        $wpdb->query("drop table `{$table_prefix}devfmt_config`");
        $wpdb->query("drop table `{$table_prefix}devfmt_supplangs`");
      }else{
        $DevFmt_Config["favLangs"] = Array();
        $DevFmt_Config['devfmtcss'] = devfmt_DefaultPublicCSSBlue();
        $DevFmt_Config['displaylinenumbers'] = "1";
        $DevFmt_Config['usedevformat'] = "1";
        $DevFmt_Config['parsepre'] = "1";
        $DevFmt_Config['showtools'] = "1";
        $DevFmt_Config['hookrss2'] = "1";
        $DevFmt_Config['copyclipboartext'] = 'copy code';
        $DevFmt_Config['geshilangpath'] = DEVFMT_GESHIPATH;
        if(DIRECTORY_SEPARATOR == '\\'){ // win server
          $DevFmt_Config['geshilangpath'] = str_replace("\\\\", "\\", $DevFmt_Config['geshilangpath']);
        }
        $DevFmt_Config['useajaxparse'] = "0";
      }
      devfmt_UpdateConfig();
    }
  }

  function devfmt_HookFilter($AFilterName){
    add_filter($AFilterName, 'devfmt_Formatter_Ini_'.$AFilterName, 1);
    add_filter($AFilterName, 'devfmt_Formatter_End_'.$AFilterName, 10);
  }

  function devfmt_Install(){
    register_activation_hook(DEVFMT_PATH.'devformatter.php', 'devfmt_Initializate');
    add_filter('tiny_mce_before_init', 'devfmt_addExtendsToMCE');
    add_action('init', 'devfmt_addInterface');
    add_action('admin_head', 'devfmt_adminHeader');
    add_action('admin_menu', 'devfmt_addPages');
    add_action('the_editor', 'devfmt_initEditor');
    add_action('richedit_pre', 'devfmt_rollbackFormat');

    devfmt_HookFilter('the_content');
    devfmt_HookFilter('comment_text');
    devfmt_HookFilter('the_excerpt');

    devfmt_HookFilter('the_content_rss');
    devfmt_HookFilter('comment_text_rss');
    devfmt_HookFilter('the_excerpt_rss');

    add_action('wp_head', 'devfmt_publicHeader');

    add_action('wp_ajax_devfmt_parseCode', 'devfmt_Formatter_ajax_devfmt_parseCode');
    add_action('wp_ajax_nopriv_devfmt_parseCode', 'devfmt_Formatter_ajax_devfmt_parseCode');
  }
?>
