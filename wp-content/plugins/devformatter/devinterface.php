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

  function devfmt_addPage(){
    GLOBAL $DevFmt_Config, $DevFmt_ParsedCode, $DevFmt_SuppLangs;

    if(!current_user_can('manage_options'))
      return false;
    
    devfmt_ReadConfigAndLangs();

    if(isSet($_POST)){
      if(IsSet($_POST['UpdateConfig'])){      
        check_admin_referer('devfmt-update-config');
      
        $DevFmt_Config['geshilangpath'] = str_replace("\\\\", "\\", $_POST['geshilangpath']);
        $DevFmt_Config['geshiuselink'] = (IsSet($_POST['geshiuselink'])? "1": "0");
        $DevFmt_Config['displaylinenumbers'] = (IsSet($_POST['displaylinenumbers'])? "1": "0");
        $DevFmt_Config['usedevformat'] = (IsSet($_POST['usedevformat'])? "1": "0");
        $DevFmt_Config['devfmtcss'] = $_POST['devfmtcss'];
        $DevFmt_Config['linkjquery'] = (IsSet($_POST['linkjquery'])? "1": "0");
        $DevFmt_Config['highlighttags'] = (IsSet($_POST['highlighttags'])? "1": "0");
        $DevFmt_Config['copyclipboartext'] = $_POST['copyclipboartext'];
        $DevFmt_Config['hookrss2'] = (IsSet($_POST['hookrss2'])? "1": "0");
        $DevFmt_Config['parsepre'] = (IsSet($_POST['parsepre'])? "1": "0");
        $DevFmt_Config['showtools'] = (IsSet($_POST['showtools'])? "1": "0");
        $DevFmt_Config['useajaxparse'] = (IsSet($_POST['useajaxparse'])? "1": "0");

        devfmt_UpdateConfig();
      }
    }

    if($DevFmt_Config['geshilangpath'] == "")
      $DevFmt_Config['geshilangpath'] = DEVFMT_GESHIPATH;
?>
	<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
		<h2>DevFormatter <span style="font-size:15px"><em><?php echo devfmt_Version(); ?></em></span></h2>
    <h3>Configurations</h3>
    <form method="post" action="options-general.php?page=devformatter/devformatter.php">
    <?php wp_nonce_field('devfmt-update-config'); ?>
    <fieldset class="options">
    <table>
    <tbody>
    <tr valign="top">
    <th scope="row" align="right"><br />DevFormatter:</th>
    <td>
      <table class="form-table">
      <tbody>

      <tr valign="top">
      <td scope="row"><strong><input name="usedevformat" type="checkbox"<?php echo (($DevFmt_Config['usedevformat'])? " checked": ""); ?>/> <label for="usedevformat">Use DevFormatter System</label></strong><br />
      Checkout the diference:<br />
      <table width="100%"><tbody><tr>
      <td valign="top">
      <?php
        $ExampleCode = "Here is a sample of my <strong>PHP code</strong>:
<code lang=\"php\">  function GimmeGimmeAText(){
    return \"Take here, a text.\";
  }
  echo GimmeGimmeAText();</code>
