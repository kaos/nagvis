<?php
/*****************************************************************************
 *
 * WuiMapCfg.php - Class for handling the map configuration in WUI
 *
 * Copyright (c) 2004-2010 NagVis Project (Contact: info@nagvis.org)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/
 
/**
 * @author	Lars Michelsen <lars@vertical-visions.de>
 */
class WuiMapCfg extends GlobalMapCfg {
	
	/**
	 * Class Constructor
	 *
	 * @param	WuiCore	$CORE	
	 * @param	String			$name		Name of the map
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	function WuiMapCfg($CORE, $name='') {
		$this->CORE = $CORE;
		
		$this->name	= $name;
		
		$this->getMap();
		
		if($this->name != '')
			$this->mapLockPath = $this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$this->name.'.lock';
		else
			$this->mapLockPath = '';
		
		parent::__construct($CORE, $this->name);
	}
	
	/**
	 * Reads which map should be displayed, primary use
	 * the map defined in the url, if there is no map
	 * in url, use first entry of "maps" defined in 
	 * the NagVis main config
	 *
	 * @author	Lars Michelsen <lars@vertical-visions.de>
     */
	function getMap() {
		// check the $this->name string for security reasons (its the ONLY value we get directly from external...)
		// Allow ONLY Characters, Numbers, - and _ inside the Name of a Map
		$this->name = preg_replace("/[^a-zA-Z0-9_-]/",'',$this->name);
	}
	
