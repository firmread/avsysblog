<?php
/*
Plugin Name: Developer Formatter
Plugin URI: http://wordpress.org/extend/plugins/devformatter/
Description: Developer Formatter system to WordPress. Powered by <a href="http://qbnz.com/highlighter/">GeSHi</a> server-side highlight system.
Version: 2013.0.1.41
Author: Gilberto Saraiva
Author URI: http://gsaraiva.projects.pro.br/

Copyright (c) 2013 Gilberto Saraiva
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
*/

  define('DEVFMT_FOLDER', dirname(plugin_basename(__FILE__)), TRUE);
  define('DEVFMT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR, TRUE);
  define('DEVFMT_URL', get_bloginfo('wpurl')."/wp-content/plugins/devformatter/");
  define('DEVFMT_GESHIPATH', DEVFMT_PATH."geshi".DIRECTORY_SEPARATOR."geshi");

  $DevFmt_FiltersToAvoid = Array("wptexturize", "wpautop", "force_balance_tags");
  $DevFmt_CodeIndex = 0;

  require_once(DEVFMT_PATH.'devcommon.php');
  require_once(DEVFMT_PATH.'devinterface.php');
  require_once(DEVFMT_PATH.'devinstall.php');
  require_once(DEVFMT_PATH.'devgeshi.php');

  function devfmt_GeSHiParseCode($ACode, $ALang, $ADevFmt = false){
    $geshi = new DevGeSHi(chr(2).$ACode.chr(2), $ALang);
    return $geshi->parse_code();
  }

  function devfmt_ParseCode($ACode, $ALang, $ADevFmt){
    if($ACode == "") return $ACode;
    $code = $ACode;
    $code = str_replace("\r\n", "\n", $code);
    $code = str_replace("\n", "\r\n", $code);

    $OtherLang = true;
    $WebServerSideLangs = Array(
      "php"       => Array("#<\?(.*?)\?>#s", "<\?(.*?)\?>", "<?", "?>"),
      "php-brief" => Array("#<\?(.*?)\?>#s", "<\?(.*?)\?>", "<?", "?>"),
      "asp"       => Array("#<\%(.*?)\%>#s", "<\%(.*?)\%>", "<%", "%>")
    );

    foreach($WebServerSideLangs as $Lang => $Checkers){
      if(($ALang == $Lang) && (preg_match($Checkers[0], $code, $matches))){
        $OtherLang = false;
        $SvLang = array(array(uniqid(''), $Checkers[1]));

        while(preg_match($Checkers[0], $code, $match)){
          $check = $match[0];
          array_shift($match);
          $SvLang[] = $match;
          $code = devfmt_StrReplace($check, $SvLang[0][0].(Count($SvLang) - 1)."_", $code, 1);
        }

        $code = devfmt_ParseCode($code, "html", $ADevFmt);

        for($i = 1; $i < Count($SvLang); $i++){
          $code = devfmt_PregReplace("#".$SvLang[0][0].$i."_#",
            devfmt_GeshiParseCode($Checkers[2] . $SvLang[$i][0] . $Checkers[3], $ALang, $ADevFmt), $code, 1);
        }

        break;
      }
    }

    // Handle HTML format [HTML, JavaScript, Style]
    if($ALang == "html"){
      $unid = uniqid('');
      $OtherLang = false;
      $SvScript = Array();
      while(preg_match("#<(script[^>]*)>(.*?)</(script)>#si", $code, $matches)){
        $id = $unid."script".Count($SvScript);
        if($matches[2] != ""){
          $subcode = devfmt_GeshiParseCode($matches[2], "javascript", $ADevFmt);
        }else
          $subcode = "";

        $SvScript[] = Array($id, $matches[1], $matches[3], $subcode);
        $code = devfmt_StrReplace($matches[0], $id, $code, 1);
      }

      $SvStyle = Array();
      while(preg_match("#<(style[^>]*)>(.*?)</(style)>#si", $code, $matches)){
        $id = $unid."style".Count($SvStyle);
        if($matches[2] != ""){
          $subcode = devfmt_GeshiParseCode($matches[2], "css", $ADevFmt);
        }else
          $subcode = "";

        $SvStyle[] = Array($id, $matches[1], $matches[3], $subcode);
        $code = devfmt_StrReplace($matches[0], $id, $code, 1);
      }

      foreach($SvScript as $script)
        $code = str_replace($script[0], "<".$script[1].">".$script[0]."</".$script[2].">", $code);

      foreach($SvStyle as $style)
        $code = str_replace($style[0], "<".$style[1].">".$style[0]."</".$style[2].">", $code);

      $code = devfmt_GeshiParseCode($code, $ALang, $ADevFmt);

      foreach($SvScript as $script)
        $code = str_replace($script[0], $script[3], $code);

      foreach($SvStyle as $style)
        $code = str_replace($style[0], $style[3], $code);
    }

    // Handle others formats
    if($OtherLang)
      $code = devfmt_GeshiParseCode($code, $ALang, $ADevFmt);

    return $code;
  }

  function devfmt_ParseStructure($ACode, $ALang, $AOptions, $AStyle = "", $AAjaxRequest = false){
    GLOBAL $DevFmt_Config, $DevFmt_CodeIndex;
    if($AStyle != "") $AStyle = " style=\"".$AStyle."\"";

    if(!is_feed() && (!$DevFmt_Config['useajaxparse'] || $AAjaxRequest)){
      $NewCode = "<table class=\"devcodearea\" width=\"100%\">";

      $CodeLines = explode("\n", $ACode);
      $TrOdd = false;
      $PadSize = strlen("" . Count($CodeLines));

      $LineCountOffset = 1;
      if(IsSet($AOptions['sl']))
        $LineCountOffset = $AOptions['sl'] * 1;

      for($i = 0; $i < Count($CodeLines); $i++){
        $lineNumClass = "devcodelines" . ($TrOdd? " devcodelinesodd" : "");
        $lineCodeClass = "devcodelinesarea" . ($TrOdd? " devcodelinesareaodd" : "");
        if($TrOdd){
          $lineNumClass .= " devcodelinesodd";
          $lineCodeClass .= " devcodelinesareaodd";
        }

        $lineNum = "";
        if($AOptions['lines'] == "lines")
          $lineNum = "<td class=\"" . $lineNumClass . "\" width=\"1%\">" .
            str_pad("".($i + $LineCountOffset), $PadSize, '0', STR_PAD_LEFT) .
            "</td>";

        $lineCode = "<td class=\"" . $lineCodeClass . "\"><pre class=\"devcode devcodeline\">" . $CodeLines[$i] . "</pre></td>";

        $NewCode .= "<tr>". $lineNum . $lineCode . "</tr>";
        $TrOdd = !$TrOdd;
      }
      $ACode = $NewCode . "</table>";
    }else{
      if($AOptions['lines'] == "lines"){
        $CodeLines = explode("\n", $ACode);
        for($i = 0; $i < Count($CodeLines); $i++)
          $CodeLines[$i] = "<li>".str_replace("  ", "&nbsp;&nbsp;", $CodeLines[$i])."</li>";
        $ACode = "<ol>".implode("", $CodeLines)."</ol>";
      }

      if($DevFmt_Config['useajaxparse'] && !$AAjaxRequest)
        $ACode = "<div id=\"#DEVFMT_AJAX_ID#\">" . $ACode . "</div>";
    }

    if($AAjaxRequest)
      return $ACode;

    $Toolbar = "";
    if($DevFmt_Config['showtools'] && !is_feed() && !IsSet($AOptions['notools'])){
      $Toolbar = '<table class="devcodetools"><tbody><tr>'.
        '<td>&nbsp;'.devfmt_getLangTitle($ALang).'&nbsp;|&nbsp;</td>'.
        '<td style="background-image:url(\''.DEVFMT_URL.'img/devformatter-copy.png\');background-repeat:no-repeat;'.
        'background-position:50% 50%;width:16px;height:16px;"/><embed id="ZeroClipboard'.$DevFmt_CodeIndex.'" src="'.
        DEVFMT_URL.'_zclipboard.swf" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="16px" height="16px"'.
        ' align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash"'.
        ' pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="id='.$DevFmt_CodeIndex.'&width=16&height=16"'.
        ' wmode="transparent" /></td>'.
        '<td>&nbsp;'.str_replace(" ", "&nbsp;", $DevFmt_Config['copyclipboartext']).'&nbsp;|</td>'.
        '<td style="cursor:pointer" title="DevFormatter Plugin" onclick="devfmt_credits()">?</td>'.
        '<td width="99%">&nbsp;</td></tr></tbody></table>';
    }

    $regionflag = Array("", "");
    if($DevFmt_Config['usedevformat'])
      $regionflag = Array("<!--DEVFMTCODE-->", "<!--END_DEVFMTCODE-->");

    return $regionflag[0].'<pre class="devcodeblock" title="'.devfmt_getLangTitle($ALang).'">'.
        $Toolbar.'<div class="devcodeoverflow"'.$AStyle.'>'.$ACode.'</div></pre>'.$regionflag[1];
  }

  function devfmt_ParseContentCode($ACode, $ADevFmt = false, $AAjaxRequest = false){
    GLOBAL $DevFmt_Config, $DevFmt_CodeIndex;

    $DevFmt_CodeIndex++;

    $opts = array();
    if(trim($ACode['options']) != ""){
      $opts['base'] = $ACode['options'];
      preg_match_all("(\w+\=[\"|'][^\"|']*[\"|'])", $ACode['options'], $matchs);
      $optsdata = $matchs[0];
      foreach($optsdata as $option){
        $opt = explode("=", $option);
        $opt[1] = trim($opt[1]);
        if($opt[1][0] == "\"")
          $opt[1] = substr($opt[1], 1, strlen($opt[1]) - 2);
        $opts[strtolower($opt[0])] = $opt[1];
      }
    }

    if(IsSet($opts['lang'])){
      $opts['lang'] = StrToLower($opts['lang']);
      if(strpos($opts['lang'], '[') !== false){
        if(preg_match("/\[(.*?)\]/", $opts['lang'], $match)){
          $tmpOpts = explode("-", $match[1]);
          $opts['options'] = Array();
          if(is_array($tmpOpts))
            foreach($tmpOpts as $Opt)
              if(strpos($Opt, ":") !== false){
                $Opt = explode(":", $Opt);
                $opts['options'][$Opt[0]] = $Opt[1];
              }else
                $opts['options'][$Opt] = $Opt;

          $opts['lang'] = str_replace($match[0], '', $opts['lang']);
        }
      }
      if(!IsSet($opts['options'])){
        $opts['options'] = Array();
      }

      if($DevFmt_Config['displaylinenumbers'])
        $opts['options']['lines'] = "lines";

      if(!IsSet($opts['style']))
        $opts['style'] = "";

      if($AAjaxRequest || !$DevFmt_Config['useajaxparse']){
        $code = devfmt_ParseCode($ACode['code'], $opts['lang'], $ADevFmt);
      }else{
        $code = htmlentities($ACode['code']);
      }

      $code = devfmt_ParseStructure($code, $opts['lang'], $opts['options'], $opts['style'], $AAjaxRequest);

    }else{
      $code = $ACode['full'];
    }

    return array($code, $opt);
  }

  function devfmt_ContentGrabCode($AContent){
    GLOBAL $DevFmt_Config;

    $GeshiOperators = Array();
    $GeshiOperators[] = Array("!<code(.*?)>(.*?)</code>!is", 1);
    if($DevFmt_Config['parsepre'])
      $GeshiOperators[] = Array("!<pre(.*?)>(.*?)</pre>!is", 2);

    $AMatchs = array();
    foreach($GeshiOperators as $GeshiOperatorRule){
      $GeshiOperator = $GeshiOperatorRule[0];
      $codes = array();
      $unid = "";
      $i = 0;

      while(preg_match($GeshiOperator, $AContent, $matches)){
        if($unid == "")
          $unid = uniqid('');

        $i++;
        $codes[$i]['full'] = $matches[0];
        $codes[$i]['options'] = $matches[1];
        if($brformat)
          if(trim($matches[2]) != ""){
            $codelines = explode("\r\n", $matches[2]);
            if(trim($codelines[0]) == "")
              array_shift($codelines);

            $matches[2] = implode("\r\n", $codelines);
          }
        $codes[$i]['code'] = $matches[2];
        $AContent = devfmt_StrReplace($matches[0], $unid.$i."_", $AContent, 1);
      }

      if($unid != "")
        $AMatchs[] = array($unid, $codes);
    }

    return array($AContent, $AMatchs);
  }

  function devfmt_ContentCheckCode($AContent, $ADevFmt = false, $APost = null){
    GLOBAL $DevFmt_Config, $DevFmt_ParsedCode;

    $GrabCode = $DevFmt_ParsedCode;//devfmt_ContentGrabCode($AContent);
    //$AContent = $GrabCode[0];
    $MatchCodes = $GrabCode[1];

    $iCode = 0;
    foreach($MatchCodes as $MatchCode){
      $codes = $MatchCode[1];
      $unid = $MatchCode[0];

      $i = 0;
      while(preg_match("!".$unid."([0-9]+)_!is", $AContent, $matches)){
        $i++;
        $iCode++;

        $parsedCode = devfmt_ParseContentCode($codes[$i], $ADevFmt, false);
        $code = $parsedCode[0];
        $opts = $parsedCode[1];

        if($DevFmt_Config['useajaxparse']){
          $code = str_replace("#DEVFMT_AJAX_ID#", "devfmt_ajax_".$iCode."_".(($APost != null)? $APost->ID : "0")."", $code);
          $code .= "<script> devfmt_addAjaxBuffer(".$iCode.", ".(($APost != null)? $APost->ID : "0")."); </script>";
        }

        $AContent = devfmt_PregReplace("!".$unid."([0-9]+)_!is", $code, $AContent, 1);
      }
    }

    return array(Count($MatchCodes) > 0, $AContent);
  }

  function devfmt_ContentFormat($AContent, $AFilter, $AInit = true){
    GLOBAL $DevFmt_Config, $DevFmt_ParsedCode, $DevFmt_SuppLangs, $DevFmt_FiltersToAvoid, $post;

    if(($AFilter == 'example') || ($DevFmt_Config['usedevformat'])){
      if(!is_feed() || (is_feed() && ($DevFmt_Config['hookrss2']))){        
        if($AInit){
          $DevFmt_ParsedCode = devfmt_ContentGrabCode($AContent);
          $AContent = $DevFmt_ParsedCode[0];
        }else{
          $ContentCheck = devfmt_ContentCheckCode($AContent, true, (IsSet($post)? $post : null));
          if($ContentCheck[0])
            $AContent = $ContentCheck[1];
        }
      }
    }
    
    return $AContent;
  }

  // BLOG  
  function devfmt_Formatter_Ini_the_content($AContent){
    return devfmt_ContentFormat($AContent, 'the_content');
  }
  function devfmt_Formatter_End_the_content($AContent){
    return devfmt_ContentFormat($AContent, 'the_content', false);
  }
  function devfmt_Formatter_Ini_comment_text($AContent){
    return devfmt_ContentFormat($AContent, 'comment_text');
  }
  function devfmt_Formatter_End_comment_text($AContent){
    return devfmt_ContentFormat($AContent, 'comment_text', false);
  }
  function devfmt_Formatter_Ini_the_excerpt($AContent){
    return devfmt_ContentFormat($AContent, 'the_excerpt');
  }
  function devfmt_Formatter_End_the_excerpt($AContent){
    return devfmt_ContentFormat($AContent, 'the_excerpt', false);
  }  

  function devfmt_Formatter_ajax_devfmt_parseCode(){
    GLOBAL $_POST, $DevFmt_CodeIndex;

    $post = get_post($_POST["dfPostId"]);
    $parseCodId = $_POST["dfId"];

    $GrabCode = devfmt_ContentGrabCode($post->post_content);
    $Content = $GrabCode[0];
    $MatchCodes = $GrabCode[1];

    $iCode = 0;
    foreach($MatchCodes as $MatchCode){
      $codes = $MatchCode[1];
      $unid = $MatchCode[0];

      $i = 0;
      while(preg_match("!".$unid."([0-9]+)_!is", $Content, $matches)){
        $i++;
        $iCode++;

        if($iCode == $parseCodId){
          $parsedCode = devfmt_ParseContentCode($codes[$i], $DevFmt_Config['usedevformat'], true);
          $code = $parsedCode[0];
          $opts = $parsedCode[1];

          echo json_encode(array("dfPostId" => $_POST["dfPostId"], "dfId" => $parseCodId, "code" => $code));
          die();
          exit;
        }

        $Content = devfmt_PregReplace("!".$unid."([0-9]+)_!is", '', $Content, 1);
      }
    }

    echo "not found";
    die();
    exit;
  }

  function devfmt_Formatter_comment_text($AContent){
    return devfmt_ContentFormat($AContent, 'comment_text');
  }

  function devfmt_Formatter_the_excerpt($AContent){
    return devfmt_ContentFormat($AContent, 'the_excerpt');
  }

  // RSS
  function devfmt_Formatter_Ini_the_content_rss($AContent){
    return devfmt_ContentFormat($AContent, 'the_content_rss');
  }
  function devfmt_Formatter_End_the_content_rss($AContent){
    return devfmt_ContentFormat($AContent, 'the_content_rss', false);
  }
  function devfmt_Formatter_Ini_comment_text_rss($AContent){
    return devfmt_ContentFormat($AContent, 'comment_text_rss');
  }
  function devfmt_Formatter_End_comment_text_rss($AContent){
    return devfmt_ContentFormat($AContent, 'comment_text_rss', false);
  }
  function devfmt_Formatter_Ini_the_excerpt_rss($AContent){
    return devfmt_ContentFormat($AContent, 'the_excerpt_rss');
  }
  function devfmt_Formatter_End_the_excerpt_rss($AContent){
    return devfmt_ContentFormat($AContent, 'the_excerpt_rss', false);
  }

  // Install DevFormatter Plugin
  devfmt_Install();
?>
