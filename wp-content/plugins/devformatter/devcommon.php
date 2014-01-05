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

  require_once(DEVFMT_PATH.'geshi'.DIRECTORY_SEPARATOR.'geshi.php');

  $DevFmt_Config = Array();
  $DevFmt_SuppLangs = Array();
  $DevFmt_ParsedCode = Array();

  function devfmt_Version($AVersion = ""){
    if($AVersion != ""){
      $verData = explode(".", $AVersion);
      $verTotal = 0;
      for($i = 0; $i < Count($verData); $i++){
        $verTotal *= 1000;
        $verTotal += $verData[$i];
      }
      return $verTotal;
    }else{
      return "2013.0.1.41";
    }
  }

  function devfmt_DefaultPublicCSS(){
    return "  .devcode, .devcodelines, .devcodeblock, .devcodetools {\n    padding: 0;\n    margin: 0;\n    font-size: 12px;\n    font-family: courier new;\n  }\n  .devcode {\n    padding: 0px 5px;\n    color: #000;\n    color: #666;\n  }\n  .devcodeoverflow {\n    margin: 1px;\n    overflow: auto;\n    overflow-x: auto;\n    overflow-y: auto;\n  }\n  .devcodelines {\n    padding-left: 5px;\n    padding-right: 5px;\n    background: #888;\n    color: #fff;\n    border-right: 4px solid #6E6;\n  }\n  .devcodelinesodd {\n    background: #999;\n  }\n  .devcodelinesarea {\n    background: #fff;\n  }\n  .devcodelinesareaodd {\n    background: #f0f0f0;\n  }\n  .devcodetools {\n    background: transparent;\n    color: #888;\n    text-align: left;\n  }\n  .devcodetools span {\n    cursor: pointer;\n  }";
  }
  $DevFmt_DefaultPublicCSS = devfmt_DefaultPublicCSS();

  function devfmt_DefaultPublicCSSBlue(){
    return "  .devcode, .devcodelines, .devcodeblock, .devcodetools {\n    padding: 0;\n    margin: 0;\n    font-size: 12px;\n    font-family: courier new;\n  }\n  .devcode {\n    padding: 0px 5px;\n    color: #000;\n    border: 1px solid #5A9AC0;\n  }\n  .devcodeline {\n    border: 0px;\n  }\n  .devcodeoverflow {\n    margin: 1px;\n    overflow: auto;\n    overflow-x: auto;\n    overflow-y: auto;\n  }\n  .devcodelines {\n    padding-left: 5px;\n    padding-right: 5px;\n    background: #729EB9;\n    color: #fff;\n    border-right: 4px solid #6E6;\n  }\n  .devcodelinesodd {\n    background: #628EA9;\n  }\n  .devcodelinesarea {\n    background: #fff;\n  }\n  .devcodelinesareaodd {\n    background: #f0f0f0;\n  }\n  .devcodetools {\n    background: transparent;\n    color: #000;\n    text-align: left;\n  }\n  .devcodetools span {\n    cursor: pointer;\n  }";
  }
  $DevFmt_DefaultPublicCSSBlue = devfmt_DefaultPublicCSSBlue();

  function devfmt_DefaultPublicCSSSummer(){
    return "  .devcode, .devcodelines, .devcodeblock, .devcodetools {\n    padding: 0;\n    margin: 0;\n    font-size: 12px;\n    font-family: courier new;\n  }\n  .devcode {\n    padding: 0px 5px;\n    color: #000;\n    border: 1px solid #fa0;\n  }\n  .devcodeline {\n    border: 0px;\n  }\n  .devcodeoverflow {\n    margin: 1px;\n    overflow: auto;\n    overflow-x: auto;\n    overflow-y: auto;\n  }\n  .devcodelines {\n    padding-left: 5px;\n    padding-right: 5px;\n    background: #ff0;\n    font-weight: bold;\n    color: #aa4801;\n    border-right: 4px solid #fd8;\n  }\n  .devcodelinesodd {\n    background: #f70;\n  }\n  .devcodelinesarea {\n    background: #ffd;\n  }\n  .devcodelinesareaodd {\n    background: #ffb;\n  }\n  .devcodetools {\n    background: transparent;\n    color: #000;\n    text-align: left;\n  }\n  .devcodetools span {\n    cursor: pointer;\n  }";
  }
  $DevFmt_DefaultPublicCSSSummer = devfmt_DefaultPublicCSSSummer();

  function devfmt_DevFmtAvaliableLangs(){
    return Array(
      "4cs.php:GADV 4CS","6502acme.php:MOS 6502 (6510) ACME Cross Assembler format","6502kickass.php:MOS 6502 (6510) Kick Assembler format","6502tasm.php:MOS 6502 (6510) TASM/64TASS 1.46 Assembler format","68000devpac.php:Motorola 68000 - HiSoft Devpac ST 2 Assembler format","abap.php:ABAP","actionscript.php:ActionScript","actionscript3.php:ActionScript 3","ada.php:Ada","algol68.php:ALGOL 68","apache.php:Apache configuration","applescript.php:AppleScript","apt_sources.php:Apt sources","arm.php:ARM ASSEMBLER","asm.php:ASM","asp.php:ASP","asymptote.php:asymptote","autoconf.php:Autoconf","autohotkey.php:Autohotkey","autoit.php:AutoIt","avisynth.php:AviSynth","awk.php:awk","bascomavr.php:BASCOM AVR","bash.php:Bash","basic4gl.php:Basic4GL","bf.php:Brainfuck","bibtex.php:BibTeX","blitzbasic.php:BlitzBasic","bnf.php:bnf","boo.php:Boo","c.php:C","caddcl.php:CAD DCL","cadlisp.php:CAD Lisp","cfdg.php:CFDG","cfm.php:ColdFusion","chaiscript.php:ChaiScript","cil.php:CIL","clojure.php:Clojure","cmake.php:CMake","cobol.php:COBOL","coffeescript.php:CoffeeScript","cpp-qt.php:C++ (Qt)","cpp.php:C++","csharp.php:C#","css.php:CSS","cuesheet.php:Cuesheet","c_loadrunner.php:C (LoadRunner)","c_mac.php:C (Mac)","d.php:D","dcl.php:DCL","dcpu16.php:DCPU-16 Assembly","dcs.php:DCS","delphi.php:Delphi","diff.php:Diff","div.php:DIV","dos.php:DOS","dot.php:dot","e.php:E","ecmascript.php:ECMAScript","eiffel.php:Eiffel","email.php:eMail (mbox)","epc.php:EPC","erlang.php:Erlang","euphoria.php:Euphoria","f1.php:Formula One","falcon.php:Falcon","fo.php:FO (abas-ERP)","fortran.php:Fortran","freebasic.php:FreeBasic","freeswitch.php:FreeSWITCH","fsharp.php:F#","gambas.php:GAMBAS","gdb.php:GDB","genero.php:genero","genie.php:Genie","gettext.php:GNU Gettext","glsl.php:glSlang","gml.php:GML","gnuplot.php:Gnuplot","go.php:Go","groovy.php:Groovy","gwbasic.php:GwBasic","haskell.php:Haskell","haxe.php:Haxe","hicest.php:HicEst","hq9plus.php:HQ9+","html4strict.php:HTML","html5.php:HTML5","icon.php:Icon","idl.php:Uno Idl","ini.php:INI","inno.php:Inno","intercal.php:INTERCAL","io.php:Io","j.php:J","java.php:Java","java5.php:Java(TM) 2 Platform Standard Edition 5.0","javascript.php:Javascript","jquery.php:jQuery","kixtart.php:KiXtart","klonec.php:KLone C","klonecpp.php:KLone C++","latex.php:LaTeX","lb.php:Liberty BASIC","ldif.php:LDIF","lisp.php:Lisp","llvm.php:LLVM Intermediate Representation","locobasic.php:Locomotive Basic","logtalk.php:Logtalk","lolcode.php:LOLcode","lotusformulas.php:Lotus Notes @Formulas","lotusscript.php:LotusScript","lscript.php:LScript","lsl2.php:LSL2","lua.php:Lua","m68k.php:Motorola 68000 Assembler","magiksf.php:MagikSF","make.php:GNU make","mapbasic.php:MapBasic","matlab.php:Matlab M","mirc.php:mIRC Scripting","mmix.php:MMIX","modula2.php:Modula-2","modula3.php:Modula-3","mpasm.php:Microchip Assembler","mxml.php:MXML","mysql.php:MySQL","nagios.php:Nagios","netrexx.php:NetRexx","newlisp.php:newlisp","nsis.php:NSIS","oberon2.php:Oberon-2","objc.php:Objective-C","objeck.php:Objeck Programming Language","ocaml-brief.php:OCaml (brief)","ocaml.php:OCaml","octave.php:GNU Octave","oobas.php:OpenOffice.org Basic","oorexx.php:ooRexx","oracle11.php:Oracle 11 SQL","oracle8.php:Oracle 8 SQL","oxygene.php:Oxygene (Delphi Prism)","oz.php:OZ","parasail.php:ParaSail","parigp.php:PARI/GP","pascal.php:Pascal","pcre.php:PCRE","per.php:per","perl.php:Perl","perl6.php:Perl 6","pf.php:OpenBSD Packet Filter","php-brief.php:PHP (brief)","php.php:PHP","pic16.php:PIC16","pike.php:Pike","pixelbender.php:Pixel Bender 1.0","pli.php:PL/I","plsql.php:PL/SQL","postgresql.php:PostgreSQL","povray.php:POVRAY","powerbuilder.php:PowerBuilder","powershell.php:PowerShell","proftpd.php:ProFTPd configuration","progress.php:Progress","prolog.php:Prolog","properties.php:PROPERTIES","providex.php:ProvideX","purebasic.php:PureBasic","pys60.php:Python for S60","python.php:Python","q.php:q/kdb+","qbasic.php:QBasic/QuickBASIC","rails.php:Rails","rebol.php:REBOL","reg.php:Microsoft Registry","rexx.php:rexx","robots.php:robots.txt","rpmspec.php:RPM Specification File","rsplus.php:R / S+","ruby.php:Ruby","sas.php:SAS","scala.php:Scala","scheme.php:Scheme","scilab.php:SciLab","sdlbasic.php:sdlBasic","smalltalk.php:Smalltalk","smarty.php:Smarty","spark.php:SPARK","sparql.php:SPARQL","sql.php:SQL","stonescript.php:StoneScript","systemverilog.php:SystemVerilog","tcl.php:TCL","teraterm.php:Tera Term Macro","text.php:Text","thinbasic.php:thinBasic","tsql.php:T-SQL","typoscript.php:TypoScript","unicon.php:Unicon (Unified Extended Dialect of Icon)","upc.php:UPC","urbi.php:Urbi","uscript.php:Unreal Script","vala.php:Vala","vb.php:Visual Basic","vbnet.php:vb.net","vedit.php:Vedit macro language","verilog.php:Verilog","vhdl.php:VHDL","vim.php:Vim Script","visualfoxpro.php:Visual Fox Pro","visualprolog.php:Visual Prolog","whitespace.php:Whitespace","whois.php:Whois (RPSL format)","winbatch.php:Winbatch","xbasic.php:XBasic","xml.php:XML","xorg_conf.php:Xorg configuration","xpp.php:X++","yaml.php:YAML","z80.php:ZiLOG Z80 Assembler","zxbasic.php:ZXBasic");
  }

  function devfmt_ReadConfig(){
    GLOBAL $DevFmt_Config, $DevFmt_SuppLangs;
    $DevFmt_Config = get_option("devfmt_options");
		if(!is_array($DevFmt_Config))
			$DevFmt_Config = unserialize($DevFmt_Config);
  }

  function devfmt_UpdateConfig(){
    GLOBAL $DevFmt_Config;
    update_option("devfmt_options", $DevFmt_Config);
    devfmt_ReadConfig();
  }

  function devfmt_ReadSupportedLangs(){
    GLOBAL $DevFmt_Config, $DevFmt_SuppLangs;
		$DevFmt_SuppLangs = DevGeSHi::langFiles();
		if(!IsSet($DevFmt_Config['favLangs']))
			$DevFmt_Config['favLangs'] = array();
			
		if(!is_array($DevFmt_Config['favLangs']))
			$DevFmt_Config['favLangs'] = array();
			
    foreach($DevFmt_SuppLangs as $key => $Lang){
      $file = substr($Lang['langfile'], 0, strpos($Lang['langfile'], "."));
      if(array_search($file, $DevFmt_Config['favLangs']) !== false)
        $DevFmt_SuppLangs[$key]['fav'] = '1';
    }
  }

  function devfmt_ReadConfigAndLangs(){
    devfmt_ReadConfig();
    devfmt_ReadSupportedLangs();
  }

  function devfmt_saveContent(&$AContent, $AFormatedArea){
    $FormatedAreas = array(uniqid(''), $AFormatedArea);
    while(preg_match("#".$AFormatedArea."#s", $AContent, $matches)){
      array_shift($matches);
      $FormatedAreas[] = $matches;
      $AContent = devfmt_PregReplace("#".$AFormatedArea."#s", $FormatedAreas[0].(Count($FormatedAreas) - 2)."_", $AContent, 1);
    }
    return $FormatedAreas;
  }

  function devfmt_recoverContent(&$AContent, $AFormatedAreas){
    for($i = 2; $i < Count($AFormatedAreas); $i++){
      while(preg_match("#".$AFormatedAreas[0].($i - 1)."_#", $AContent, $matches)){
        $formated = $AFormatedAreas[1];
        foreach($AFormatedAreas[$i] as $tmp)
          $formated = devfmt_PregReplace("#\(.*?\)#", $tmp, $formated, 1);

        $AContent =  devfmt_PregReplace("#".$AFormatedAreas[0].($i - 1)."_#", $formated, $AContent, 1);
      }
    }
  }

  function devfmt_PregReplace($ASearch, $AReplace, $AString, $ACount = null){
    return preg_replace($ASearch, str_replace(Array("\\", "\$"), Array("\\\\", "\\$"), $AReplace), $AString, $ACount);
  }

  function devfmt_StrReplace($ASearch, $AReplace, $AString, $ACount = -1, $AOffset = -1){
    if(($ACount == -1) && ($AOffset == -1)){
      $res = str_replace($ASearch, $AReplace, $AString);
    }else if($ASearch != ""){
      $res = $AString;
      $rl = strlen($ASearch);

      $i = 1;
      while(($ACount != -1) ? ($i <= $ACount) : true){
        $p = ($AOffset == -1) ? strpos($res, $ASearch) : strpos($res, $ASearch, $AOffset);

        if($p !== false)
          $res = substr($res, 0, $p) . $AReplace . substr($res, $p + $rl);
        else
          break;

        $i++;
      }
    }else
      $res = $AString;
    return $res;
  }

  function devfmt_StrBetween($AInit, $AEnd, $AString, $AOffset = -1){
    $init = ($AOffset == -1)? strpos($AString, $AInit): strpos($AString, $AInit, $AOffset);
    if($init !== false){
      $init += strlen($AInit);
      $end = strpos($AString, $AEnd, $init);
      return substr($AString, $init, $end - $init);
    }else
      return false;
  }

  function devfmt_getLangTitle($ALang){
    GLOBAL $DevFmt_SuppLangs;
    $res = DevGeSHi::langNeeded($ALang);
    if($res > 0){
      return DevGeSHi::langFileTitle($ALang.".php");
    }else{
      foreach($DevFmt_SuppLangs as $Lang)
        if(strtolower($Lang['langfile']) == strtolower($ALang.".php"))
          return $Lang['langname'];

      devfmt_ReadSupportedLangs();
      return DevGeSHi::langFileTitle($ALang.".php");
    }
  }

  function devfmt_RunDefaultFilters($AFilter, $AContent, $ACheckAvoid){
    GLOBAL $DevFmt_DefaultFilters, $DevFmt_FiltersToAvoid;
    if(is_array($DevFmt_DefaultFilters) && IsSet($DevFmt_DefaultFilters[$AFilter]))
      foreach($DevFmt_DefaultFilters[$AFilter] as $key => $FilterGroup)
        foreach($FilterGroup as $FilterName => $FilterData){
          $canAdd = true;
          if($ACheckAvoid)
            $canAdd = !array_search($FilterName, $DevFmt_FiltersToAvoid);
          if($canAdd)
            if(!is_null($FilterData['function']))
              $AContent = call_user_func_array($FilterData['function'], Array($AContent));
        }
    return $AContent;
  }

?>
