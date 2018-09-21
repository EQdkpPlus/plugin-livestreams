<?php
/*	Project:	EQdkp-Plus
 *	Package:	Livestream Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2018 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found'); exit;
}

/*+----------------------------------------------------------------------------
  | livestreams
  +--------------------------------------------------------------------------*/
class livestreams extends plugin_generic {

	public $version				= '1.0.2';
	public $build				= '';
	public $copyright			= 'GodMod';

	protected static $apiLevel	= 23;

	/**
	* Constructor
	* Initialize all informations for installing/uninstalling plugin
	*/
	public function __construct(){
		parent::__construct();

		$this->add_data(array (
			'name'				=> 'Livestreams',
			'code'				=> 'livestreams',
			'path'				=> 'livestreams',
			'template_path'		=> 'plugins/livestreams/templates/',
			'icon'				=> 'fa-video-camera',
			'version'			=> $this->version,
			'author'			=> $this->copyright,
			'description'		=> $this->user->lang('livestreams_short_desc'),
			'long_description'	=> $this->user->lang('livestreams_long_desc'),
			'homepage'			=> EQDKP_PROJECT_URL,
			'manuallink'		=> false,
			'plus_version'		=> '2.2',
		));

		$this->add_dependency(array(
			'plus_version'      => '2.2'
		));

		$this->add_permission('a', 'settings',		'N', $this->user->lang('menu_settings'),		array(2,3));
		$this->add_permission('u', 'view',		'N', $this->user->lang('menu_settings'),		array(1,2,3));

		//Routing
		$this->routing->addRoute('Livestreams', 'livestreams', 'plugins/livestreams/pageobjects');
		
		$this->add_portal_module('ls_livestreams');
		
		// -- Menu --------------------------------------------
		$this->add_menu('admin', $this->gen_admin_menu());

		$this->add_menu('main', $this->gen_main_menu());

		$this->tpl->css_file($this->root_path.'plugins/livestreams/templates/base_template/livestreams.css');
	}

	/**
	* pre_install
	* Define Installation
	*/
	public function pre_install(){
		$intTwitchFieldID = register('pdh')->get('user_profilefields', 'field_by_name', array('twitch'));
		
		//Create Twitch Profilefield
		if(!$intTwitchFieldID){
			$arrOptions = array(
					'name' 			=> 'Twitch',
					'lang_var'		=> '',
					'type' 			=> 'link',
					'length'		=> 30,
					'minlength' 	=> 3,
					'validation'	=> '[\w_\.]+',
					'required' 		=> 0,
					'show_on_registration' => 0,
					'enabled'		=> 1,
					'is_contact'	=> 1,
					'contact_url' 	=> 'http://www.twitch.tv/%s',
					'icon_or_image' => 'fa-twitch',
					'bridge_field'	=> null,
			);
			
			register('pdh')->put('user_profilefields', 'insert_field', array($arrOptions, array()));
		}
	}


	/**
	* post_uninstall
	* Define Post Uninstall
	*/
	public function post_uninstall(){
	}

	/**
	* gen_admin_menu
	* Generate the Admin Menu
	*/
	private function gen_admin_menu(){
		$admin_menu = array (array(
			'name'	=> $this->user->lang('livestreams'),
			'icon'	=> 'fa-video-camera',
			1 => array (
					'link'	=> 'plugins/livestreams/admin/settings.php'.$this->SID,
					'text'	=> $this->user->lang('settings'),
					'check'	=> 'a_livestreams_settings',
					'icon'	=> 'fa-wrench'
			),
		));
		return $admin_menu;
	}

	/**
	* gen_admin_menu
	* Generate the Admin Menu
	*/
	private function gen_main_menu(){

		$main_menu = array(
			1 => array (
				'link'		=> $this->routing->build('livestreams', false, false, true, true),
				'text'		=> $this->user->lang('ls_livestreams'),
			),
			
		);
		return $main_menu;
	}
}

?>