Can you take it?
";
      ?>
      <strong>Page/Post content:</strong>
      <pre style="background:#fff;padding:4px;border:1px solid #ccc"><?php echo htmlentities($ExampleCode); ?></pre>
      </td>
      </tr><tr>
      <td valign="top">
      <strong>DevFormatter display:</strong>
      <div style="background:#fff;padding:4px;border:1px solid #ccc" lang="PHP"><?php
        $DevFmt_ParsedCode = devfmt_ContentGrabCode($ExampleCode);
        $ContentCheck = devfmt_ContentCheckCode($DevFmt_ParsedCode[0], true, null);
        
        echo $ContentCheck[1];
      ?></div>
      <script language="JavaScript"><!--
        if(typeof(jQuery) != "undefined"){
          jQuery(function($){ devfmt_createTools('devcode'); })
        }else
          alert("jQuery problem.");
      //--></script>
      </td>
      </tr><tr>
      <td valign="top">
      <strong>WordPress display:</strong>
      <div style="background:#fff;padding:4px;border:1px solid #ccc;" lang="PHP"><?php
        $DevFmt_ParsedCode = devfmt_ContentGrabCode($ExampleCode);
        $ContentCheck = devfmt_ContentCheckCode($DevFmt_ParsedCode[0], true, null);
        if($ContentCheck[0])
          echo wpautop(wptexturize($ContentCheck[1]));
      ?></div>
      </td>
      </tr></tbody></table>
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><input name="displaylinenumbers" type="checkbox"<?php echo (($DevFmt_Config['displaylinenumbers'])? " checked": ""); ?>/> <label for="displaylinenumbers">Display Line Numbers</label></strong><br />
      This option can be overwrited by forcing line display selecting "<strong>Display Line numbers?</strong>" on the page/post Editor.
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><label for="copyclipboartext">Copy to Clipboard Text</label></strong><br />
      <input name="copyclipboartext" type="text" value="<?php echo $DevFmt_Config['copyclipboartext']; ?>" size="80" />
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><input name="showtools" type="checkbox"<?php echo (($DevFmt_Config['showtools'])? " checked": ""); ?>/> <label for="showtools">Show toolbar before code</label></strong><br />
      This option will create a toolbar with copy code and plain display buttons.
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><label for="copyclipboartext">Custom CSS</label></strong><br />
      <textarea name="devfmtcss" rows="6" cols="60"><?php echo $DevFmt_Config['devfmtcss']; ?></textarea>
      <br /><input type="button" value="Default CSS" onclick="devfmt_cssdefault()">&nbsp;-&nbsp;<input type="button" value="Blue Theme CSS" onclick="devfmt_cssdefaultblue()">&nbsp;-&nbsp;<input type="button" value="Summer Theme CSS" onclick="devfmt_cssdefaultsummer()">
      <br />If you have recently updated DevFormatter click <strong>"Default CSS" or "Blue Theme CSS"</strong> to get new improvements on CSS layout configurations.
      <script language="JavaScript"><!--
        devfmt_cssdefault = function(){ document.forms[0].devfmtcss.value = "<?php echo str_replace("\n", "\\r\\n\" + \r\n \"", devfmt_DefaultPublicCSS()) ?>"; }
        devfmt_cssdefaultblue = function(){ document.forms[0].devfmtcss.value = "<?php echo str_replace("\n", "\\r\\n\" + \r\n \"", devfmt_DefaultPublicCSSBlue()) ?>"; }
        devfmt_cssdefaultsummer = function(){ document.forms[0].devfmtcss.value = "<?php echo str_replace("\n", "\\r\\n\" + \r\n \"", devfmt_DefaultPublicCSSSummer()) ?>"; }
      //--></script>
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><input name="hookrss2" type="checkbox"<?php echo (($DevFmt_Config['hookrss2'])? " checked": ""); ?>/> <label for="hookrss2">Format code for with RSS 2.0</label></strong><br />
      This option will format blocks of code on RSS 2.0 output (Direct RSS2.0 entry only with IE7+).
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><input name="parsepre" type="checkbox"<?php echo (($DevFmt_Config['parsepre'])? " checked": ""); ?>/> <label for="parsepre">Parse PRE Tags with language attribute as code block</label></strong><br />
      Format PRE Tag with language attribute as blocks of code (Compatibility with some Writer applications as Live Writer).
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><input name="linkjquery" type="checkbox"<?php echo (($DevFmt_Config['linkjquery'])? " checked": ""); ?>/> <label for="linkjquery">Create a link for jQuery</label></strong><br />
      You need to enable this option if you don't already use jQuery on your blog.
      <br /><small><em>Credits for jQuery: <a href="http://jquery.com/">http://jquery.com/</a></em></small>
      </td>
      </tr>

      <tr valign="top">
      <td scope="row"><strong><input name="useajaxparse" type="checkbox"<?php echo (($DevFmt_Config['useajaxparse'])? " checked": ""); ?>/> <label for="useajaxparse">Use AJAX DevFormatter System</label></strong><br />
      You need to enable this option to format the code with the parse ajax system, active it when your WP become slow with the current settings.<br />
      <em>(Is really recommended to use a Cache Plugin to get a better performance)</em>
      </td>
      </tr>

      </tbody>
      </table>
    </td>
    </tr>

    <tr valign="top">
    <th scope="row" align="right"><br />GeSHI:</th>
    <td>
      <table class="form-table">
      <tbody>
      <tr valign="top">
      <td scope="row"><strong><label for="geshilangpath">GeSHI Languages Path</label></strong><br />
      <input name="geshilangpath" type="text" value="<?php echo $DevFmt_Config['geshilangpath']; ?>" size="80" />
      <br />Your default path = <?php echo DEVFMT_GESHIPATH ?>
      <br /><span style="color:red">If the directory permission block php to write files, you'll have to upload files to the site ftp yourself (Get the <input type="button" onclick="javascript:window.open('http://svn.wp-plugins.org/devformatter/branches/langs/langs.zip')" value="full language pack here"/> if you want)</span>.
      </td>
      </tr>
      <tr valign="top">
      <td scope="row"><strong><input name="geshiuselink" type="checkbox"<?php echo (($DevFmt_Config['geshiuselink'])? " checked": ""); ?>/> <label for="geshiuselink">Display Links on reserved words</label></strong><br />
      Create a link on a reserved work of the language to a website helper.
      </td>
      </tr>
      <tr valign="top">
      <td scope="row"><strong><label for="geshiuselink">Downloaded Languages</label></strong><br />
      You have downloaded <strong><?php echo Count($DevFmt_SuppLangs) ?></strong> of <?php echo Count(devfmt_DevFmtAvaliableLangs()) ?> supported languages.
      <div style="background:#fff;border:1px solid #ccc;height:200px;overflow:auto">
