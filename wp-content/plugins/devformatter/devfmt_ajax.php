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

  error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
  define('DOING_AJAX', true);
  $out="";while(!file_exists($out."wp-config.php"))$out.="..".DIRECTORY_SEPARATOR;
  require_once($out."wp-config.php");
  require_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'admin.php');

  @header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

  if(IsSet($_POST['favs'])){
    $FavIndexs = explode('-', urldecode($_POST['favs']));
    $Favs = $DevFmt_Config["favLangs"];

    foreach($FavIndexs as $key => $Fav)
      $Favs[] = $Fav;

    $DevFmt_Config["favLangs"] = $Favs;
    devfmt_UpdateConfig();
  }else if(IsSet($_POST['preview'])){
    $code = str_replace(Array('\\\'', '\\"', '\\\\'), Array('\'', '"', '\\'), $_POST['preview']);
    $options = Array();
    if(IsSet($_POST['preview_lines']) && ($_POST['preview_lines'] == 'true'))
      $options["lines"] = "lines";
    if(IsSet($_POST['preview_sline']) && ($_POST['preview_sline'] > 1))
      $options["sl"] = $_POST['preview_sline'];
    if(IsSet($_POST['preview_notools']) && ($_POST['preview_notools'] == 'true'))
      $options["notools"] = "notools";

    $parsed = devfmt_ParseStructure(
      devfmt_ParseCode($code, $_POST['preview_lang'], true),
      $_POST['preview_lang'], $options);

    echo $parsed;
  }
?>
