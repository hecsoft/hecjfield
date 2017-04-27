<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/*
* NOTE : Replace {MyComponent} by your component name 
*/

defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/controller.php';
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */
class {MyComponent}ControllerAttachments extends {MyComponent}Controller
{
	
	
	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;
	
		// Check acces right (for send email)
		$params = JComponentHelper::getParams( 'com_hecmailing' );
		$usr =JFactory::getUser();
		parent::__construct($config);
		// Check if current user is in authorized joomla groups
		$adminType = $params->get('usertype','ADMINISTRATOR;SUPER ADMINISTRATOR');
		
		$modelbase = $this->getModel("Form","HecMailingModel");
		
		// Check if current user is in admin hec Mailing group (groupaccess hec Mailing parameter)
		$usrgrp = $params->get('groupaccess','MailingGroup');
			
		if (!$modelbase->isAdminUserType($adminType) && !$modelbase->isInGroupe($usrgrp) && !$modelbase->hasGroupe())
		{
			$msg=JText::sprintf("COM_HECMAILING_NO_RIGHT",$usrgrp);
			$return = JURI::root();		// redirect to home if no right
			$this->setRedirect( $return, $msg );
		}
		
	}
	
	
	/**
	 * Method to check if source page is this website page
	 *
	 * @access	public
	 * @return true if page belong this website false else
	 */
	
	function checkWebServiceOrigine()
	{
		return true;
		$user = JFactory::getUser();
		$user->guest==0 or die("|NOT ALLOWED|");
		if (isset($_SERVER['HTTP_REFERER']))
			$ref = $_SERVER['HTTP_REFERER'];
		else
			$ref="";
		$uri = $_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
		$ref_tab = explode('/', $ref);
		$ser_tab = explode('/', $uri);
		$uri_serveur='';
		$j=2;
		$ok=true;
	
		for ($i=0;$i<count($ser_tab)-4;$i++)
		{
			if ($ref_tab[$j]!=$ser_tab[$i])
			{
				$ok=false;
				break;
			}
			$j++;
		}
		return $ok;
	}
	/**
	 * Method to return directory content for Browse WebService
	 *
	 * @access	public
	 */
	
	function getdir()
	{
		if ($this->checkWebServiceOrigine())
		{
			if (array_key_exists("dir", $_POST))
			{
				$dir = $_POST["dir"];
			}
			else
			{
				$dir= JRequest::getVar('dir','');;
			}
			$list=array();
			$params = JComponentHelper::getParams( '{MyComponent}' );
			$root = realpath(JPATH_ROOT).DS.$params->get('browse_path','images');
			if ($dir != '')
			{
				$list[] = array('type'=>'dir','path'=>"..");
				$relatdir = $dir;
				$dir = $root . $dir;
			}
			else
			{
				$relatdir="";
				$dir=$root;
			}
		
			// Open a known directory, and proceed to read its contents
			if (is_dir($dir))
			{
				if ($dh = opendir($dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (is_dir($dir .'/'. $file))
						{
							if ($file !='.' && $file!='..')
							{
								$list[] = array('type'=>'dir','path'=>$file);
							}
						}
						else 
						{
							if ($file !='.' && $file!='..')
							{
								$mtime = date ("d/m/Y H:i",filemtime($dir .'/'. $file));
								$fsize= filesize($dir .'/'. $file);
								$list[]= array('type'=>'file','path'=>$file, 'mtime'=>$mtime, 'size'=>$fsize);
								
							}
						}
					}
					closedir($dh);
				}
			}
			$error="";
		}
		else
		{
			$list[] = array();
			$error=JText::_("COM_HECMAILING_WEBSERVICE_NOTALLOWED");
		}
		sort($list);
		$data = array('dir'=> $relatdir, 'list' => $list, 'error'=>$error);
		// Get the document object.
		$document =JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="getdirlist.json"');
		
		// Output the JSON data.
		echo json_encode($data);
		
		exit;
	}
	
	/**
	 * Method to return directory content for Browse WebService
	 *
	 * @access	public
	 */
	
	function upload()
	{
		$params = JComponentHelper::getParams( '{MyComponent}' );
		$rootdir = realpath(JPATH_ROOT).DS.$params->get('browse_path','images');
		$list= array();
		$app=JFactory::getApplication();
		if ($this->checkWebServiceOrigine())
		{
			$dir = $app->input->getString("directory","");
			//$files = $app->input->files->get("uploadedfiles");
			$files=$_FILES["uploadedfiles"];
			if($files)
			//foreach ($files as $file) {
			for ($i=0;$i<count($files['name']);$i++) {
				
				// Get uploaded files
				$filename = JFile::makeSafe($files['name'][$i]);
				$src = $files['tmp_name'][$i];
				if ($src!='')
				{
					//Set up the source and destination of the file
					if ($dir!="")
						$dest = $rootdir.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$filename;
					else 
						$dest = $rootdir.DIRECTORY_SEPARATOR.$filename;
					// Upload uploaded file to attchment directory (temp or saved dir)
					JFile::upload($src, $dest, false,true);
					$list[$filename]=$filename;
				}
			}
			$error="";
		}
		else
		{
			$list[] = array();
			$error=JText::_("COM_HECMAILING_WEBSERVICE_NOTALLOWED");
		}
		sort($list);
		$data = array('dir'=> $dir, 'selected' => $list, 'error'=>$error);
		// Get the document object.
		$document =JFactory::getDocument();
	
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
	
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="uploaded.json"');
	
		// Output the JSON data.
		echo json_encode($data);
	
		exit;
	}
	
	
	
	function getGroupContent()
	{
		if (checkWebServiceOrigine())
		{
			$currentGroup = JRequest::getVar('groupid', 0, 'get', 'int');
			$groupType = JRequest::getVar('grouptype', 0, 'get', 'int');
			$db =JFactory::getDBO();
			switch($groupType)
			{
				case 3:
					if(version_compare(JVERSION,'1.6.0','<')){
						//Code pour Joomla! 1.5
						$query = "Select id, name From #__core_acl_aro_groups order by id";
					}
					else
					{
						//Code pour Joomla >= 1.6.0
						$query = "SELECT id, title FROM  #__usergroups  ORDER BY id";
					}
					break;
				case 5:
					$query = "Select grp_id_groupe, grp_nm_groupe FROM #__hecmailing_groups WHERE grp_id_groupe!=".$currentGroup." ORDER BY grp_nm_groupe";
					break;
			}
			$db->setQuery( $query );
			if (!$db->query()) {
				$data = array(array('-1', JText::_('MSG_ERROR_SAVE_CONTACT').':'.$query.'/'.$db->getErrorMsg(true)));
			}
			else
				$data = $db->loadRowList();
		}
		else
		{
			$data = array(array('0','NOT ALLOWED'));
		}
			
		// Get the document object.
		$document =& JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="group'.$groupType.'.json"');
		
		// Output the JSON data.
		echo json_encode($data);
		exit;
		
		
	}
	
	function mail_read()
	{
		$app = JFactory::getApplication();
		$mail_id=$app->input->getInt("mail_id", 0 );
		$email=$app->input->getInt("email", 0 );
		
		$db = JFactory::getDbo();
		$query = "UPDATE #__hecmailing_log_user SET status=2, timestamp=sysdate() WHERE log_id_message=" . $mail_id . " AND email=".$db->quote($email);
		$db->setQuery($query);
		$db->query();
		$file=JPATH_COMPONENT."/images/pix.png";
		$image= imagecreatefrompng($file);
		header("Content-Type: image/png" );
		imagepng($image);
		exit;
		
	}
}

?>