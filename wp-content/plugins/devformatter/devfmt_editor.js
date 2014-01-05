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
devfmt_EditorCtrl = function(Editor){
  sUniq = "";
  for(i = 0; i < 10; i++){
    r = Math.floor(Math.random() * 36);
    sUniq += "abcdefghijklmnopqrstuvwxyz1234567890".substring(r, r + 1);
  }
  this.uniqid = sUniq;
  this.flag = "<!--wp_fromhtmlpreview_devfmt-->";
  this.codeblocks = new Array();

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
    content = content.replace(/(<(pre|code|samp|script)[^>]*>)([\s\S]+?)(<\/\2>)/g, function(a, a1, a2, a3, a4){
      return a1 + devfmtEditorCtrl.saveBlock(a3) + a4;
    });
    return content;
  }

  this.parseEnd = function(content, wpautop){
    parseBlock = (wpautop)? (content.indexOf(devfmtEditorCtrl.flag) == -1) : (content.indexOf(devfmtEditorCtrl.flag) != -1);
    if(!wpautop)
      content = content.replace(new RegExp(devfmtEditorCtrl.flag, 'g'), "");
    else
      content = devfmtEditorCtrl.flag + content;

    content = content.replace(/(<(pre|code|samp|script)[^>]*>)([\s\S]+?)(<\/\2>)/g, function(a, a1, a2, a3, a4){
      block = devfmtEditorCtrl.getBlock(a3);
      if(parseBlock){
        if((a2.toLowerCase() != "code") || (!wpautop))
          block = block.replace(/<br ?\/?>[\r\n]*/gi, '\n').replace(/<\/?p( [^>]*)?>[\r\n]*/gi, '\n');
        entities = '160,nbsp, ,38,amp,&,162,cent,¢,8364,euro,€,163,pound,£,165,yen,¥,169,copy,©,174,reg,®,8482,trade,™,8240,permil,‰,60,lt,<,62,gt,>,176,deg,°,8722,minus,-'.split(',');
        if(wpautop){
          for(i = 0; i < entities.length - 1; i += 3)
            block = block.replace(new RegExp(entities[i + 2], 'g'), '{{DVFMTSC}}' + entities[i + 1] + ';');
          block = block.replace(/{{DVFMTSC}}/gi, '<!--DVFMTSC-->&').replace(/\n/gi, "<br />");
        }else{
          for(i = 0; i < entities.length - 1; i += 3)
            block = block.replace(new RegExp('<!--DVFMTSC-->&' + entities[i + 1] + ';', 'g'), entities[i + 2]);
        }
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
		c.data = devfmtEditorCtrl.parseEnd(a._wp_Nop(devfmtEditorCtrl.parseInit(c.data)), false);
		jQuery("body").trigger("afterPreWpautop", [c]);

    for(k in edButtons)
      if(edButtons[k])
        if(edButtons[k].id == 'code')
          edCodeId = k;

    jQuery("input#qt_content_code").bind("click", function(){
      //ed_codeInsert(edCanvas, edCodeId);
    });

		return c.data;
  };

  this.wpautop =  function(b){
		var a = this,
			c = {
				o: a,
				data: b,
				unfiltered: b
			};
		jQuery("body").trigger("beforeWpautop", [c]);
		c.data = devfmtEditorCtrl.parseEnd(a._wp_Autop(devfmtEditorCtrl.parseInit(c.data)), true);
		jQuery("body").trigger("afterWpautop", [c]);
		return c.data;
  };

  Editor.pre_wpautop = this.pre_wpautop;
  Editor.wpautop = this.wpautop;
}

jQuery(document).ready(function(){
	if(typeof(switchEditors) != "undefined"){
    devfmtEditorCtrl = new devfmt_EditorCtrl(switchEditors);
	}
});

