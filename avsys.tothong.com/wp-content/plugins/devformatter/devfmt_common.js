/*
Developer Formater
http://wordpress.org/extend/plugins/devformatter/
Developer Formatter system to WordPress. Powered by <a href="http://qbnz.com/highlighter/">GeSHi</a> server-side highlight system.
Version: 2015.0.2.1
Author: Gilberto Saraiva
Author URI: http://gsaraiva.projects.pro.br/

Copyright (c) 2013 Gilberto Saraiva
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
*/
devfmt_credits = function(){
  window.open('http://wordpress.org/extend/plugins/devformatter/');
}

devfmt_getCode = function(ABase, AIdent){
  code = "";
  jQuery(ABase).parent().parent().parent().parent().find("pre." + AIdent + "").each(function(){
    code += this[((document.all)? "innerText" : "textContent")] + "\n";
  });
  return code;
}

devfmt_createToolsIdent = "";
devfmt_createTools = function(AIdent){
  devfmt_createToolsIdent = AIdent;
  jQuery("." + AIdent + "block div.devcodetools").each(function(){
    if(!jQuery(this).find("div:eq(0)").data("devtoolenabled")){
      jQuery(this).find("div:eq(0)")
        .data("devtoolenabled", true)
        .css("cursor", "pointer")
        .attr("title", "Show Plain " + jQuery(this).attr("title") + " Code")
        .click(function(){
          devfmt_showPlainText(devfmt_getCode(this, AIdent));
        });
    }
  });
}

devfmt_showPlainText = function(ACode){
  sourceWnd = window.open("about:blank", "_blank", "location=0,resizable=1,menubar=0,scrollbars=0,top=0,left=0,width=600,height=350");
  with(sourceWnd){
    document.write("<body style=\"padding:0;margin:0;\" onload=\"selectAll()\"><script> selectAll = function(){ window.opener.devfmt_selectAllPlainText(window); } </script><textarea style=\"width:100%;height:100%\" id=\"source_code\" wrap=\"off\">" + ACode + "</textarea></body>");
    document.close();
  }
}

var
  devfmt_selectAllPlainTextEnable = true;

devfmt_selectAllPlainText = function(wnd){
  if(devfmt_selectAllPlainTextEnable)
    jQuery(wnd.document).find("TEXTAREA").select();
}

devfmt_cleanUpZeroClipboard = function(){
  jQuery("." + devfmt_createToolsIdent + "block table.devcodetools").each(function(){
    jQuery(this).find("td:eq(1)").html("");
  });
}

var
  devfmt_ajaxBuffer = new Array();

devfmt_addAjaxBuffer = function(ADevFmtId, APostId){
  devfmt_ajaxBuffer[devfmt_ajaxBuffer.length] = [ADevFmtId, APostId];
}

devfmt_parseAjaxBuffer = function(){
  for(i = 0; i < devfmt_ajaxBuffer.length; i++){
    var data = {
      action: 'devfmt_parseCode',
      dfPostId: devfmt_ajaxBuffer[i][1],
      dfId: devfmt_ajaxBuffer[i][0]
    };

    jQuery.post(DevFmtAjaxUrl, data, function(response){
      response = JSON.parse(response);
      jQuery("#devfmt_ajax_" + response.dfId + "_" + response.dfPostId).html(response.code);
      devfmt_createTools("devcode");
    });
  }
}

/*
  ZeroClipboard Flash 10 clipboard wrapper
  Simple Set Clipboard System
  Author: Joseph Huckaby
*/
ZeroClipboard = {
  dispatch: function(id, eventName, args){
    EmbedObj = jQuery("embed#ZeroClipboard" + id)[0];
    switch(eventName){
      case("load"):
        jQuery(EmbedObj).attr("title", "Double-click to copy");
        EmbedObj.setText(devfmt_getCode(jQuery(EmbedObj).parent()[0], "devcode"));
        break;
      case("mouseOut"):
        // ReCreate clipboard object
        pTD = jQuery(EmbedObj).parent();
        pTD.html(pTD.html());
        break;
      case("mouseDown"):
        if(jQuery.browser.msie){
          // avoid flash clipboard on ie
          window.clipboardData.setData("Text", devfmt_getCode(jQuery(EmbedObj).parent()[0], "devcode"));
        }
        break;
    }
  }
}

if(typeof(jQuery) != "undefined"){
  if(jQuery.browser.msie){
    window.attachEvent('onunload', devfmt_cleanUpZeroClipboard);
  }else{
    jQuery(window).unload(function(){ devfmt_cleanUpZeroClipboard() });
  }

  jQuery(function($){
    devfmt_parseAjaxBuffer();
  });
}else
  alert("jQuery link problem. Active the link on DevFormatter config page.");