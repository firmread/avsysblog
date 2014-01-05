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
  $out="";while(!file_exists($out."wp-config.php"))$out.="..".DIRECTORY_SEPARATOR;
  require_once($out."wp-config.php");
  @header('Content-Type: text/css; charset=' . get_option('blog_charset'));

  echo preg_replace("/(?:\ \!important)*;/i", " !important;", $DevFmt_Config[devfmtcss]);
?>