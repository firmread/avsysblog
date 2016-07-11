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

devfmt_EditorCtrlPlainCodeButton = function(){
  if(typeof(edButtons) != "undefined"){
    for(k in edButtons)
      if(edButtons[k])
        if(edButtons[k].id == 'code')
          edCodeId = k;

    jQuery("input#qt_content_code").unbind("click");
    jQuery("input#qt_content_code").bind("click", function(){
      ed_codeInsert(edCanvas, edCodeId);
      return false;
    });
  }
};

strtoHex = function(str){
    var hex, i;

    var result = "";
    for (i=0; i<str.length; i++) {
        hex = str.charCodeAt(i).toString();
        result += "#"+("000"+hex).slice(-4);
    }

    return result
}

devfmt_EditorCtrl = function(Editor){
  sUniq = "";
  for(i = 0; i < 10; i++){
    r = Math.floor(Math.random() * 36);
    sUniq += "abcdefghijklmnopqrstuvwxyz1234567890".substring(r, r + 1);
  }
  this.uniqid = sUniq;
  this.flag = "<!--wp_fromhtmlpreview_devfmt-->";
  this.codeblocks = new Array();

  this.replacePlainCodeButton = function(){
    devfmt_EditorCtrlPlainCodeButton();
  }

  this.saveBlock = function(block){
    uid = ";;" + this.uniqid + this.codeblocks.length + ";;";
    this.codeblocks[this.codeblocks.length] = new Array(uid, block);
    return uid;
  }

  this.getBlock = function(uid){
    for(i = 0; i < this.codeblocks.length; i++)
      if(this.codeblocks[i][0] == uid)
        return this.codeblocks[i][1];
  }

  this.parseInit = function(content){
    this.codeblocks = new Array();
    content = content.replace(/(<(code)[^>]*>)([\s\S]+?)(<\/\2>)/g, function(a, a1, a2, a3, a4){
      return a1 + devfmtEditorCtrl.saveBlock(a3) + a4;
    });
    return content;
  }

  this.parseEnd = function(content, wpautop){
    fromPlainBlock = (content.indexOf(devfmtEditorCtrl.flag) != -1);
    if(!wpautop)
      content = content.replace(new RegExp(devfmtEditorCtrl.flag, 'g'), "");
    else
      content = devfmtEditorCtrl.flag + content;

    content = content.replace(/(<(code)[^>]*>)([\s\S]+?)(<\/\2>)/g, function(a, a1, a2, a3, a4){
      block = devfmtEditorCtrl.getBlock(a3);
      block = block.replace(new RegExp("<br />", 'g'), "\n");
      if(fromPlainBlock && !wpautop){
        block = block.replace(new RegExp("<!--DVFMTSC-->", 'g'), "");
      }else{
        block = block.replace(/ /gi, "<!--DVFMTSC-->&nbsp;");
        block = block.replace(/\n/gi, "<br />");
      }
      return a1 + block + a4;
    });
    
    return content;
  }

  // Overwrites on default editor.
  this.defpre_wpautop = Editor.pre_wpautop;
  this.defwpautop = Editor.wpautop;
  this.pre_wpautop = function(b){
    var a = this,
      c = {
        o: a,
        data: b,
        unfiltered: b
      };

    jQuery("body").trigger("beforePreWpautop", [c]);
    c.data = devfmtEditorCtrl.parseEnd(Editor._wp_Nop(devfmtEditorCtrl.parseInit(c.data)), false);
    jQuery("body").trigger("afterPreWpautop", [c]);

    devfmtEditorCtrl.replacePlainCodeButton();

    return c.data;
  }

  this.wpautop = function(b){
    var a = this || Editor,
      c = {
        o: a,
        data: b,
        unfiltered: b
      };

    jQuery("body").trigger("beforeWpautop", [c]);
    c.data = devfmtEditorCtrl.parseEnd(Editor._wp_Autop(devfmtEditorCtrl.parseInit(c.data)), true);
    jQuery("body").trigger("afterWpautop", [c]);

    return c.data;
  }

  window.wp.editor.autop = this.wpautop;
  window.wp.editor.removep = this.pre_wpautop;

  this.replacePlainCodeButton();
}

jQuery(document).ready(function(){
  //next to tinymce load buttons
  setTimeout(function(){
    if(typeof(window.switchEditors) != "undefined"){
      devfmtEditorCtrl = new devfmt_EditorCtrl(window.switchEditors);
    }else{
      devfmt_EditorCtrlPlainCodeButton();
    }
  }, 10);
});

