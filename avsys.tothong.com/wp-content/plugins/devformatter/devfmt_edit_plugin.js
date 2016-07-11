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
(function() {

  // Load plugin specific language pack
  tinymce.PluginManager.requireLangPack('devformatter');

  tinymce.create('tinymce.plugins.devformatterPlugin', {
    init : function(ed, url) {
      var t = this;
      t.editor = ed;
      ed.addCommand('mce_devformatter', t._devformatter, t);
      ed.addButton('devformatter',{
        title : 'Insert a formatter code',
        cmd : 'mce_devformatter',
        image :  url + '/img/devformatter-button.png'
      });
    },

    getInfo : function() {
      return {
        longname : 'Developer Formatter system to WordPress',
        author : 'Gilberto Saraiva',
        authorurl : 'http://gsaraiva.projects.pro.br/',
        infourl : 'http://gsaraiva.projects.pro.br/',
        version : '1.1'
      };
    },

    // Private methods
    _devformatter : function(){ // open a popup window
      DevFmt_ShowSelectorMCE(this.editor);
      return false;
    }
  });

  // Register plugin
  tinymce.PluginManager.add('devformatter', tinymce.plugins.devformatterPlugin);
})();
