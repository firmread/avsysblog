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

  class DevGeSHi {

    var $GeSHIObj;
    var $Source;

    function DevGeSHi($ASource = '', $ALanguage = ''){
      GLOBAL $DevFmt_Config;
      $res = DevGeSHi::langNeeded($ALanguage);

      if($res == 2){
        $ALanguage = "";
        $this->GeSHIObj = null;
        $this->Source = $ASource;
        return ;
      }else
        $this->Source = "";

      $this->GeSHIObj = new GeSHi($ASource, $ALanguage, $DevFmt_Config['geshilangpath']);
      $this->GeSHIObj->set_overall_style("");
      $this->GeSHIObj->set_overall_class("");
      $this->GeSHIObj->set_overall_id("");
      $this->GeSHIObj->set_code_style("");
      $this->GeSHIObj->enable_keyword_links($DevFmt_Config['geshiuselink']);
      $this->GeSHIObj->enable_line_numbers(false);
      $this->GeSHIObj->enable_multiline_span(false);
      $this->GeSHIObj->set_header_type(GESHI_HEADER_PRE);
    }

    function parse_code(){
      if($this->GeSHIObj != null){
        $code = $this->GeSHIObj->parse_code();
      }else{
        $code = $this->Source;
      }

      preg_match("#".chr(2)."(.*?)".chr(2)."#si", $code, $code);
      return $code[1];
    }

    function langFileTitle($ALangFile){
      GLOBAL $DevFmt_Config;
      $name = "";
      $langFile = $DevFmt_Config['geshilangpath'].DIRECTORY_SEPARATOR.$ALangFile;
      if(file_exists($langFile)){
        ob_start();
        include($langFile);
        if(isset($language_data)){
          $name = $language_data['LANG_NAME'];
          unset($language_data);
        }
        ob_end_clean();
      }else{
        $name = array_shift(explode(".", $ALangFile));
      }
      return $name;
    }

    function langFiles(){
      GLOBAL $DevFmt_Config;
      $res = Array();
      $suppLangs = @scandir($DevFmt_Config['geshilangpath']);
      if(is_array($suppLangs)){
        array_shift($suppLangs);
        array_shift($suppLangs);

        foreach($suppLangs as $file)
          $res[] = Array(
            'langname' => DevGeSHi::langFileTitle($file),
            'langfile' => $file,
            'fav' => '0'
          );
      }

      return $res;
    }

    function langNeeded($ALanguage){
      GLOBAL $DevFmt_Config;

      if($ALanguage == "html")
        DevGeSHi::langNeeded("html4strict");

      $langFile = $DevFmt_Config['geshilangpath'].DIRECTORY_SEPARATOR.$ALanguage.".php";
      $exists = file_exists($langFile);
      if(!$exists){
        $langData = implode("\n", file("http://svn.wp-plugins.org/devformatter/branches/langs/".$ALanguage.".php"));
        if(($langData) && (strpos($langData, "The requested URL /devformatter/branches/langs/") === false)){
          @mkdir($DevFmt_Config['geshilangpath']);
          $f = fopen($langFile, "w+");
          if($f){
            fwrite($f, $langData);
            fclose($f);
          }
          $exists = 1;
        }else{
          $exists = 2;
        }
      }else{
        $exists = 0;
      }
      return $exists;
    }
  }

?>