<?php
/*****************************************************************************
 *
 * GlobalMap.php - Class for parsing the NagVis maps
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
 * FIXME: Maybe to be removed in future cleanups
 */
class GlobalMap {
	protected $CORE;
	protected $MAPCFG;
	
	private $linkedMaps = Array();
	
	/**
	 * Class Constructor
	 *
	 * @param 	GlobalCore 	$CORE
	 * @param 	GlobalMapCfg 	$MAPCFG
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct($CORE,$MAPCFG) {
		$this->CORE = $CORE;
		$this->MAPCFG = $MAPCFG;
	}
}
?>