	/**
	 * Gets all information about an object type
	 *
	 * @param   String  Type to get the information for
	 * @return  Array   The validConfig array
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	function getValidObjectType($type) {
		return self::$validConfig[$type];
	}
	
	/**
	 * Gets the valid configuration array
	 *
	 * @return	Array The validConfig array
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function getValidConfig() {
		return self::$validConfig;
	}
	
	/**
	 * Reads the configuration file of the map and 
	 * sends it as download to the client.
	 *
	 * @return	Boolean   Only returns FALSE if something went wrong
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	function exportMap() {
		if($this->checkMapConfigReadable(1)) {
			$mapPath = $this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$this->getName().'.cfg';
			
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$this->getName().'.cfg');
			header('Content-Length: '.filesize($mapPath));
			
			if(readfile($mapPath)) {
				exit;
			} else {
				return FALSE;	
			}
		} else {
			return FALSE;	
		}
	}
	
	/**
	 * Deletes the map configfile
	 *
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function deleteMapConfig($printErr=1) {
		// is file writeable?
		if($this->checkMapConfigWriteable($printErr)) {
			if(unlink($this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$this->name.'.cfg')) {
				// Also remove cache file
				if(file_exists($this->cacheFile))
					unlink($this->cacheFile);
				
				// And also remove the permission
				GlobalCore::getInstance()->getAuthorization()->deletePermission('Map', $this->name);
				
				return TRUE;
			} else {
				if($printErr) {
					new GlobalMessage('ERROR', $this->CORE->getLang()->getText('couldNotDeleteMapCfg','MAPPATH~'.$this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$this->name.'.cfg'));
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks for writeable map config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function checkMapConfigWriteable($printErr) {
		$path = $this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$this->name.'.cfg';
		return GlobalCore::getInstance()->checkWriteable($path, $printErr);
	}
	
	/**
	 * Writes the element from array to the config file
	 *
	 * @param	String	$type	Type of the Element
	 * @param	Integer	$id		Id of the Element
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function writeElement($type,$id) {
		if($this->checkMapConfigExists(1) && $this->checkMapConfigReadable(1) && $this->checkMapConfigWriteable(1)) {
			// read file in array
			$file = file($this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$this->name.'.cfg');
			
			// number of lines in the file
			$l = 0;
			// number of elements of the given type
			$a = 0;
			// done?!
			$done = FALSE;
			while(isset($file[$l]) && $file[$l] != '' && $done == FALSE) {
				// ignore comments
				if(!preg_match('/^#/',$file[$l]) && !preg_match('/^;/',$file[$l])) { 
					$defineCln = explode('{', $file[$l]);
					$define = explode(' ',$defineCln[0]);
					// select only elements of the given type
					if(isset($define[1]) && trim($define[1]) == $type) {
						// check if element exists
						if($a == $id) {
							// check if element is an array...
							if(is_array($this->mapConfig[$type][$a])) {
								// ...array: update!
								
								// choose first parameter line
								$l++;
								
								// Loop all parameters from array
								foreach($this->mapConfig[$type][$id] AS $key => $val) {
									// if key is not type
									if($key != 'type' && $key != 'object_id') {
										$cfgLines = 0;
										$cfgLine = '';
										$cfgLineNr = 0;
										
										// Loop parameters from file (Find line for this option)
										while(isset($file[($l+$cfgLines)]) && trim($file[($l+$cfgLines)]) != '}') {
											$entry = explode('=',$file[$l+$cfgLines], 2);
											if($key == trim($entry[0])) {
												$cfgLineNr = $l+$cfgLines;
												if(is_array($val)) {
													$val = implode(',',$val);
												}
												$cfgLine = $key.'='.$val."\n";
											}
											$cfgLines++;	
										}
										
										if($cfgLineNr != 0 && $val != '') {
											// if a parameter was found in file and value is not empty, replace line
											$file[$cfgLineNr] = $cfgLine;
										} elseif($cfgLineNr != 0 && $val == '') {
											// if a paremter is not in array or a value is empty, delete the line in the file
											$file[$cfgLineNr] = '';
											$cfgLines--;
										} elseif($cfgLineNr == 0 && $val != '') {
											// if a parameter is was not found in array and a value is not empty, create line
											if(is_array($val)) {
												$val = implode(',',$val);
											}
											$neu = $key.'='.$val."\n";
											
											for($i = $l; $i < count($file);$i++) {
												$tmp = $file[$i];
												$file[$i] = $neu;
												$neu = $tmp;
											}
											$file[count($file)] = $neu;
										} elseif($cfgLineNr == 0 && $val == '') {
											// if a parameter is empty and a value is empty, do nothing
										}
									}
								}
								$l++;
							} else {
								// ...no array: delete!
								$cfgLines = 0;
								while(trim($file[($l+$cfgLines)]) != '}') {
									$cfgLines++;
								}
								$cfgLines++;
								
								for($i = $l; $i <= $l+$cfgLines;$i++) {
									unset($file[$i]);	
								}
							}
							
							$done = TRUE;
						}
						$a++;
					}
				}
				$l++;	
			}
			
			// reached end of file - couldn't find that element, create a new one...
			if($done == FALSE) {
				if(count($file) > 0 && $file[count($file)-1] != "\n") {
					$file[] = "\n";
				}
				$file[] = 'define '.$type." {\n";

				// Templates need a special handling here cause they can have all types
				// of options. So read all keys which are currently set
				if($type !== 'template') {
					$aKeys = $this->getValidTypeKeys($type);
				} else {
					$aKeys = array_keys($this->mapConfig[$type][$id]);
				}
				
				foreach($aKeys As $key) {
					$val = $this->getValue($type, $id, $key, TRUE);
					if(isset($val) && $val != '') {
						$file[] = $key.'='.$val."\n";
					}
				}
				$file[] = "}\n";
				$file[] = "\n";
			}
			
			// open file for writing and replace it
			$fp = fopen($this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$this->name.'.cfg','w');
			fwrite($fp,implode('',$file));
			fclose($fp);
			
			// Also remove cache file
			if(file_exists($this->cacheFile))
				unlink($this->cacheFile);
			
			return TRUE;
		} else {
		 			return FALSE;
		} 
	}
	
	/**
	 * Gets lockfile information
	 *
	 * @param	Boolean $printErr
	 * @return	Array/Boolean   Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
   */
	function checkMapLocked($printErr=1) {
		// read lockfile
		$lockdata = $this->readMapLock();
		if(is_array($lockdata)) {
			// Only check locks which are not too old
			if(time() - $lockdata['time'] < $this->CORE->getMainCfg()->getValue('wui','maplocktime') * 60) {
				// there is a lock and it should be recognized
				// check if this is the lock of the current user (Happens e.g. by pressing F5)
				if(GlobalCore::getInstance()->getAuthentication()->getUser() == $lockdata['user']
																						&& $_SERVER['REMOTE_ADDR'] == $lockdata['ip']) {
					// refresh the lock (write a new lock)
					$this->writeMapLock();
					// it's locked by the current user, so it's not locked for him
					return FALSE;
				}
				
				// message the user that there is a lock by another user,
				// the user can decide wether he want's to override it or not
				if($printErr == 1)
					print '<script>if(!confirm(\''.str_replace("\n", "\\n", $this->CORE->getLang()->getText('mapLocked',
									Array('MAP' =>  $this->name,       'TIME' => date('d.m.Y H:i', $lockdata['time']),
												'USER' => $lockdata['user'], 'IP' =>   $lockdata['ip']))).'\', \'\')) { history.back(); }</script>';
				return TRUE;
			} else {
				// delete lockfile & continue
				// try to delete map lock, if nothing to delete its OK
				$this->deleteMapLock();
				return FALSE;
			}
		} else {
			// no valid information in lock or no lock there
			// try to delete map lock, if nothing to delete its OK
			$this->deleteMapLock();
			return FALSE;
		}
	}
	
