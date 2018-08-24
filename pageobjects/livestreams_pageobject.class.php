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

class livestreams_pageobject extends pageobject {
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('social' => 'socialplugins');
   	return array_merge(parent::__shortcuts(), $shortcuts);
  }
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('livestreams', PLUGIN_INSTALLED))
      message_die($this->user->lang('ls_plugin_not_installed'));
    
    $this->user->check_auth('u_livestreams_view');
    
    $handler = array();
    parent::__construct(false, $handler, array());
    
    $this->process();
  }
  
  
  public function display(){
		include_once($this->root_path.'plugins/livestreams/includes/livestream_helper.class.php');
		$objHelper = register('livestream_helper', array($this->config->get('twitch_clientid', 'livestreams')));
  	
		$arrAccounts = $objHelper->getStreamAccounts();
		$arrLiveStreamData = $objHelper->queryTwitch($arrAccounts);
		
		$blnShowOffline = $this->config->get('show_offline', 'livestreams');

		$intStreamCount = 0;
		
		foreach($arrLiveStreamData as $arrStreamData){
			if(!$blnShowOffline && !$arrStreamData['stream_live']) continue;
			
			$this->tpl->assign_block_vars('stream_row', array(
				'S_IS_LIVE' 	=> ($arrStreamData['stream_live']) ? true : false,
				'ONLINE_CLASS'	=> ($arrStreamData['stream_live']) ? 'online' : 'offline',
				'STREAM_NAME' 	=> (isset($arrStreamData['stream_username_display'])) ? sanitize ($arrStreamData['stream_username_display']) : sanitize($arrStreamData['stream_username']),
				'STREAM_TYPE' 	=> sanitize($arrStreamData['stream_type']),
				'STREAM_ICON' 	=> $arrStreamData['stream_icon'],
				'STREAM_LINK' 	=> sanitize($arrStreamData['stream_link']),
				'STREAM_ID'	=> sanitize($arrStreamData['stream_username']),
				'USERNAME'	=> $arrStreamData['username'],
				'USERLINK'	=> $arrStreamData['userlink'],
				'STREAM_GAME' 	=> sanitize($arrStreamData['stream_game']),
				'STREAM_START' 	=> $this->time->nice_date($arrStreamData['stream_start']),
				'STREAM_VIEWS' 	=> sanitize($arrStreamData['stream_viewer']),
				'BACKGROUND' 	=> sanitize($arrStreamData['stream_background']),
				'STREAM_AVATAR' => sanitize($arrStreamData['stream_avatar']),
  			));
			$intStreamCount = $intStreamCount+1;
		}
  		
  		$this->tpl->assign_vars(array(
  			'LS_STREAM_COUNT' => $intStreamCount,
  			'S_SHOW_STREAM' => ($this->in->get('stream') != ""),
  			'SHOW_STREAMNAME' => sanitize($this->in->get('stream')),
  			'S_OPEN_PLATFORM' => $this->config->get('open_platform', 'livestreams'),
  		));

  		
  		// -- EQDKP ---------------------------------------------------------------
	  	$this->core->set_vars(array (
	  			'page_title'    => $this->user->lang('ls_livestreams'),
	  			'template_path' => $this->pm->get_data('livestreams', 'template_path'),
	  			'template_file' => 'livestreams.html',
	  			'page_path'		=> array(
	  					array('url' => ' ', 'title' => $this->user->lang('ls_livestreams'))	
	  			),
	  			'display'       => true
	  	));

  }
  
 
}
?>
