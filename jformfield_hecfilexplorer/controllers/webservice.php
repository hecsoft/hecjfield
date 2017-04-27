<?php
/**
 * @package     JFormField FileXPlorer
 * @subpackage  filexplorer
 *
 * @copyright   Copyright (C) 2005 - 2016 Hervé CYR, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/controller.php';
jimport('joomla.filesystem.file');

// Rename {NOM DU COMPOSANT} with your component name
class {NOM DU COMPOSANT}ControllerWebservice extends {NOM DU COMPOSANT}Controller
{
	
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
	 * 
	 * json format for jformfield filexplorer :
	 *
	 * { 'dir' : [relative dir], 
	 *   'error' : [error],
	 *   'right': [directory right ro|rw] ,  
	 *   'list' : [{ 'type': 'dir|file', 'path': 'file or dir name', 'right': 'ro|rw' , 'mtime' : [file modification time], 'size' : [ file size in octet], 'ext':'file extension' }] 
	 * }
	 *
	 * POST variables:
	 * rootdir : root directory
	 * directory : current directory (to list)
	 * filter : extensions split by |
	 * exclude : file or extensions exclude
	 * mode : 1 file only, 2 dir only , 3 files and dirs
	 */
	function getdir()
	{
		// Check if this is called from the same URL
		if ($this->checkWebServiceOrigine())
		{
			// Get post variables
			$dir=JFactory::getApplication()->input->get('directory','','post');
			$rootdir=JFactory::getApplication()->input->get('rootdir','','post');
			$filter=JFactory::getApplication()->input->get('filter',false,'post');
			$exclude=JFactory::getApplication()->input->get('exclude',false,'post');
			$mode=JFactory::getApplication()->input->getInt('mode',3);
			
			$showfiles=($mode & 1)!=0;
			$showfolders=($mode & 2)!=0;
			
			$filterarr=explode("|", $filter);
			$filter=array();
			foreach($filterarr as $f)
				$filter[$f]=$f;
			$excludearr=explode("|", $exclude);
				$exclude_files=array();
				$exclude_ext=array();
				foreach($excludearr as $f)
					if (substr($f, 0,2)=="*.")
						$exclude_ext[substr($f, 2)]=substr($f, 2);
					else
						$exclude_files[$f]=$f;
			$list=array();
			$params = JComponentHelper::getParams( JFactory::getApplication()->input->get('option', ''));
			$root = realpath(JPATH_ROOT); // Define base directory of all path (for example /images/)
			if ($dir != '')
			{
				$up = array('type'=>'dir','path'=>"..",'ext'=>'dir', 'right'=>'rw');   // Add .. to go to updir (only if in subdir)
				$relatdir = JPath::clean($rootdir.DIRECTORY_SEPARATOR.$dir);
				$dir = JPath::clean($root . $relatdir);
				$rw='rw';
			}
			else
			{
				$relatdir=JPath::clean($rootdir);
				$dir=JPath::clean($root.$relatdir);
				$rw='ro';
				$up=false;
			}
		
			// Open a known directory, and proceed to read its contents
			// DIR is ROOT_PATH+root_dir+dir
			if (is_dir($dir))
			{
				if ($dh = opendir($dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (is_dir($dir .'/'. $file))
						{
							if ($file !='.' && $file!='..' && $showfolders)
							{
								$list[] = array('type'=>'dir','path'=>$file,'right'=> 'rw');
							}
						}
						else 
						{
							if ($file !='.' && $file!='..' && $showfiles)
							{
								$ext= pathinfo($file,PATHINFO_EXTENSION );
								if ($filter) if (!array_key_exists($ext, $filter)) continue;
								if ($exclude_files) if (array_key_exists($file, $exclude_files)) continue;
								if ($exclude_ext) if (array_key_exists($path_parts['extension'], $exclude_ext)) continue;
								$mtime = date ("d/m/Y H:i",filemtime($dir .'/'. $file));
								$fsize= filesize($dir .'/'. $file);
								$list[]= array('type'=>'file','path'=>$file, 'mtime'=>$mtime, 'size'=>$fsize, 'ext'=>$ext = pathinfo($file, PATHINFO_EXTENSION),'right'=> 'rw');
								
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
		if ($up) array_unshift($list,$up);
		$data = array('dir'=> $relatdir, 'list' => $list, 'error'=>$error,'right'=> $rw);
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
	 * Method to delete file or directory
	 *
	 * @access	public
	 *
	 * return json format for jformfield filexplorer :
	 *
	 * { 'return' : true|false , 
	 *   'error' : [error]
	 * }
	 *
	 * POST variables:
	 * directory : current directory (to list)
	 * files : file list to delete
	 */
	function delete()
	{
		$error="";
		
		if ($this->checkWebServiceOrigine())
		{
			$dir=JFactory::getApplication()->input->get('directory','','post');
			$files = JFactory::getApplication()->input->get('files',array(),'array');
			$rootdir = realpath(JPATH_ROOT).DIRECTORY_SEPARATOR.$dir;
			if (count($files)>0) {
				foreach($files as $file) {
					if ($file!=''){
						$filename=$rootdir.DIRECTORY_SEPARATOR.$file;
						
						if (file_exists($filename)) {
							if (is_dir($filename))
							{
								if(!@rmdir($filename))
									$error.="Erreur suppression repertoire ".error_get_last()['message'];
							}
							else {
								if (!@unlink($filename)) {
									$error.="Erreur suppression fichier ".error_get_last()['message'];
								}
							}
						}
						else 
							$error.="Fichier/Répertoire inexistant";
					}
					
					else
					{
						$error.="Mauvais nom de fichier";
					}
				}
			}
			else {
				$error="Aucun fichier";
			}
		}	
		$return = ($error=='');
		$data = array( 'return'=>$return, 'error'=>$error);
		// Get the document object.
		$document =JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="delete.json"');
		
		// Output the JSON data.
		echo json_encode($data);
		
		exit;
	}
	
	/**
	 * Method to rename a file or directory
	 *
	 * @access	public
	 *
	 * return json format for jformfield filexplorer :
	 *
	 * { 'return' : true|false , 
	 *   'error' : [error]
	 * }
	 *
	 * POST variables:
	 * directory : current directory (to list)
	 * oldfile : old file to rename
	 * newfile : new filename
	 */
	function rename()
	{
		$error="";
	
		if ($this->checkWebServiceOrigine())
		{
			$dir=JFactory::getApplication()->input->get('directory','','post');
			$oldfile = JFactory::getApplication()->input->get('oldfile','','post');
			$newfile = JFactory::getApplication()->input->get('newfile','','post');
			$rootdir = realpath(JPATH_ROOT).DIRECTORY_SEPARATOR.$dir;
			if ($oldfile!='' && $newfile!='') {
				$oldfilename=$rootdir.DIRECTORY_SEPARATOR.$oldfile;
				$newfilename=$rootdir.DIRECTORY_SEPARATOR.$newfile;
				if (file_exists($newfilename)) {
					$error=$newfile. " existe déjà!";
				}
				else 
				{
					if (file_exists($oldfilename)) {
						if (!rename($oldfilename,$newfilename)) {
							$error.="Erreur suppression de ".$filename;
						}
					}
					else
						$error.="Fichier ".$file." inexistant";
				}
			}
			else {
				$error="Aucun fichier";
			}
		}
		$return = ($error=='');
		$data = array( 'return'=>$return, 'error'=>$error);
		// Get the document object.
		$document =JFactory::getDocument();
	
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
	
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="rename.json"');
	
		// Output the JSON data.
		echo json_encode($data);
	
		exit;
	}
	
	
	/**
	 * Method to upload one or more files
	 *
	 * @access	public
	 *
	 * return json format for jformfield filexplorer :
	 *
	 * { 'return' : true|false , 
	 *   'error' : [error]
	 * }
	 *
	 * POST variables:
	 * directory : current directory (to list)
	 * uploadedfiles : list of uploaded files
	 */
	function upload()
	{
		
		$params = JComponentHelper::getParams(JFactory::getApplication()->input->get('option', ''));
		$rootdir = realpath(JPATH_ROOT);
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
		$data = array('return'=> $error=="", 'error'=>$error);
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
	
}

?>