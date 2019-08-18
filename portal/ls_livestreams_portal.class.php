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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class ls_livestreams_portal extends portal_generic {

	protected static $path		= 'livestreams';
	protected static $data		= array(
		'name'			=> 'Livestreams Module',
		'version'		=> '0.2.0',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'icon'			=> 'fa-video-camera',
		'description'	=> 'Shows status of the users\' livestreams',
		'lang_prefix'	=> 'ls_',
		'multiple'		=> true,
	);
	
	
	protected $settings	= array(
		'show_offline' => array(
				'type'		=> 'radio',
		),
	);

	
	protected static $apiLevel = 20;
	
	public function get_settings($state){
		
		return $this->settings;
	}
	

	public function output() {
		include_once($this->root_path.'plugins/livestreams/includes/livestream_helper.class.php');
		$objHelper = register('livestream_helper', array());
		
		$arrAccounts = $objHelper->getStreamAccounts();
		$arrLiveStreamData = $objHelper->queryData($arrAccounts);
		
		$blnShowOffline = $this->config('show_offline');
		
		$intStreamCount = 0;
		
		$myOut = '<div class="table" style="width:100%">';
		
		foreach($arrLiveStreamData as $arrStreamData){
			if(!$blnShowOffline && !$arrStreamData['stream_live']) continue;

			if($this->config->get('open_platform', 'livestreams')){
				$link = '<a href="'.sanitize($arrStreamData['stream_link']).'">';
			} else {
				$link = '<a href="'.$this->routing->build('livestreams').'&stream='.sanitize($arrStreamData['stream_username']).'&type='.sanitize($arrStreamData['stream_type']).'&videoid='.sanitize($arrStreamData['stream_videoid']).'">';
			}
			
			$myOut .= '<div class="tr">';
			
			$myOut .= '<div class="td" style="width: 28px;">'.$link.'<div class="user-avatar-small user-avatar-border">';
			if($arrStreamData['stream_avatar']) $myOut .='<img src="'.sanitize($arrStreamData['stream_avatar']).'" class="user-avatar small"/>';
			
			$myOut .= '</div></a>';
			$myOut .= '</div>';
			
			$myOut .= '<div class="td"><div>';
			
			$myOut .= '<div class="floatRight">'.(($arrStreamData['stream_live']) ? '<i class="eqdkp-icon-online" style="background-color:red;"></i> LIVE' : '').'</div>';
			
			$displayName = isset($arrStreamData['stream_username_display']) ? $arrStreamData['stream_username_display'] : $arrStreamData['stream_username'];
			
			$myOut .= $link.sanitize($displayName).'</a>';
			if($arrStreamData['stream_live']){
				$myOut .= '<br/><span class="small" style="font-style:italic;">'.sanitize($arrStreamData['stream_game']).'</span>';
				$myOut .= '<br/><span class="small">'.sanitize($arrStreamData['stream_viewer']).' '.$this->user->lang('ls_viewer').'</span>';
			}
			
	
			$myOut .= '</div></div>';
			
			$myOut .= '</div>';

		}
		
		$myOut .= "</div>";
		return $myOut;
	}
}
?>