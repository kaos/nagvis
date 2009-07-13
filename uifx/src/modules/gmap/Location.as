/*****************************************************************************
 *
 * Copyright (C) 2009 NagVis Project
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

package modules.gmap
{
	[Bindable]
	[RemoteClass(alias="Location")]
	[Event(name="change", type="modules.gmap.LocationEvent")]
	public class Location
	{
		private var _id : String;
		private var _point : String;
		private var _label : String;
		private var _address : String;
		private var _description : String;
		private var _hosts : Array;

		public function get id() : String
		{
			return this._id;
		}

		public function set id(value : String) : void
		{
			if(_id != value)
			{
				this._id = value;
				dispatchEvent(new LocationEvent('change', this));
			}
		}

		public function get point() : String
		{
			return this._point;
		}

		public function set point(value : String) : void
		{
			if(_point != value)
			{
				this._point = value;
				dispatchEvent(new LocationEvent('change', this));
			}
		}

		public function get label() : String
		{
			return this._label;
		}

		public function set label(value : String) : void
		{
			if(_label != value)
			{
				this._label = value;
				dispatchEvent(new LocationEvent('change', this));
			}
		}

		public function get address() : String
		{
			return this._address;
		}

		public function set address(value : String) : void
		{
			if(_address != value)
			{
				this._address = value;
				dispatchEvent(new LocationEvent('change', this));
			}
		}

		public function get description() : String
		{
			return this._description;
		}

		public function set description(value : String) : void
		{
			if ( _description != value)
			{
				this._description = value;
				dispatchEvent(new LocationEvent('change', this));
			}
		}

		public function get hosts() : Array
		{
			return this._hosts;
		}

		public function set hosts(value : Array) : void
		{
			if ( _hosts != value)
			{
				this._hosts = value;
				dispatchEvent(new LocationEvent('change', this));
			}
		}

		public function update(value : Location) : void
		{
			this.id = value.id;
			this.point = value.point;
			this.label = value.label;
			this.address = value.address;
			this.description = value.description;			
		}
	}
}
