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
class JFormFieldDataTable extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'datatable';
	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected static $initialised = false;
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$doc = JFactory::getDocument();
		$fields = $this->loadForm();
		
		
		$component_path = Juri::base(true)."/components/".JFactory::getApplication()->input->get('option', '');
		$doc->addStyleSheet($component_path."/libraries/datatables/datatables.min.css");
		$doc->addScript($component_path."/libraries/datatables/datatables.min.js");
		$doc->addStyleSheet("//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css");
		$lib_addrow = JText::_($this->getAttribute("label_addrow"),JText::_("JOK"));
		$lib_addrow_title = JText::_($this->getAttribute("label_addrow_title"),"Add row");
		$class=$this->getAttribute("class","jformfield_datatable");
		$height=$this->getAttribute("height","600");
		$searching=$this->getAttribute("searching","false");
		$ordering=$this->getAttribute("ordering","true");
		$width=$this->getAttribute("width","600");
		$cancelimg=$component_path."/assets/images/cancel16.png";
		$editimg=$component_path."/assets/images/folder16.png";
		$fileimg=$component_path."/assets/images/file16.png";
		$loaderimg=$component_path."/assets/images/ajax-loader.gif";

		// Manage language
		$lang = JFactory::getLanguage();
		$langtag = $lang->getTag();
		$langtag= explode("-",$langtag)[0];
		
		$langfilelocation=JPATH_COMPONENT."/libraries/datatables/lang-".$langtag.".json";
		if (JFile::exists($langfilelocation))
			$langfile = $component_path."/libraries/datatables/lang-".$langtag.".json";
		else
			$langfile=$component_path."/libraries/datatables/lang-default.json";
		
		// Create row template
		$cols=array();
		$header=array();
		$fieldnames=array();
		foreach($fields as $field)
		{
			//$cols[]="<td><input type=\"checkbox\" name=\"".$this->name."[checked][]\" id=\"".$this->id."{num}_checked\" checked=\"checked\" value=\"{id}\" >";
			$name=$field->id;
			$fieldnames[]=$name;
			if (!$field->hidden)
			{
				if ($field->element['columnname'])
					$columnname=(string)$field->element['columnname'];
				else
					$columnname=(string)$field->element['label'];
				$header[]="<th>".JText::_($columnname)."</th>";
				$cols[]="<td><input type=\"hidden\" name=\"".$this->name."[$name][]\" id=\"".$this->id."{num}_$name\" value=\"{".$name."}\">{".$name."}</td>";
			}
			else
				$cols[]="<input type=\"hidden\" name=\"".$this->name."[$name][]\" id=\"".$this->id."{num}_$name\" value=\"{".$name."}\">";
		}
		$header[]="<th></th>";
		$cols[]="<td><button onclick=\"javascript:jformfielddatatable_openAddDialog({quote}".$this->id."{quote}, {quote}{num}{quote});return false;\" /><span class=\"icon-edit\" ></span></button>";
		$cols[]="<button onclick=\"javascript:jformfielddatatable_removeRow({quote}".$this->id."{quote}, {quote}{num}{quote});return false;\" /><span class=\"icon-remove\" ></span></button></td>";
		$cols[]="<input type=\"hidden\" name=\"".$this->name."[rowstatus][]\" id=\"".$this->id."{num}_rowstatus\" value=\"{rowstatus}\">";
		$rowContent=implode("",$cols);
		
		if (!self::$initialised)
		{
			// Load the modal behavior script.
			//JHtml::_('behavior.modal');
			// Build the script : Based on JFormFieldMedia object of Joomla Core.
			$script = array();
			$script[]="";
			$script[]="function jformfielddatatable_openAddDialog(id, current) {";
			$script[]="    document.getElementById(id+'_currentrow').value=current;";
			foreach($fields as $field) {
				$dlgid=$field->id;
				$script[]="    if (current!='') document.getElementById('$dlgid').value=document.getElementById(id+current+'_$dlgid').value;";
				$script[]="    else document.getElementById('$dlgid').value='';";
			}
			$script[]="    jQuery( \"#\"+id+\"_addRowDlg\" ).dialog({resizable: false, height:".$height.",width : ".$width.",modal: true, draggable: true});";
			$script[]="    return false; }";
			$script[]="function jformfielddatatable_removeRow(id, row) {";
			$script[]="    jQuery('#'+id+'_confirmeRemoveRow_id').val(row);";
			$script[]="    jQuery( \"#\"+id+\"_confirmeRemoveRow\" ).dialog({resizable: false, height:150,width : 300,modal: true, draggable: true});";
			$script[]="    return false; }";
			$script[]="function jformfielddatatable_confirmRemoveRow(id) {";
			$script[]="    var row=jQuery('#'+id+'_confirmeRemoveRow_id').val();";
			$script[]="    jQuery( \"#\"+id+\"_row\"+row ).hide();";
			$script[]="    jQuery( \"#\"+id+row+\"_rowstatus\" ).val('deleted');";
			$script[]="    jQuery( \"#\"+id+\"_confirmeRemoveRow\" ).dialog('close');";
			$script[]="    return false; }";
			$script[]="function jformfielddatatable_cancelRemoveRow(id) {";
			$script[]="    jQuery( \"#\"+id+\"_confirmeRemoveRow\" ).dialog('close');";
			$script[]="}";
			$script[]="// Add/Update Row";
			$script[]="function jformfielddatatable_addRow(id) {"; 
			$script[]="    var currentRow=document.getElementById(id+'_currentrow').value;";
			$script[]="    var tab=document.getElementById(id+'_datatablebody');";
			$script[]="    if(currentRow=='') {";
			$script[]="        var maxidEl=document.getElementById(id+'_maxid');";
			$script[]="        var row=document.createElement('tr');";
			$script[]="        var num=parseInt(maxidEl.value)+1";
			$script[]="        var newid=id+num;";
			$script[]="        var name='".$this->name."[]';";
			$script[]="        row.id=id+'_row'+num;";
			$script[]="        maxidEl.value=num;";
			$script[]="        tab.appendChild(row);";
			$script[]="        var rowStatus='new';";
			$script[]="    } else {";
			$script[]="        var row=document.getElementById(id+'_row'+currentRow);";
			$script[]="        var rowStatus='updated';";
			$script[]="    }";
			$script[]="    rowContent='".$rowContent."';";
			$script[]="    rowContent=rowContent.replace(/{num}/g,num);";
			$script[]="    rowContent=rowContent.replace(/{rowstatus}/g,rowStatus);";
			$script[]="    rowContent=rowContent.replace(/{quote}/g,\"'\");";
			foreach($fields as $field) {
				$dlgid=$field->id;
				$name=$field->name;
				$script[]="    rowContent=rowContent.replace(/{".$name."}/g,document.getElementById('$dlgid').value);";
			}
			$script[]="    row.innerHTML=rowContent;";
			$script[]="    jQuery( '#'+id+'_addRowDlg').dialog('close');";
			$script[]="}";
			$script[]="jQuery.extend( true, jQuery.fn.dataTable.defaults, {";
    		$script[]="    'searching':$searching,'ordering':$ordering } );";
			$script[]="jQuery(document).ready(function() {";
			$script[]="    jQuery('#".$this->id."_datatabletable').DataTable({";
        	$script[]="        'scrollY':'".$height."','scrollCollapse': true,'paging': false, 'language': {";
            $script[]="        'url': '".$langfile."',  'dom': '<\"toolbar\">frtip'  } } );";
            $script[]="    jQuery('div.toolbar').html('<button onclick=\"javascript: jformfielddatatable_openAddDialog(\'".$this->id."\'); return false;\" /><span class=\"icon-add\">+</span> ".$lib_addrow."</button>');";
			$script[]="} );";
			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n    ", $script));
			
			$css=array();
			$css[] = ".toolbar { float: left; }";
			$doc->addStyleDeclaration(implode("", $css));	
			
			self::$initialised = true;
		}
		
		// Initialize variables.
		
		$html = array();
		$html[]="<div id=\"".$this->id."_confirmeRemoveRow\" style=\"display:none\" title=\"".JText::_('JFORMFIELD_DATATABLE_REMOVE_ROW_TITLE')."\">";
		$html[]="  <div style=\"float:left\"><img src=\"".$component_path."/assets/images/messagebox_question.png\" /></div><div id=\"".$this->id."_content\" style=\"margin-left:70px;\">";
		$html[]="    ".JText::_('JFORMFIELD_DATATABLE_REMOVE_ROW_DESC');
		$html[]="  </div><input type=\"hidden\" id=\"".$this->id."_confirmeRemoveRow_id\" value=\"\">";
		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons center\" style=\"margin-top:30px;\">";
		$html[]="    <button onclick=\"javascript:jformfielddatatable_confirmRemoveRow('".$this->id."');return false;\"><span class=\"icon-ok\"></span>".JText::_('JFORMFIELD_DATATABLE_REMOVE')."</button>";
		$html[]="    <span class=\"icon-close\"></span><button onclick=\"javascript:jformfielddatatable_cancelRemoveRow('".$this->id."');return false;\"><img src=\"".$component_path."/assets/images/cancel16.png\" >".JText::_('JFORMFIELD_DATATABLE_CANCEL')."</button>";
		$html[]="  </div>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_ErrorMessage\" style=\"display:none\" title=\"".JText::_('JFORMFIELD_DATATABLE_ERROR_TITLE')."\">";
		$html[]="  <div id=\"".$this->id."_ErrorMessageContent\" class=\"content\" ></div>";
		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons align-center\">";
		$html[]="    <button onclick=\"javascript:jQuery('#".$this->id."_ErrorMessage').dialog.close();return false;\"><span class=\"icon-close\"></span>".JText::_('JFORMFIELD_DATATABLE_OK')."</button>";
		$html[]="  </div>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_addRowDlg\" style=\"display:none\" title=\"".$lib_addrow_title."\" class=\"jformfielddatatable_addrow\" >";
		$html[]="  <div class=\"content\" ><input type='hidden' id='".$this->id."_currentrow' value='' />";
		foreach ($fields as $field)
		{
			
			$html[]="    <div><div class=\"control-group\">".$field->getLabel()."</div>";
			$html[]="    <div class=\"controls\">".$field->getInput()."</div></div>";
		}
		//$html[]="    <div class=\"control-group\"><div class=\"control-label\"><label id=\"".$this->id."_addRowDlg_name-lbl\" for=\"".$this->id."_addRowDlg_name\" class=\"required\">Name<span class=\"star\">&#160;*</span></label></div>";
		//$html[]="    <div class=\"controls\"><input type=\"text\" id=\"".$this->id."_addRowDlg_name\" value=\"\" /></div></div>";
		//$html[]="    <div class=\"control-group\"><div class=\"control-label\"><label id=\"jform_value-lbl\" for=\"".$this->id."_addRowDlg_value\" >Value<span class=\"star\">&#160;*</span></label></div>";
		//$html[]="    <div class=\"controls\"><input type=\"text\" id=\"".$this->id."_addRowDlg_value\" value=\"\"  /></div></div>";
  		$html[]="  </div>";
  		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons\">";
    	$html[]="    <button onclick=\"javascript:jformfielddatatable_addRow('".$this->id."');return false;\"><img src=\"".$component_path."/assets/images/ok16.png\" >".$lib_addrow."</button>";
    	$html[]="    <span class=\"icon-close\"></span><button onclick=\"javascript:jQuery('#".$this->id."_addRowDlg').dialog.close();return false;\"><img src=\"".$component_path."/assets/images/cancel16.png\" >".JText::_('JCANCEL')."</button>";
    	$html[]="  <br/></div>";
    	$html[]="</div>";
        $html[]="<div id='".$this->id."_container' class='".$class."'>";
        $html[]="<div id='".$this->id."_header' class='".$class."_header'>";

		$html[]="<button onclick='javascript: jformfielddatatable_openAddDialog(\"".$this->id."\",\"\"); return false;' /><span class=\"icon-new\"></span> ".$lib_addrow."</button>";
		//$html[]=$media->renderField();
    	$html[]="</div><div id='".$this->id."_list' class=''".$class."_list'>";
    	$html[]="<table id='".$this->id."_datatabletable' class='".$class."_table' style=\"width:100%\" >";
    	    	
    	$html[]="<thead><tr>".implode("", $header)."</tr></thead>";
    	$html[]="<tbody id='".$this->id."_datatablebody' >";
    	$ilocal=0;
    	$iattach=0;
    	if ($this->value)
    	foreach($this->value as $row)
    	{
    		$ilocal++;
    		$colContent=implode("\n",$cols);
    		$colContent=str_replace("{num}", "$ilocal", $colContent);
    		$colContent=str_replace("{quote}", "'", $colContent);
    		$colContent=str_replace("{rowstatus}", "not_changed", $colContent);
    		foreach($fieldnames as $fieldname) {
    			$colContent=str_replace("{".$fieldname."}", $row[$fieldname], $colContent);
    		}
    		$html[]="<tr id=\"".$this->id."_row".$ilocal."\" >".$colContent."</tr>";
    	}
  		$html[]="</tbody></table></div></div><input type=\"hidden\" name='".$this->id."_maxid' id='".$this->id."_maxid' value='".$ilocal."' >";
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
			return $this->element[$attr_name];
		}
		else
		{
			return $default;
		}
	}
	
	private function loadForm()
	{
		$this->form = new JForm($this->name.'_form',array());
		$fieldset=$this->element->children()[0];
		$this->form->load($fieldset->asXML());
		$this->fields= $this->form->getFieldset();
		
		return $this->fields;
	}
}