	/**
	 * Reads the contents of the lockfile
	 *
	 * @return	Array/Boolean   Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function readMapLock() {
		if($this->checkMapLockReadable(0)) {
			$fileContent = file($this->mapLockPath);
			// only recognize the first line, explode it by :
			$arrContent = explode(':',$fileContent[0]);
			// if there are more elements in the array it is OK
			if(count($arrContent) > 0) {
				return Array('time' => $arrContent[0], 'user' => $arrContent[1], 'ip' => trim($arrContent[2]));
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Writes the lockfile for a map
	 *
	 * @return	Boolean     Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function writeMapLock() {
		// Can an existing lock be updated?
		if($this->checkMapLockExists(0) && !$this->checkMapLockWriteable(0))
			return false;

		// If no map lock exists: Can a new one be created?
		if(!$this->checkMapLockExists(0) && !GlobalCore::getInstance()->checkWriteable(dirname($this->mapLockPath), 0))
			return false;

		// open file for writing and insert the needed information
		$fp = fopen($this->mapLockPath, 'w');
		fwrite($fp, time() . ':' . GlobalCore::getInstance()->getAuthentication()->getUser() . ':' . $_SERVER['REMOTE_ADDR']);
		fclose($fp);
		return true;
	}
	
	/**
	 * Deletes the lockfile for a map
	 *
	 * @return	Boolean     Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function deleteMapLock() {
		if($this->checkMapLockWriteable(0)) {
			if(unlink($this->mapLockPath)) {
				// map lock deleted => OK
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			// no map lock to delete => OK
			return TRUE;   
		}
	}
	
	/**
	 * Checks for existing lockfile
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
     */
	function checkMapLockExists($printErr) {
		return GlobalCore::getInstance()->checkExisting($this->mapLockPath, $printErr);
	}
	
	/**
	 * Checks for readable lockfile
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
     */
	function checkMapLockReadable($printErr) {
		return GlobalCore::getInstance()->checkReadable($this->mapLockPath, $printErr);
	}
	
	/**
	 * Checks for writeable lockfile
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
     */
	function checkMapLockWriteable($printErr) {
		return GlobalCore::getInstance()->checkWriteable($this->mapLockPath, $printErr);
	}
	
	/**
	 * Parses WUI specific settings
	 *
	 * @return  String  JSON Code
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	function parseViewProperties() {
		$arr = Array();
		
		$arr['grid_show'] = intval($this->getValue('global', 0, 'grid_show'));
		$arr['grid_color'] = $this->getValue('global', 0, 'grid_color');
		$arr['grid_steps'] = intval($this->getValue('global', 0, 'grid_steps'));
		
		return json_encode($arr);
	}
}
?>