<?php
    $FavLangs = Array();
    foreach($DevFmt_SuppLangs as $Lang){
        if($Lang['fav'])
          $FavLangs[] = $Lang['langname'];
        echo "<li><strong>&middot;".$Lang['langname']."</strong><br /><dd>
        ".DEVFMT_GESHIPATH.DIRECTORY_SEPARATOR.$Lang['langfile']."</dd></li>";
    }
?>
      </div>
      <strong>Favorite language(s):</strong>
      '<?php echo implode("', '", $FavLangs); ?>'
      <br /><small><em>Credits for GeSHi: <a href="http://qbnz.com/highlighter/">http://qbnz.com/highlighter/</a></em></small>
      </td>
      </tr>
      </tbody>
      </table>
    </td>
    </tr>

    </tbody>
    </table>
    </fieldset>
    <p class="submit"><input type="submit" name="UpdateConfig" class="button-primary" value="Save Changes" /></p>
    </form>
  </div>
<?php
  }

  function devfmt_addPages(){
  	add_options_page('DevFormatter', 'DevFormatter', 2, DEVFMT_PATH.'devformatter.php', 'devfmt_addPage');
  }

  function devfmt_addInterface(){
    GLOBAL $DevFmt_Config, $DevFmt_SuppLangs;

    devfmt_ReadConfigAndLangs();

    if(!current_user_can('edit_posts') && !current_user_can('edit_pages'))
      return;

    if(function_exists('load_plugin_textdomain'))
      load_plugin_textdomain('devformatter','/wp-content/plugins/devformatter/langs');
    add_filter("mce_external_plugins", "devfmt_addMCEPlugin", 0);
    add_filter("mce_buttons_2", "devfmt_addMCEButton", 0);

    add_action('edit_page_form', 'devfmt_addHTMLButton');
    add_action('edit_form_advanced', 'devfmt_addHTMLButton');
  }

  function devfmt_addMCEButton($AButtons){
    $charmapIndex = array_search('charmap', $AButtons);
    $res = array();
    for($i = 0; $i < Count($AButtons); $i++){
      $res[] = $AButtons[$i];
      if($i == $charmapIndex)
        $res[] = "devformatter";
    }

    return $res;
  }

  function devfmt_addMCEPlugin($AExtPlugins){
  	if(is_array($AExtPlugins) == false)
  		$AExtPlugins = array();

  	return array_merge($AExtPlugins,
      array("devformatter" => DEVFMT_URL."devfmt_edit_plugin.js?ver=".devfmt_Version())
    );
  }

  function devfmt_addHTMLButton(){
    GLOBAL $DevFmt_Config, $DevFmt_SuppLangs;

    $Langs = devfmt_DevFmtAvaliableLangs();
    foreach($Langs as $key => $lang){
      $d = explode(":", $lang);
      $d[0] = array_shift(explode(".", $d[0]));
      $f = (array_search($d[0], $DevFmt_Config['favLangs']) !== false)? "1" : "0";
      $Langs[$key] = "{n:'".$d[1]."',s:'".$d[0]."',f:".$f."}";
    }

    echo "
  <script language=\"JavaScript\">
  // <![CDATA[
    Langs = new Array(".implode(",", $Langs).");
    HtmlEditor = true;
    ButtonIndex = -1;
    SelectedLangIndex = -1;
    EditorSelection = '';

    document.write(\"<div id='devfmt_langselector'></div>\");
    DevFmt_WriteDefaults = function(){
      jQuery('#devfmt_langselector').html('');
      sHTML = \"<center>\";
      sHTML += \"<table id='devfmt_langselectorarea'><tbody><tr><td>\";
      sHTML += \"<table width='100%'><tbody><tr><td valign='top'>\";
      sHTML += \"<div class='devfmt_lsleft'>\";

      sHTML += \"<h3>Your favorites Languages</h3><select name='devfmt_langfavselection' size='7' onchange='DevFmt_ShowLangInfo(this)' multiple>\";
      HasFav = false;
      for(i = 0; i < Langs.length; i++)
        if(Langs[i].f){
          HasFav = true;
          sHTML += \"<option value='\" + Langs[i].s +\"'>\" + Langs[i].n +\"</option>\";
        }
      sHTML += \"</select>\";
      sHTML += \"</div></td><td valign='top'><div class='devfmt_lsright'>\";
      sHTML += \"<h3>Select a Language</h3><select name='devfmt_langselection' size='7' onchange='DevFmt_ShowLangInfo(this)' multiple>\";
      for(i = 0; i < Langs.length; i++)
        sHTML += \"<option value='\" + Langs[i].s +\"'>\" + Langs[i].n +\"</option>\";
      sHTML += \"</select>\";
      sHTML += \"</div>\";
      sHTML += \"</td></tr></tbody></table>\";

      sHTML += \"<table id='DevFmt_LangInfo'><tbody>\" +
        \"<tr><td width='120px'>Name:</td><td class='langname'>...</td></tr>\" +
        \"<tr><td>Identification:</td><td class='langident'>...</td></tr>\" +
        \"</tbody></table>\";
      sHTML += \"<table id='devfmt_langselectorarea'><tbody><tr>\";
      sHTML += \"<td style='width:120px;font-weight:bold' id='tab_devfmt_code'>Code</td>\";
      sHTML += \"<td style='width:120px' id='tab_devfmt_preview'>Preview</td>\";
      sHTML += \"</tr></tbody></table><table width='100%' id='devfmt_langselectorarea'><tbody><tr>\";
      sHTML += \"<div id='devfmt_codeeditor'>\";
      sHTML += \"<textarea id='devfmt_content'>\" + EditorSelection + \"</textarea>\";
      sHTML += \"</div>\";
      sHTML += \"<div id='devfmt_preview' style='display:none'>\";
      sHTML += \"<div id='devfmt_previewcode'></div>\";
      sHTML += \"</div></tr></tbody></table>\";
      sHTML += \"</td></tr></tbody></table>\";

      UseCaption = (EditorSelection == \"\")? \"Insert the code\" : \"Update to code format\";

      sHTML += \"<div style='position:relative;width:500px;text-align:left'>\";
      sHTML += \"<input type='checkbox' id='devfmt_forcelines' style='margin-left:32px;margin-right:12px'/> Display Line numbers? <em>(Note: Force lines count to be displayed )</em><br />\";
      sHTML += \"<input type='checkbox' id='devfmt_notools' style='margin-left:32px;margin-right:12px'/> Hide toolbar? <em>(Note: Force toolbar to be hided )</em><br />\";
      sHTML += \"<input type='text' id='devfmt_startlinenum' value='1' style='width:60px'/> Start line number  <em>(Note: Change the start number of lines )</em><br />\";
      sHTML += \"</div>\";
      sHTML += \"<hr><input type='button' class='button-secondary' value='\" + UseCaption + \"' onclick='DevFmt_SelectLang()'/>\";
      sHTML += \"</center>\";

      jQuery('#devfmt_langselector').html(sHTML);

      if(HasFav){
        jQuery('select[name=devfmt_langfavselection]')[0].selectedIndex = 0;
        DevFmt_ShowLangInfo(jQuery('select[name=devfmt_langfavselection]')[0]);
      }

      jQuery('#tab_devfmt_code').click(function(){
        jQuery('#devfmt_preview').hide();
        jQuery('#devfmt_content').show();
        jQuery(this).css('font-weight','bold');
        jQuery('#tab_devfmt_preview').css('font-weight','normal');
      });

      jQuery('#tab_devfmt_preview').click(function(){
        if(SelectedLangIndex != -1){
          jQuery('#devfmt_content').hide();
          jQuery('#devfmt_preview').show();
          jQuery(this).css('font-weight','bold');
          jQuery('#tab_devfmt_code').css('font-weight','normal');
          jQuery.post('".DEVFMT_URL."devfmt_ajax.php',
            {
              'preview': jQuery('textarea#devfmt_content')[0].value,
              'preview_lang': Langs[SelectedLangIndex].s,
              'preview_lines': ((jQuery('#devfmt_forcelines')[0].checked)? 'true' : ''),
              'preview_sline': jQuery('input#devfmt_startlinenum')[0].value,
              'preview_notools': ((jQuery('#devfmt_notools')[0].checked)? 'true' : '')
            },
            function(data){
              jQuery('#devfmt_preview div').html(data);
            }
          );
        }else{
          alert('Select a language.');
        }
      });
    }

    DevFmt_ShowSelector = function(){
      DevFmt_WriteDefaults();
      tb_show('DevFormatter','?TB_inline=1&modal=false&inlineId=devfmt_langselector');

      jQuery('#TB_ajaxContent').css('width', '');
      jQuery('#TB_ajaxContent').css('height', '90%');
      jQuery('#TB_ajaxContent').css('overflow', 'scroll');
    }

    DevFmt_IndexOfLang = function(ALangIdent){
      for(i = 0; i < Langs.length; i++)
        if(Langs[i].s == ALangIdent)
          return i;
      return -1;
    }

    DevFmt_ShowLangInfo = function(ASelect){
      if(ASelect.name == 'devfmt_langfavselection'){
        jQuery('select[name=devfmt_langselection]')[0].selectedIndex = -1;
      }else{
        jQuery('select[name=devfmt_langfavselection]')[0].selectedIndex = -1;
      }
      SelectedLangIndex = DevFmt_IndexOfLang(ASelect.value);
      jQuery('#DevFmt_LangInfo .langname').html(Langs[SelectedLangIndex].n);
      jQuery('#DevFmt_LangInfo .langident').html(ASelect.value);
    }

    DevFmt_SelectLang = function(){
      if((jQuery('select[name=devfmt_langselection]')[0].selectedIndex == -1) &&
        (jQuery('select[name=devfmt_langfavselection]')[0].selectedIndex == -1)){
        alert('Please, select a language first.');
        return false;
      }
      tb_remove();
      Langs[SelectedLangIndex].f = true;

      DevFmt_ConfigExts = new Array();
      if(jQuery('#devfmt_forcelines')[0].checked){
        DevFmt_ConfigExts[DevFmt_ConfigExts.length] = 'lines';
      }

      if((jQuery('input#devfmt_startlinenum')[0].value * 1) > 1){
        DevFmt_ConfigExts[DevFmt_ConfigExts.length] = 'sl:' + jQuery('input#devfmt_startlinenum')[0].value;
      }

      if(jQuery('#devfmt_notools')[0].checked){
        DevFmt_ConfigExts[DevFmt_ConfigExts.length] = 'notools';
      }

      if(DevFmt_ConfigExts.length == 0){
        DevFmt_ConfigExt = '';
      }else{
        DevFmt_ConfigExt = '[' + DevFmt_ConfigExts.join('-') + ']';
      }

      DevFmt_ContentStart = '<code lang=\"' + Langs[SelectedLangIndex].s + DevFmt_ConfigExt + '\">';
      DevFmt_TheContent = '' + jQuery('textarea#devfmt_content')[0].value;
      DevFmt_ContentEnd = '</code>';
      if(HtmlEditor){
        edInsertContent(edCanvas, DevFmt_ContentStart + DevFmt_TheContent + DevFmt_ContentEnd);
      }else{
        alert(DevFmt_ContentStart + DevFmt_TheContent + DevFmt_ContentEnd);
        tinyMCE.execInstanceCommand('content', 'mceReplaceContent', false,
          switchEditors.wpautop(DevFmt_ContentStart + DevFmt_TheContent + DevFmt_ContentEnd));
      }

      DevFmt_WriteDefaults();

      sFavs = '';
      for(i = 0; i < Langs.length; i++)
        if(Langs[i].f){
          if(sFavs != '') sFavs += '-';
          sFavs += Langs[i].s;
        }
      jQuery.post('".DEVFMT_URL."devfmt_ajax.php', {'favs': sFavs});
    }

    function DevFmt_ShowSelectorMCE(){
      HtmlEditor = false;
      ButtonIndex = -1;
      EditorSelection = tinyMCE.activeEditor.selection.getContent({format : 'text'});
      DevFmt_ShowSelector();
    }

    function ed_selectedcode(){
    	if(document.selection){
    		edCanvas.focus();
    	  return document.selection.createRange();
    	}else if (edCanvas.selectionStart || edCanvas.selectionStart == '0') {
    		startPos = edCanvas.selectionStart;
        return edCanvas.value.substring(startPos, edCanvas.selectionEnd);
      }
    }

    function ed_codeInsert(myField, i){
      HtmlEditor = true;
      ButtonIndex = i;
      EditorSelection = ed_selectedcode();
      DevFmt_ShowSelector();
    }

    jQuery(function($){
      $('#content').bind('onchange', function(){
        //alert('changed');
      });

      for(k in edButtons)
        if(edButtons[k])
          if(edButtons[k].id == 'ed_code')
            edCodeId = k;

      $(\"input#ed_code\").click(function(){
        ed_codeInsert(edCanvas, edCodeId);
      });
    });
  // ]]>
  </script>
    ";
  }

  function devfmt_addExtendsToMCE($AArConfig){
    $AArConfig['extended_valid_elements'] = "*[*]";
    $AArConfig['entities'] = "160,nbsp,38,amp,34,quot,162,cent,8364,euro,163,pound,165,yen,169,copy,174,reg,8482,trade,8240,permil,60,lt,62,gt,8804,le,8805,ge,176,deg,8722,minus";
    return $AArConfig;
  }

  function devfmt_commonHeader(){
    GLOBAL $DevFmt_Config;
    echo "<script language=\"JavaScript\"><!--\n var DevFmtUrl='".DEVFMT_URL."'; var DevFmtAjaxUrl = '".admin_url('admin-ajax.php')."'; //-->\n</script>\n";
    echo "<script type='text/javascript' src='".DEVFMT_URL."devfmt_common.js?ver=".devfmt_Version()."'></script>\n";
  }

  function devfmt_adminHeader(){
  	echo "<script type='text/javascript' src='".DEVFMT_URL."devfmt_editor.js?ver=".devfmt_Version()."'></script>";

    devfmt_commonHeader();

    echo "
    <style type='text/css'>
    #devfmt_langselector {
      font-size: 16px;
      display: none;
      z-index: 999;
    }
    #tab_devfmt_code, #tab_devfmt_preview {
      cursor: pointer;
    }
    #devfmt_content, #devfmt_previewcode {
      width: 100%;
      height: 200px;
      border: 1px solid #000;
      padding: 0;
      margin: 0;
      overflow: scroll;
    }
    #devfmt_preview div pre {
      padding: 0;
      margin: 0;
    }
    #devfmt_content, #DevFmt_LangInfo {
      width : 500px;
    }
    .devfmt_langselectorarea {
      width: 500px;
    }
    .devfmt_lsleft, .devfmt_lsright, .devfmt_lsleft select, .devfmt_lsright select {
      width: 250px;
    }
    .devcodeblock tr {
      background: transparent;
    }
    .devcodeblock, .devcodeblock tr, .devcodeblock tr td {
      left : 0;
      top : 0;
      border: 0;
      padding: 0;
      margin: 0;
    }
    </style>\n";
    devfmt_DevFmtCSS();
  }

  function devfmt_DevFmtCSS(){
    GLOBAL $DevFmt_Config;

    if(isSet($_POST) && ($_GET['page'] == "devformatter/devformatter.php"))
      if(IsSet($_POST['UpdateConfig']) && IsSet($_POST['devfmtcss']))
        $DevFmt_Config['devfmtcss'] = $_POST['devfmtcss'];

    echo "<style type=\"text/css\" media=\"screen\"> @import url( '".DEVFMT_URL."devfmt_reset.css?ver=".devfmt_Version()."' ); </style>\n";
    if(trim($DevFmt_Config[devfmtcss]) != ""){
      echo "<style type=\"text/css\" media=\"screen\"> @import url( '".DEVFMT_URL."devfmt_css.php?ver=".devfmt_Version()."' ); </style>";
    }
  }

  function devfmt_publicHeader(){
    GLOBAL $DevFmt_Config;
    devfmt_ReadConfig();
    if($DevFmt_Config['linkjquery'])
      echo "<script type='text/javascript' src='".get_option('siteurl')."/wp-includes/js/jquery/jquery.js?ver=1.2.6'></script>\n";

    devfmt_commonHeader();

    echo "<script type='text/javascript' src='".DEVFMT_URL."devfmt_public.js?ver=".devfmt_Version()."'></script>\n";
    devfmt_DevFmtCSS();
  }

  function devfmt_initEditor($AEditor){
		if('tinymce' == wp_default_editor()){
      $AEditor .= "<script language='JavaScript'><!--\n jQuery(document).ready(function(){ if(typeof(switchEditors) != \"undefined\"){ jQuery('textarea#content')[0].value = switchEditors.wpautop(jQuery('textarea#content')[0].value); } }); //-->\n</script>";
    }
    return $AEditor;
  }

  function devfmt_rollbackFormat($AContent){
    GLOBAL $post;
    return $post->post_content;
  }
?>
