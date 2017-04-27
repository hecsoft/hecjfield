<?php
/**
 * @version     1.0.0
 * @package     com_kwpmessage
 * @copyright   Copyright (C) 2014. Tous droits réservés.
 * @license     GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 * @author      Hervé CYR <herve.cyr@kantarworldpanel.com> - 
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldFileXplorer extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'filexplorer';
	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected static $initialised = false;
	
	protected $parent_id=0;
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$doc = JFactory::getDocument();
		$app=JFactory::getApplication();
		
		$option=$app->input->get('option', '');
		$component_path = Juri::base(true)."/components/".$option;
		$fieldpath=$component_path."/models/fields/filexplorer";
		$curdir=$this->value;
		
		$doc->addStyleSheet($component_path."/libraries/datatables/datatables.min.css");
		$doc->addScript($component_path."/libraries/datatables/datatables.min.js");
		//$doc->addStyleSheet("//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css");
		if ($curdir=="") $curdir=$this->getAttribute("defaultdir",JPATH_SITE."/images");
		$rootlabel=$this->getAttribute("rootlabel","<span class=\"icon-home\"></span>");
		$filter=$this->getAttribute("filter","");
		$modeatt=$this->getAttribute("mode","file&folder");
		$webservicecontroller=$this->getAttribute("controller","webservice");
		$mode=0;
		if (strstr( $modeatt,"file")>=0) $mode+=1;
		if (strstr( $modeatt,"folder")>=0) $mode+=2;
		$exclude=$this->getAttribute("exclude","");
		$refreshurl = $this->getAttribute("refreshurl",JUri::base(true)."/index.php?task=".$webservicecontroller.".getdir&option=".$option);
		$removeurl=$this->getAttribute("removeurl",Juri::base(true)."/index.php?option=".$option."&task=".$webservicecontroller.".delete");
		$renameurl=$this->getAttribute("renameurl",Juri::base(true)."/index.php?option=".$option."&task=".$webservicecontroller.".rename");
		$uploadurl=$this->getAttribute("uploadurl",Juri::base(true)."/index.php?option=".$option."&task=".$webservicecontroller.".upload");
		$txt_remove_file_title = JText::_("JFORMFIELD_FILEXPLORER_DELETE_FILE_TITLE");
		$txt_remove_file_label=JText::_("JFORMFIELD_FILEXPLORER_DELETE_FILE_LABEL");
		$txt_error_title="Erreur";
		$searching=$this->getAttribute("searching","false");
		$ordering=$this->getAttribute("ordering","true");
		$class=$this->getAttribute("class","jformfieldfilexplorer");
		
		$height=$this->getAttribute("height","300px");
		$width=$this->getAttribute("width","100%");
		$image_folder=$fieldpath."/images";
		$cancelimg=$fieldpath."/images/cancel16.png";
		$folderimg=$fieldpath."/images/folder16.png";
		$fileimg=$fieldpath."/images/file16.png";
		$loaderimg=$fieldpath."/images/ajax-loader.gif";
		$quetionimg=$fieldpath."/images/messagebox_question.png";
		if($this->readonly) $readonlyjs="true"; else $readonlyjs="false";
		
		$rowfilecontent = "<td class=\"col-icon\">{icon}</td><td class=\"col-name\">{name}</td><td class=\"col-size\">{size}</td><td class=\"col-date\">{date}</td>";
		$rowdircontent = "<td class=\"col-icon\">{icon}</td><td class=\"col-name\">{name}</td><td class=\"col-size\">{size}</td><td class=\"col-date\">{date}</td>";
		// Manage language
		$lang = JFactory::getLanguage();
		$langtag = $lang->getTag();
		$mainlangtag= explode("-",$langtag)[0];
		// Lang file for datatable
		$langfilelocation=JPATH_COMPONENT."/libraries/datatables/lang-".$mainlangtag.".json";
		if (JFile::exists($langfilelocation))
			$langfile = $component_path."/libraries/datatables/lang-".$mainlangtag.".json";
			else
				$langfile=$component_path."/libraries/datatables/lang-default.json";
		$lang->load("jformfield_filexplorer",JPATH_COMPONENT."/models/fields/filexplorer",$lang->getTag(),true);
				
				
		if (!self::$initialised)
		{
			$script = array();
			$script[]="jformfieldfilexplorer_supported_extensions={doc:1, docx:1, zip:1,xls:1,xlsx:1, pdf:1,png:1, gif:1,jpg:1,jpeg:1, ico:1, html:1, htm:1, php:1}";
			$script[]="jformfieldfilexplorer_curdir={};";
			$script[]="jformfieldfilexplorer_curdirreadonly={};";
			$script[]="jformfieldfilexplorer_readonly={};";
			$script[]="jformfieldfilexplorer_rootdir={};";
			$script[]="jformfieldfilexplorer_table={};";
			$script[]="jformfieldfilexplorer_deletefiles=[];";
			$script[]="function jformfieldfilexplorer_showUpload(id) {";
			$script[]="    jQuery( \"#\"+id+\"_uploadDlg\" ).dialog({resizable: false, height:300,width : 400,modal: true, draggable: true});";
			$script[]="    return false; }";
			$script[]="function jformfieldfilexplorer_openRenameDialog(id) {";
			$script[]="    var tbl=jformfieldfilexplorer_table[id];";
			$script[]="    var row=tbl.row('.selected');";
			$script[]="    if (row===undefined) { jformfieldfilexplorer_errorBox(id,'".JText::_("JFORMFIELD_FILEXPLORER_RENAME_NOTONEERROR")."'); return; } ";
			$script[]="    if(row.node().hasClass('right_write')) {";
			$script[]="        jQuery('#'+id+'_renameFileMessageBox_oldfilename').val(row.data()[1]);";
			$script[]="        jQuery('#'+id+'_renameFileMessageBox_newfilename').val(row.data()[1]);";
			$script[]="        jQuery( \"#\"+id+\"_renameFileMessageBox\" ).dialog({resizable: false, height:200,width : 400,modal: true, draggable: true});";
			$script[]="    } else {";
			$script[]="        jformfieldfilexplorer_errorBox(id,'".JText::_("JFORMFIELD_FILEXPLORER_NOTRWRIGHT")."');";
			$script[]="    }";
			$script[]="    return false; ";
			$script[]="}";
			
			$script[]="function jformfieldfilexplorer_calcSize(size) {";
			$script[]="    var units=['','Ko','Mo','Go'];";
			$script[]="    var i=0;var v=size;";
			$script[]="    while (v>1024 && i<4) {";
			$script[]="        i++;v=Math.round(v/1024);";
			$script[]="    }";
			$script[]="    return v+' '+units[i];";
			$script[]="}";
			$script[]="function stripTrailingSlash(str) {";
    		$script[]="    if(str.substr(-1) === '/') {str=str.substr(0, str.length - 1);}";
    		$script[]="    if(str.substr(0,1) === '/') {str=str.substr(1);}";
    		$script[]="    return str;";
			$script[]="}";
			$script[]="function jformfieldfilexplorer_formatpath(id,rootlabel,dir) {";
			$script[]="    var parts=stripTrailingSlash(dir).split('/');var curdir='';";
			$script[]="    var str='';";
			$script[]="    if (dir=='') return rootlabel;";
			$script[]="    else str+='<a onclick=\"jformfieldfilexplorer_fillList(\\''+id+'\\',\\''+curdir+'\\');return false\">'+rootlabel+'</a><span class=\"icon-rightarrow\"></span>';";
			$script[]="    for(var i=0;i<parts.length;i++){";
			$script[]="        curdir+=parts[i]+'/';";
			$script[]="        if(i<parts.length-1) {";
			$script[]="            str+='<a onclick=\"jformfieldfilexplorer_fillList(\\''+id+'\\',\\''+curdir+'\\');return false\">'+parts[i]+'</a><span class=\"icon-rightarrow\"></span>';";
			$script[]="        } else {";
			$script[]="            str+='<span>'+parts[i]+'</span>';";
			$script[]="        }";
			$script[]="    }";
			$script[]="    return str;";
			$script[]="}";
			$script[]="function jformfieldfilexplorer_addRow(id,path, isfolder, isSelected,fsize,mtime,ext, right) {";
			$script[]="    var row=[];var cls='';";
			$script[]="    var tbl=jformfieldfilexplorer_table[id];";
			$script[]="    if (isfolder) { "; //dir
			$script[]="        row=['<img src=\"".$folderimg."\" width=\"16\" width=\"16\" >', path,'',''];cls='directory';";
			$script[]="    } else {";
			$script[]="        var icon='';";
			$script[]="        if(ext in jformfieldfilexplorer_supported_extensions) { ";
			$script[]="            icon='file-'+ext+'.png'; ";
			$script[]="        } else { ";
			$script[]="            icon='file-default.png'; ";
			$script[]="        }";
			$script[]="        row=['<img src=\"".$image_folder."/'+icon+'\" width=\"16\" width=\"16\" >',path,mtime,jformfieldfilexplorer_calcSize(fsize)];cls='file';";
			$script[]="    }";
			$script[]="    if (right.indexOf('w')) { cls+=' right_write';}else{cls+=' right_readonly'}";
			$script[]="    var newrow= tbl.row.add(row);";
			$script[]="    newrow.draw( false );";
			$script[]="    newrow.node().addClass(cls);";
			$script[]="}";
			$script[]="function jformfieldfilexplorer_dbclick(id,row) {";
			$script[]="    var name=row.cells[1].innerText;";
			$script[]="    var curdir=jformfieldfilexplorer_curdir[id];";
			$script[]="    var rootdir=jformfieldfilexplorer_rootdir[id];";
			$script[]="    if(row.hasClass('directory')){";
			$script[]="        if (name=='..'){";
			$script[]="            k=-1;";
			$script[]="            for(i=curdir.length-1;i>0;i--){";
			$script[]="                if (curdir.substr(i,1)=='/')	{k=i;break;	}";
			$script[]="            }";
			$script[]="            newdir=curdir.substr(0,k);";
			$script[]="        } else { newdir=curdir+'/'+ name; }";
			$script[]="        jformfieldfilexplorer_fillList(id, newdir);";
			$script[]="    } else {";
			$script[]="        var filename=rootdir;";
			$script[]="        if(curdir!='') filename=filename+curdir;";
			$script[]="        filename=filename+'/'+name;";
			$script[]="        var a=document.getElementById(id+'_link');a.href='".JUri::base(true)."'+filename;a.click();";
			$script[]="    }";
			$script[]="}";
			$script[]="function jformfieldfilexplorer_errorBox(id,text) {";
			$script[]="    jQuery('#'+id+'_ErrorMessage .content').html(text);";
			$script[]="    jQuery('#'+id+'_ErrorMessage').dialog({resizable: false, height:150,width : 300,modal: true, draggable: true});";
			$script[]="}";
			$script[]="function jformfieldfilexplorer_openRemoveDialog(id,text) {";
			$script[]="    jformfieldfilexplorer_deletefiles=[];";
			$script[]="    var tbl=jformfieldfilexplorer_table[id];";
			$script[]="    var rows=tbl.rows({selected:true});";
			$script[]="    tbl.rows({selected:true}).every( function () {";
			$script[]="        if(this.node().hasClass('right_write')) {";
			$script[]="            jformfieldfilexplorer_deletefiles.push(this.data());";
			$script[]="        }";
			$script[]="    });";
			$script[]="    if (jformfieldfilexplorer_deletefiles.length>0) {";
			$script[]="        jQuery('#'+id+'_confirmeRemoveFile .content').html(text);";
			$script[]="        jQuery('#'+id+'_confirmeRemoveFile').dialog({resizable: false, height:150,width : 300,modal: true, draggable: true});";
			$script[]="    } else { jformfieldfilexplorer_errorBox(id,'Aucun fichier sélectionné');}";
			$script[]="}";
			$script[]="function jformfieldfilexplorer_openUploadDialog(id,text) {";
			$script[]="    jQuery('#'+id+'_uploadDialog').dialog({resizable: false, height:470,width : 400,modal: true, draggable: true});";
			$script[]="}";
			$script[]="function jformfieldfilexplorer_fillList(id,dirname, selected) {";
			$script[]="    if (dirname==undefined) dirname=jformfieldfilexplorer_curdir[id];";
			$script[]="    var rootdir=jformfieldfilexplorer_rootdir[id];";
			$script[]="    if(selected==undefined) selected={};";
			$script[]="    var lst = document.getElementById(id+'_browseListe');";
			$script[]="    var tbl=jformfieldfilexplorer_table[id];";
			$script[]="    tbl.clear();";
			$script[]="    img = document.createElement('img');";
			$script[]="    img.src='".$loaderimg."';";
			$script[]="    l = lst.clientWidth/2;";
			$script[]="    h = lst.clientHeight/2;";
			$script[]="    img.style.marginLeft= l+'px';";
			$script[]="    img.style.marginTop= h+'px';";
			$script[]="    lst.appendChild(img);";
			$script[]="    var lurl ='".$refreshurl."';";
			$script[]="    formData={'rootdir':rootdir,'directory': dirname, 'filter': '".$filter."', 'exclude':'".$exclude."', 'mode':'".$mode."'};";
			$script[]="    try {";
			$script[]="        jQuery.ajax({";
        	$script[]="        type: 'POST',";
            $script[]="        url: lurl,";
        	$script[]="        data: formData,";
    		$script[]="        dataType: 'json',";
    		$script[]="        success: function (data, textStatus, jqXHR){";
			$script[]="            jformfieldfilexplorer_curdir[id] = dirname;";
			$script[]="            items= data.list;var isrootdir=(data.dir==jformfieldfilexplorer_rootdir[id])";
			$script[]="    	       jformfieldfilexplorer_curdirreadonly[id]=((data.right.indexOf('w') == -1) && jformfieldfilexplorer_readonly[id]);";
			$script[]="            if(data.error=='') {";
			$script[]="                jQuery.each( items, function( i, item ) {";
			$script[]="                    if (item.path!='..' || !isrootdir) ";
			$script[]="                        jformfieldfilexplorer_addRow(id,item.path, item.type == 'dir', (item.path in selected),item.size,item.mtime,item.ext,item.right);";
			$script[]="                });";
			$script[]="                jQuery('#'+id+'_browseTable tbody tr').dblclick( function() { jformfieldfilexplorer_dbclick(id,this);}); ";
			$script[]="                jQuery('#'+id+'_browseTable tbody').on( 'click', 'tr', function () { ";
			$script[]="                    jQuery('.jformfieldfilexplorer_itemoperation').prop('disabled', jQuery(this).hasClass('right_readonly')); ";
        	$script[]="                })";
        	$script[]="                jQuery('.jformfieldfilexplorer_directoryoperation').prop('disabled', jformfieldfilexplorer_curdirreadonly[id]); ";
        	$script[]="                if (jformfieldfilexplorer_curdirreadonly[id]) jQuery('.jformfieldfilexplorer_draguploadinfo').hide(); else jQuery('.jformfieldfilexplorer_draguploadinfo').show(); ";
        	$script[]="                jQuery('#'+id+'_search').val('');";
			$script[]="            } else {";
			$script[]="                jformfieldfilexplorer_errorBox(id,data.error);";
			$script[]="            }";
			
			
			$script[]="            document.getElementById(id+'_browsePath').innerHTML=jformfieldfilexplorer_formatpath(id,'".$rootlabel."',dirname);";
			$script[]="            if (!isrootdir) tbl.order.fixed( { pre: [ 0, 'asc' ]} );";
			$script[]="        },"; 
			$script[]="        error: function (msg,status) {";
    		$script[]="            jformfieldfilexplorer_errorBox(id,'".JText::_("JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR")." :'+status);";
	    	$script[]="	       }});";
			$script[]="    } catch(ex) {";
			$script[]="        jformfieldfilexplorer_errorBox(id,'".JText::_("JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR")." :'+ex);";
			$script[]="    }";
			$script[]="    img.parentNode.removeChild(img);";
			$script[]="}";
			
			$script[]="function jformfieldfilexplorer_uploadFiles(id, files, container, dlg) {";
			$script[]="    if (jformfieldfilexplorer_readonly[id]) return;";
			$script[]="    if (dlg===undefined){";
			$script[]="        var dlg = jQuery('<div></div>');var container=dlg;";
			$script[]="        var container=jQuery('<div></div>');dlg.append(container);";
			$script[]="        var message=jQuery('<div></div>');dlg.append(message);";
			$script[]="        dlg.dialog({resizable: false, height:400,width : 500,modal: false, draggable: true});";
			$script[]="    } else { var message=jQuery('#'+id+'_uploadDialogMessage');}";
			$script[]="    for (var i = 0; i < files.length; i++) {";
        	$script[]="        var fd = new FormData();fd.append('uploadedfiles[]', files[i], files[i].name);";
 			$script[]="        var status = new jformfieldfilexplorer_createStatusbar(jQuery(container)); ";
        	$script[]="        status.setFileNameSize(files[i].name,files[i].size);";
        	$script[]="	       jformfieldfilexplorer_uploadFile(id, fd,status);";
 			$script[]="    }";
 			$script[]="    message.html('Envoi termine');";
			$script[]="}";
        	$script[]="function jformfieldfilexplorer_uploadFile(id, formData,status) {";
			$script[]="    var curdir=jformfieldfilexplorer_curdir[id];";
			$script[]="    var rootdir=jformfieldfilexplorer_rootdir[id];";
			$script[]="    jquploadinput = jQuery('#'+id+'_uploadfileselect');";
			$script[]="    var dir=rootdir;if(curdir!='') dir=dir+curdir;";
			$script[]="    formData.append('directory', dir);";
			$script[]="    try {";
			$script[]="        var ajaxQuery=jQuery.ajax({";
			$script[]="            xhr: function() {";
            $script[]="                var xhrobj = jQuery.ajaxSettings.xhr();";
            $script[]="                if (xhrobj.upload) {";
            $script[]="                    xhrobj.upload.addEventListener('progress', function(event) {";
            $script[]="                        var percent = 0;";
            $script[]="                        var position = event.loaded || event.position;";
            $script[]="                        var total = event.total;";
            $script[]="                       if (event.lengthComputable) { percent = Math.ceil(position / total * 100); }";
            $script[]="                       status.setProgress(percent);";
            $script[]="                   }, false);";
            $script[]="               }";
            $script[]="               return xhrobj;";
        	$script[]="            },";
			$script[]="        type: 'POST',";
			$script[]="        url: '".$uploadurl."',";
			$script[]="        data: formData,";
			$script[]="        dataType: 'json',";
			$script[]="        processData: false, // Don't process the files";
        	$script[]="        contentType: false,";
			$script[]="        success: function (data, textStatus, jqXHR){";
			$script[]="            jquploadinput.replaceWith(jquploadinput.clone())";
			$script[]="            jformfieldfilexplorer_fillList(id);";
			$script[]="            status.setProgress(100);";
			$script[]="        },";
			$script[]="        error: function (msg,status) {";
			$script[]="            jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR')." :'+status);";
			$script[]="	       }});";
			$script[]="        status.setAbort(ajaxQuery);";
			$script[]="    } catch(ex) {";
			$script[]="        jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR')." :'+ex);";
			$script[]="    }";
			$script[]="}";
			$script[]="jformfieldfilexplorer_rowCount=0;";
			$script[]="function jformfieldfilexplorer_createStatusbar(dlg){";
     		$script[]="    jformfieldfilexplorer_rowCount++;";
     		$script[]="    var row='odd';";
     		$script[]="    if(jformfieldfilexplorer_rowCount %2 ==0) row ='even';";
     		$script[]="    this.statusbar = jQuery('<div class=\"jformfieldfilexplorer_upload_statusbar '+row+'\"></div>');";
     		$script[]="    this.filename = jQuery('<div class=\"jformfieldfilexplorer_upload_filename\"></div>').appendTo(this.statusbar);";
     		$script[]="    this.size = jQuery('<div class=\"jformfieldfilexplorer_upload_filesize\"></div>').appendTo(this.statusbar);";
     		$script[]="    this.progressBar = jQuery('<div class=\"jformfieldfilexplorer_upload_progressBar\"><div></div></div>').appendTo(this.statusbar);";
     		$script[]="    this.abort = jQuery('<div class=\"jformfieldfilexplorer_upload_abort\">Abort</div>').appendTo(this.statusbar);";
     		$script[]="    dlg.append(this.statusbar);";
     		$script[]="    this.setFileNameSize = function(name,size) {";
        	$script[]="        var sizeStr=jformfieldfilexplorer_calcSize(size);";
        	$script[]="        this.filename.html(name);";
        	$script[]="        this.size.html(sizeStr);";
    		$script[]="    }";
    		$script[]="    this.setProgress = function(progress){";       
        	$script[]="        var progressBarWidth =progress*this.progressBar.width()/ 100;";  
        	$script[]="        this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + '% ');";
        	$script[]="        if(parseInt(progress) >= 100){this.abort.hide();}";
    		$script[]="    }";
    		$script[]="    this.setAbort = function(jqxhr) {"; 
        	$script[]="        var sb = this.statusbar;";
        	$script[]="        this.abort.click(function(){ jqxhr.abort(); sb.hide(); });";
    		$script[]="    }";
    		$script[]="}";
			$script[]="function jformfieldfilexplorer_removeFile(id) {";
			$script[]="     jQuery('#'+id+'_confirmeRemoveFile').dialog('close');";
			$script[]="    if (jformfieldfilexplorer_readonly[id]) { return; }";
			$script[]="    var tbl=jformfieldfilexplorer_table[id];";
			$script[]="    var curdir=jformfieldfilexplorer_curdir[id];";
			$script[]="    var rootdir=jformfieldfilexplorer_rootdir[id];";
			$script[]="    var dir=rootdir;if(curdir!='') dir=dir+curdir;";
			$script[]="    var formData = new FormData();";
			$script[]="    for (var i = 0; i < jformfieldfilexplorer_deletefiles.length; i++) {";
			$script[]="        var row=jformfieldfilexplorer_deletefiles[i];";
			$script[]="        formData.append('files[]',row[1] );";
 			$script[]="    }";
			$script[]="    formData.append('directory', dir);";
			$script[]="    try {";
			$script[]="        var ajaxQuery=jQuery.ajax({";
			$script[]="        type: 'POST',";
			$script[]="        url: '".$removeurl."',";
			$script[]="        data: formData,";
			$script[]="        dataType: 'json',";
			$script[]="        processData: false, // Don't process the files";
        	$script[]="        contentType: false,";
			$script[]="        success: function (data, textStatus, jqXHR){";
			$script[]="            if(data.return==false) jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_DELETEFILE_ERROR')." :'+data.error);";
			$script[]="            jformfieldfilexplorer_fillList(id);";
			$script[]="        },";
			$script[]="        error: function (msg,status) {";
			$script[]="            jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR')." :'+status);";
			$script[]="	       }});";
			$script[]="    } catch(ex) {";
			$script[]="        jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR')." :'+ex);";
			$script[]="    }";
 			$script[]="}";
 			$script[]="function jformfieldfilexplorer_renameFile(id) {";
 			$script[]="     jQuery('#'+id+'_renameFileMessageBox').dialog('close');";
 			$script[]="    if (jformfieldfilexplorer_readonly[id]) { return; }";
 			$script[]="    var tbl=jformfieldfilexplorer_table[id];";
 			$script[]="    var curdir=jformfieldfilexplorer_curdir[id];";
 			$script[]="    var rootdir=jformfieldfilexplorer_rootdir[id];";
 			$script[]="    var dir=rootdir;if(curdir!='') dir=dir+curdir;";
 			$script[]="    var formData = new FormData();";
 			$script[]="    formData.append('oldfile',jQuery('#'+id+'_renameFileMessageBox_oldfilename').val());";
 			$script[]="    formData.append('newfile',jQuery('#'+id+'_renameFileMessageBox_newfilename').val());";
 			$script[]="    formData.append('directory', dir);";
 			$script[]="    try {";
 			$script[]="        var ajaxQuery=jQuery.ajax({";
 			$script[]="        type: 'POST',";
 			$script[]="        url: '".$renameurl."',";
 			$script[]="        data: formData,";
 			$script[]="        dataType: 'json',";
 			$script[]="        processData: false, // Don't process the files";
 			$script[]="        contentType: false,";
 			$script[]="        success: function (data, textStatus, jqXHR){";
 			$script[]="            if(data.return==false) jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_RENAMEFILE_ERROR')." :'+data.error);";
 			$script[]="            jformfieldfilexplorer_fillList(id);";
 			$script[]="        },";
 			$script[]="        error: function (msg,status) {";
 			$script[]="            jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR')." :'+status);";
 			$script[]="	       }});";
 			$script[]="    } catch(ex) {";
 			$script[]="        jformfieldfilexplorer_errorBox(id,'".JText::_('JFORMFIELD_FILEXPLORER_WEBSERVICE_ERROR')." :'+ex);";
 			$script[]="    }";
 			$script[]="}";
 			$script[]="function jformfieldfilexplorer_search(id) {";
 			$script[]="    var tbl=jformfieldfilexplorer_table[id];";
 			$script[]="    var searchtext=jQuery('#'+id+'_search').val();";
 			$script[]="    tbl.search(searchtext).draw();";
 			$script[]="}";
			
			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration("    ".implode("\n    ", $script));
			
			$css=array();
			//$css[]=".jformfieldfilexplorer_browseliste{ text-align:left; border:1px solid #4FBAB3; white-space:nowrap; font:normal 12px verdana; background:#fff;";
			//$css[]="              padding:5px; overflow: auto;	margin-bottom:auto;	width:100%;	margin-bottom: 10px;  z-index:1001;}";
			$css[]=".jformfieldfilexplorer_browsedlg .buttons {	margin-bottom:0px;	height:30px;}";
			
			//$css[]=".jformfieldfilexplorer_browsepath {width:calc(100% - 50px);height:20px;background-color:white;border:1px solid #4FBAB3;padding-left:10px;margin-bottom:3px;padding-top:5px;}";
			$css[]=".jformfieldfilexplorer_browsepath {width:100%;height:20px;background-color:white;border:1px solid #4FBAB3;padding-left:10px;margin-bottom:3px;padding-top:5px;}";
			$css[]=".jformfieldfilexplorer_browsepath span {font-weight: bold;}";
			$css[]=".jformfieldfilexplorer_browsepath a { text-decoration: none; }";
			$css[]=".jformfieldfilexplorer_browsepath a:hover { color:black;cursor: hand !important; }";
			$css[]=".jformfieldfilexplorer_browsepathcontainer { width:100%;height:30px; }";
			$css[]=".jformfieldfilexplorer_browsepathcontainer button { position:absolute; margin-left:-33px; height:27px;}";
			$css[]=".jformfieldfilexplorer_browseTable tr { cursor:default;	color:#000;	text-decoration:none;	background:#fff; vertical-align:middle; width:100%; height:20px;}";
			$css[]=".jformfieldfilexplorer_browseTable tr:hover{ color:white; background-color:blue;}";
			$css[]=".jformfieldfilexplorer_browseTable tr.directory ,.jformfieldfilexplorer_browseTable tr.file { -webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none;-ms-user-select: none; user-select: none;}";				
			$css[]=".jformfieldfilexplorer_browseTable {  font:normal 12px verdana;width:100%;  }";
			$css[]=".jformfieldfilexplorer_browseTable tr .col-date, .jformfieldfilexplorer_browseTable th .col-date { width:120px; font-size:80%;  }";
			$css[]=".jformfieldfilexplorer_browseTable tr .col-size, .jformfieldfilexplorer_browseTable th .col-size { width:70px; font-size:80%; }";
			$css[]=".jformfieldfilexplorer_browseTable tr .col-icon, .jformfieldfilexplorer_browseTable th .col-icon { width:36px; font-size:80%;  }";
			$css[]=".jformfieldfilexplorer_browseTable tr .col-name, .jformfieldfilexplorer_browseTable th .col-name { width:100%;   }";
			$css[]=".jformfieldfilexplorer_browseContainer div, .jformfieldfilexplorer_browseContainer button { float:left;}";
			$css[]=".jformfieldfilexplorer_toolbarbutton { width:100%;}";
			$css[]=".jformfieldfilexplorer_toolbarbutton .left-side { float:left;}";
			$css[]=".jformfieldfilexplorer_toolbarbutton .right-side { float:right;}";
			$css[]=".jformfieldfilexplorer_toolbarbutton button, .jformfieldfilexplorer_toolbarbutton input { float:left;}";
			$css[]="table.dataTable thead th, table.dataTable thead td { padding: 1px 15px !important;}";
			$css[]=".jformfieldfilexplorer_uploadProgress { min-height:270px; border:1px solid #4FBAB3; margin:3px; width:100%; }";
			$css[]=".jformfieldfilexplorer_upload_progressBar {width: 200px; height: 22px; border: 1px solid #ddd; border-radius: 5px;  overflow: hidden; display:inline-block; margin:0px 10px 5px 5px; vertical-align:top;}";
			$css[]=".jformfieldfilexplorer_upload_progressBar div { height: 100%; color: #fff; text-align: right;  line-height: 22px; /* same as #progressBar height if we want text middle aligned */ width: 0; background-color: #0ba1b5; border-radius: 3px;}";
			$css[]=".jformfieldfilexplorer_upload_statusbar { border-top:1px solid #A9CCD1; min-height:25px;  padding:10px 10px 0px 10px; vertical-align:top;}";
			$css[]=".jformfieldfilexplorer_upload_statusbar:nth-child(odd){ background:#EBEFF0;}";
			$css[]=".jformfieldfilexplorer_upload_filename {display:inline-block;vertical-align:top;width:250px;}";
			$css[]=".jformfieldfilexplorer_upload_filesize{display:inline-block;vertical-align:top;color:#30693D;width:100px;margin-left:10px;margin-right:5px;}";
			$css[]=".jformfieldfilexplorer_upload_abort{background-color:#A8352F; -moz-border-radius:4px; -webkit-border-radius:4px; border-radius:4px;display:inline-block; color:#fff; font-family:arial;font-size:13px;font-weight:normal; padding:4px 15px; cursor:pointer;  vertical-align:top }";
			$css[]=".jformfieldfilexplorer_draguploadinfo { color:red; font-size:80%;}";
			$css[]=".jformfieldfilexplorer_toolbar { float: left;background-color:grey;width:150px;height:25px;}";
			$css[]="div#jform_documents_browseTable_filter { margin-top: -20px;}";
			$css[]=".jformfieldfilexplorer_msgbox { display:none; }";
			$css[]=".jformfieldfilexplorer_msgbox buttons { margin-top:20px; }";
			$css[]=".jformfieldfilexplorer_msgbox_edit { width:90%; }";
			$css[]=".dataTables_wrapper .even { background-color:rgb(230,230,230);}";
			$css[]=".jformfieldfilexplorer_errormsgbox .content { min-height:70px; }";
			$doc->addStyleDeclaration(implode("", $css));	
			
			self::$initialised = true;
		}
		
		// Initialize variables.
		
		$html = array();
		$html[]="<a href=\"\" id=\"".$this->id."_link\" style=\"display:none\" target=\"blank\" ></a>";
		$html[]="<div id=\"".$this->id."_uploadDialog\" class=\"jformfieldfilexplorer_msgbox\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_UPLOAD_TITLE')."\">";
		$html[]="  <div id=\"".$this->id."_uploadDialogMessage\" ></div>";
		$html[]="  <fieldset><legend>".JText::_('JFORMFIELD_FILEXPLORER_UPLOAD_LEGEND')."</legend>";
    	$html[]="    <form id=\"".$this->id."_fileform\" action=\"".$uploadurl."\" method=\"POST\" ENCTYPE=\"multipart/form-data\">";
    	$html[]="      <input type=\"file\" id=\"".$this->id."_uploadfileselect\" name=\"".$this->id."_uploadfileselect[]\" multiple style=\"100%\" />";
    	$html[]="      <button onclick=\"javascript:jformfieldfilexplorer_uploadFiles('".$this->id."', document.getElementById('".$this->id."_uploadfileselect').files,'#".$this->id."_uploadProgressDialog', jQuery('#".$this->id."_uploadDialog'));return false;\">";
    	$html[]="      <span class=\"icon-upload\"></span> ".JText::_('JFORMFIELD_FILEXPLORER_UPLOAD_SEND')."</button>";
    	$html[]="      <div id=\"".$this->id."_uploadProgressDialog\" class=\"jformfieldfilexplorer_uploadProgress\" ></div>";
    	$html[]="      <div class=\"jformfieldfilexplorer_draguploadinfo\">".JText::_("JFORMFIELD_FILEXPLORER_DRAGUPLOAD_TEXT")."</div>";
    	$html[]="      <div class=\"center\" style=\"width:100%;text-align:center;margin-top:10px;\" ><button onclick=\"javascript:jQuery('#".$this->id."_uploadDialog').dialog('close');return false;\"><span class=\"icon-cancel\"></span> ".JText::_('JFORMFIELD_FILEXPLORER_CLOSE')."</button></div>";
    	$html[]="    </form>";
    	$html[]="  </fieldset>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_confirmeRemoveFile\" class=\"jformfieldfilexplorer_msgbox\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_DELETE_FILE_TITLE')."\">";
		$html[]="  <div style=\"float:left\"><img src=\"".$quetionimg."\" /></div><div id=\"".$this->id."_content\" style=\"margin-left:70px;\" class=\"content\">";
		$html[]="    ".JText::_('JFORMFIELD_FILEXPLORER_DELETE_FILE_TITLE');
		$html[]="  </div><input type=\"hidden\" id=\"".$this->id."_confirmeRemoveAttachment_id\" value=\"\">";
		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons center\" style=\"margin-top:30px;\">";
		$html[]="    <button onclick=\"jformfieldfilexplorer_removeFile('".$this->id."');return false;\"><span class=\"icon-ok\"></span>".JText::_('JFORMFIELD_FILEXPLORER_DELETE')."</button>";
		$html[]="    <button onclick=\"javascript:jQuery('#".$this->id."_confirmeRemoveFile').dialog('close');return false;\"><span class=\"icon-cancel\"></span>".JText::_('JFORMFIELD_FILEXPLORER_CANCEL')."</button>";
		$html[]="  </div>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_renameFileMessageBox\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_RENAME_FILE_TITLE')."\" class=\"jformfieldfilexplorer_msgbox\">";
		$html[]="  <div style=\"float:left\"><img src=\"".$quetionimg."\" /></div><div id=\"".$this->id."_content\" style=\"margin-left:70px;\" class=\"content\">";
		$html[]="    ".JText::_('JFORMFIELD_FILEXPLORER_RENAME_FILE_LABEL');
		$html[]="    <input id=\"".$this->id."_renameFileMessageBox_newfilename\" type=\"text\" value=\"\" class=\"jformfieldfilexplorer_msgbox_edit\" >";
		$html[]="    <input id=\"".$this->id."_renameFileMessageBox_oldfilename\" type=\"hidden\" value=\"\"  >";
		$html[]="  </div>";
		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons center\" style=\"margin-top:30px;\">";
		$html[]="    <button onclick=\"jformfieldfilexplorer_renameFile('".$this->id."');return false;\"><span class=\"icon-ok\"></span>".JText::_('JFORMFIELD_FILEXPLORER_RENAME')."</button>";
		$html[]="    <button onclick=\"javascript:jQuery('#".$this->id."_renameFileMessageBox').dialog('close');return false;\"><span class=\"icon-cancel\"></span>".JText::_('JFORMFIELD_FILEXPLORER_CANCEL')."</button>";
		$html[]="  </div>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_ErrorMessage\" title=\"".$txt_error_title."\" class=\"jformfieldfilexplorer_msgbox jformfieldfilexplorer_errormsgbox\">";
		$html[]="  <div id=\"".$this->id."_ErrorMessageContent\" class=\"content\" ></div>";
		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons center\">";
		$html[]="    <button onclick=\"javascript:jQuery('#".$this->id."_ErrorMessage').dialog('close');return false;\"><span class=\"icon-cancel\"></span>".JText::_('JFORMFIELD_FILEXPLORER_OK')."</button>";
		$html[]="  </div>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_browseDlg\" class=\"jformfieldfilexplorer_browsedlg\"  >";
		$html[]="  <div id=\"".$this->id."_dragandrophandler\" class=\"jformfieldfilexplorer_content\" >";
		
   		$html[]="    <div id=\"".$this->id."_browseContainer\" class=\"jformfieldfilexplorer_browseContainer\">";
   		$html[]="        <div id=\"".$this->id."_toolbarbutton\" class=\"jformfieldfilexplorer_toolbarbutton\" ><div class=\"left-side\">";
   		
   		if (!$this->readonly) {
   		$html[]="            <button disabled='disabled' class=\"jformfieldfilexplorer_itemoperation\" onclick=\"javascript:jformfieldfilexplorer_openRemoveDialog('".$this->id."');return false;\"><span class=\"icon-delete\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_REMOVEFILE_TOOLTIP')."\"></span></button>";
   		$html[]="            <button disabled='disabled' class=\"jformfieldfilexplorer_itemoperation\" onclick=\"javascript:jformfieldfilexplorer_openRenameDialog('".$this->id."');return false;\"><span class=\"icon-edit\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_RENAMEFILE_TOOLTIP')."\"></span></button>";
   		$html[]="            <button disabled='disabled' class=\"jformfieldfilexplorer_directoryoperation\" onclick=\"javascript:jformfieldfilexplorer_openUploadDialog('".$this->id."');return false;\"><span class=\"icon-upload\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_UPLOADFILE_TOOLTIP')."\"></span></button>";
   		}
   		$html[]="            </div><div class=\"right-side\">";
   		$html[]="            <input type=\"text\" id=\"".$this->id."_search\" placeholder=\"".JText::_('JFORMFIELD_FILEXPLORER_SEARCH_TOOLTIP')."\" />";
   		$html[]="            <button onclick=\"javascript:jformfieldfilexplorer_search('".$this->id."');return false;\"><span class=\"icon-search\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_SEARCH_TOOLTIP')."\"></span></button>";
   		$html[]="        </div></div>";
   		$html[]="        <div  style=\"clear:both;\"></div><div id=\"".$this->id."_browsePathContainer\" class=\"jformfieldfilexplorer_browsepathcontainer\">";
   		$html[]="            <div id=\"".$this->id."_browsePath\" class=\"jformfieldfilexplorer_browsepath\" ></div>";
   		$html[]="            <button onclick=\"javascript:jformfieldfilexplorer_fillList('".$this->id."');return false;\"><span class=\"icon-refresh\" title=\"".JText::_('JFORMFIELD_FILEXPLORER_REFRESH')."\"></span></button></div>";
   		$html[]="        </div>";
   		//$html[]="    <div id=\"".$this->id."_browseCurrentDir\" style=\"display:none\">..</div>";
 		$html[]="    <table id=\"".$this->id."_browseTable\" class=\"jformfieldfilexplorer_browseTable\" class=\"hover\" cellspacing=\"0\" width=\"100%\"><thead><tr><th class=\"col-icon\">&nbsp;</th><th class=\"col-name\">Nom</th><th class=\"col-date\">Date</th><th class=\"col-size\">Taille</th></tr></thead><tbody id=\"".$this->id."_browseListe\"></tbody></table>";
 		$html[]="    <div class=\"jformfieldfilexplorer_draguploadinfo\">".JText::_("JFORMFIELD_FILEXPLORER_DRAGUPLOAD_TEXT")."</div>";
  		$html[]="  </div>";
		$html[]="</div>";
		
				
		$script=array();
		$script[]="jformfieldfilexplorer_curdir['".$this->id."']='';";
		$script[]="jformfieldfilexplorer_readonly['".$this->id."']=".$readonlyjs;
		$script[]="jformfieldfilexplorer_curdirreadonly['".$this->id."']=".$readonlyjs;
		$script[]="jformfieldfilexplorer_rootdir['".$this->id."']='".$curdir."';";
		$script[]="jQuery.extend( true, jQuery.fn.dataTable.defaults, {";
		$script[]="    'searching':$searching,'ordering':$ordering } );";
		$script[]="jQuery(document).ready(function() {";
		$script[]="    jformfieldfilexplorer_table['".$this->id."']=jQuery('#".$this->id."_browseTable').DataTable({";
		$script[]="        'scrollY':'".$height."',scrollCollapse: true,paging: false, select:true, 'bJQueryUI': true,searching: true, 'dom': 'rtip',";
		$script[]="        'language': {'url': '".$langfile."' , select: {rows: { _: '".JText::_("JFORMFIELD_FILEXPLORER_N_FILES_SELECTED")."', 0: '".JText::_("JFORMFIELD_FILEXPLORER_0_FILES_SELECTED")."', 1: '".JText::_("JFORMFIELD_FILEXPLORER_1_FILES_SELECTED")."'} }},";
		$script[]="        'createdRow': function ( row, data, index ) {";
        $script[]="            jQuery('td', row).eq(2).addClass('col-date'); ";
        $script[]="            jQuery('td', row).eq(3).addClass('col-size'); }";
        $script[]="    } );";
		$script[]="    jformfieldfilexplorer_fillList('".$this->id."','', {}); ";
		if (!$this->readonly) {
		$script[]="    var obj = jQuery('#".$this->id."_dragandrophandler');";
		$script[]="    obj.on('dragenter', function (e) { e.stopPropagation();  e.preventDefault();  });";
		$script[]="    obj.on('dragover', function (e) { e.stopPropagation(); e.preventDefault();});";
		$script[]="    obj.on('drop', function (e) {"; 
		$script[]="        e.preventDefault();  ";
		$script[]="        var files = e.originalEvent.dataTransfer.files;";
 	     				   //We need to send dropped files to Server
     	$script[]="        jformfieldfilexplorer_uploadFiles('".$this->id."', files,'#".$this->id."_dragandrophandler');";
		$script[]="   });";
		$script[]="    var obj = jQuery('#".$this->id."_uploadDialog');";
		$script[]="    obj.on('dragenter', function (e) { e.stopPropagation();  e.preventDefault();  });";
		$script[]="    obj.on('dragover', function (e) { e.stopPropagation(); e.preventDefault();});";
		$script[]="    obj.on('drop', function (e) {";
		$script[]="        e.preventDefault();  ";
		$script[]="        var files = e.originalEvent.dataTransfer.files;";
		//We need to send dropped files to Server
		$script[]="        jformfieldfilexplorer_uploadFiles('".$this->id."', files,'#".$this->id."_uploadDialog',obj);";
		$script[]="   });";
		}
		$script[]="    jQuery(document).on('dragenter', function (e){ e.stopPropagation(); e.preventDefault();});";
		$script[]="    jQuery(document).on('dragover', function (e){ e.stopPropagation(); e.preventDefault();});";
		$script[]="    jQuery(document).on('drop', function (e){ e.stopPropagation();  e.preventDefault();});";
		$script[]="    jQuery('#".$this->id."_search').keyup(function (e) { if (jQuery('#".$this->id."_search').is(':focus') && (e.keyCode == 13)) {";
		$script[]="    jformfieldfilexplorer_search('".$this->id."'); }});";
		$script[]="} );";
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration("    ".implode("\n    ", $script));
		
			
        return implode("\n",$html);
	}
	
	/**
	 * Wrapper method for getting attributes from the form element
	 *
	 * @param string $attr_name Attribute name
	 * @param mixed  $default   Optional value to return if attribute not found
	 *
	 * @return mixed The value of the attribute if it exists, null otherwise
	 */
	public function getAttribute($attr_name, $default = null)
	{
		if (!empty($this->element[$attr_name]))
		{
			return (string)$this->element[$attr_name];
		}
		else
		{
			return $default;
		}
	}
}