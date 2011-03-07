<?php
/**
 * Description of PHP file wsp\class\display\advanced_object\treeview\TreeViewFolder.class.php
 * Class TreeViewFolder
 *
 * WebSite-PHP : PHP Framework 100% object (http://www.website-php.com)
 * Copyright (c) 2009-2011 WebSite-PHP.com
 * PHP versions >= 5.2
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Emilien MOREL <admin@website-php.com>
 * @link        http://www.website-php.com
 * @copyright   WebSite-PHP.com 22/10/2010
 *
 * @version     1.0.40
 * @access      public
 * @since       1.0.17
 */

include_once("TreeViewItem.class.php");

class TreeViewFolder extends TreeViewItem {
	private $treeview_object = null;
	
	/**
	 * Constructor TreeViewFolder
	 * @param mixed $value 
	 * @param mixed $path_folder 
	 */
	function __construct($value, $path_folder) {
		parent::__construct($value, '', false, $path_folder);
	}
	
	/**
	 * Method getLocalPath
	 * @access public
	 * @return string
	 * @since 1.0.35
	 */
	public function getLocalPath() {
		$path = $this->getPath();
		if ($this->treeview_object == null) {
			$this->treeview_object = $this->getTreeViewObject();
		}
		if ($this->treeview_object->isRootFolder()) {
			$path = substr($path, find($path, "/", 0, 0), strlen($path));
		}
		return str_replace("\\", "/", $this->treeview_object->getLoadedPath().$path);
	}
	
	/**
	 * Method addFolder
	 * @access public
	 * @param string $value [default value: New Folder]
	 * @param mixed $context_menu_object [default value: null]
	 * @param boolean $collapse [default value: true]
	 * @return mixed
	 * @since 1.0.35
	 */
	public function addFolder($value="New Folder", $context_menu_object=null, $collapse=true) {
		$path = $this->getLocalPath();
		
		if ($this->treeview_object == null) {
			$this->treeview_object = $this->getTreeViewObject();
		}
		$is_dir_ok = true;
		if ($this->treeview_object->isSynchronizeWithDir()) {
			if (!mkdir($path.$value."/")) {
				$is_dir_ok = false;
			}
		}
		
		if ($is_dir_ok) {
			$new_item = new TreeViewFolder($value, $path.$value."/");
			$this->addItem($new_item);
			if ($collapse) {
				$new_item->collapse();
			}
			if ($context_menu_object != null) {
				$this->treeview_object->setContextMenuOnTreeViewItem($context_menu_object, $new_item);
			}
			
			return $new_item;
		}
		return null;
	}
	
	/**
	 * Method addFile
	 * @access public
	 * @param string $value [default value: New File]
	 * @param string $data 
	 * @param string $link 
	 * @param mixed $context_menu_object [default value: null]
	 * @return mixed
	 * @since 1.0.35
	 */
	public function addFile($value="New File", $data='', $link='', $context_menu_object=null) {
		$path = $this->getLocalPath();
		$file = new File($path.$value);
		
		if ($this->treeview_object == null) {
			$this->treeview_object = $this->getTreeViewObject();
		}
		$is_file_ok = true;
		if ($this->treeview_object->isSynchronizeWithDir()) {
			if ($data == "") { $data  = " "; }
			if ($file->write($data) == false) {
				$is_file_ok = false;
			}
		}
		
		if ($is_file_ok) {
			$new_item = new TreeViewFile($value, $path.$value, $link);
			$this->addItem($new_item);
			if ($context_menu_object != null) {
				$this->treeview_object->setContextMenuOnTreeViewItem($context_menu_object, $new_item);
			}
			$file->close();
			return $new_item;
		}
		return null;
	}
	
	/**
	 * Method remove
	 * @access public
	 */
	public function remove() {
		$path = $this->getLocalPath();
		
		if ($this->treeview_object == null) {
			$this->treeview_object = $this->getTreeViewObject();
		}
		$is_dir_ok = true;
		if ($this->treeview_object->isSynchronizeWithDir()) {
			if (is_dir($path)) {
				if (!$this->rrmdir($path)) {
					$is_dir_ok = false;
				}
			} else {
				$is_dir_ok = false;
			}
		}
		
		if ($is_dir_ok) {
			$this->removeItem();
		}
	}
	
	/**
	 * Method rrmdir
	 * @access private
	 * @param mixed $dir 
	 * @return boolean
	 * @since 1.0.35
	 */
	private function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			return rmdir($dir);
		}
		return false;
	} 
	
	/**
	 * Method rename
	 * @access public
	 * @param mixed $value 
	 */
	public function rename($value) {
		if (!$this->nodeValueAlreadyExists($value)) {
			$path = $this->getLocalPath();
		
			if ($this->treeview_object == null) {
				$this->treeview_object = $this->getTreeViewObject();
			}
			$is_dir_ok = true;
			if ($this->treeview_object->isSynchronizeWithDir()) {
				if (is_dir($path)) {
					if (!rename($path, str_replace("/".$this->getValue()."/", "/".$value."/", $path))) {
						$is_dir_ok = false;
					}
				} else {
					$is_dir_ok = false;
				}
			}
		
			if ($is_dir_ok) {
				$this->setValue($value);
			}
		}
	}
}
?>
