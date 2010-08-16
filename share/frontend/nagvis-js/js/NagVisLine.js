/*****************************************************************************
 *
 * NagVisLine.js - This class handles the visualisation of stateless line
 *                 objects in the NagVis js frontend
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


var NagVisLine = NagVisStatelessObject.extend({
	constructor: function(oConf) {
		// Call parent constructor;
		this.base(oConf);
	},
	
	/**
	 * PUBLIC parse()
	 *
	 * Parses the object
	 *
	 * @return	String		HTML code of the object
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	parse: function () {
		var oContainerDiv;
		
		// Create container div
		oContainerDiv = document.createElement('div');
		oContainerDiv.setAttribute('id', this.conf.object_id);
		
		var oLine = this.parseLine();
		oContainerDiv.appendChild(oLine);
		oShape = null;
		
		// Append child to map and save reference in parsedObject
		var oMap = document.getElementById('map');
		this.parsedObject = oMap.appendChild(oContainerDiv);
		oContainerDiv = null;
		oMap = null;
		
		this.drawLine();
	},
	
	/**
	 * Parses the HTML-Code of a line
	 *
	 * @return	String		HTML code
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	parseLine: function () {
		var ret = '';
		var link = '';
		
		// Create container div
		var oContainerDiv = document.createElement('div');
		oContainerDiv.setAttribute('id', this.conf.object_id+'-linediv');
		
		// Create line div
		var oLineDiv = document.createElement('div');
		oLineDiv.setAttribute('id', this.conf.object_id+'-line');
		oLineDiv.style.zIndex = this.conf.z;
		
		oContainerDiv.appendChild(oLineDiv);
		oLineDiv = null;
		
		// Parse hover/link area only when needed
		if((this.conf.url && this.conf.url !== '') || (this.conf.hover_menu && this.conf.hover_menu !== '')) {
			var oLinkDiv = document.createElement('div');
			
			oLinkDiv.setAttribute('id', this.conf.object_id+'-linelinkdiv');
			oLinkDiv.style.zIndex = (this.conf.z+1);
			var sUrl = this.conf.url;
			var sUrlTarget = this.conf.url_target;
			oLinkDiv.onclick = function() { window.open(sUrl, sUrlTarget, ""); };
			
			oContainerDiv.appendChild(oLinkDiv);
			oLinkDiv = null;
		}
		
		return oContainerDiv;
	},
	
	/**
	 * Draws the NagVis lines on the already added divs.
	 *
	 * @return	String		HTML code
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	drawLine: function() {
		var x = this.conf.x.split(',');
		var y = this.conf.y.split(',');
		
		var width = this.conf.line_width;
		
		var colorFill = this.conf.line_color;
		var colorBorder = this.conf.line_color_border;
		
		// Parse the line object
		drawNagVisLine(this.conf.object_id, this.conf.line_type, x[0], y[0], x[1], y[1], this.conf.z, width, colorFill, colorBorder, ((this.conf.url && this.conf.url !== '') || (this.conf.hover_menu && this.conf.hover_menu !== '')));
	},
	
	parseHoverMenu: function () {
		this.getHoverMenu(this.conf.object_id+'-icon');
	}
});