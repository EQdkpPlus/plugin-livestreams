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

class livestream_helper extends gen_class {
	
	private $strTwitchClientID = "xz3zsp1i87vb6l4uw8fj40rvhzcvvm";
	private $intCacheMinutes = 5;
	
	public function __construct($strTwitchClientID){
		if($strTwitchClientID && $strTwitchClientID != "") {
			$this->strTwitchClientID = $strTwitchClientID;
			$this->intCacheMinutes = 1;
		}
	}
	
	public function getStreamAccounts(){
		$arrUserIDs = $this->pdh->sort($this->pdh->get('user', 'id_list'), 'user', 'name', 'asc');
		$arrAccounts = array();
		
		foreach($arrUserIDs as $intUserID){
			
			$strTwitch = $this->pdh->get('user', 'profilefield_by_name', array($intUserID, 'twitch', false, true));
			
			if($strTwitch && $strTwitch != ""){
				
				$strUsername = $this->pdh->get('user', 'name', array($intUserID));
				
				$arrAccounts[] = array(
						'username' 		=> $strUsername,
						'userlink' 		=> $this->routing->build('user', $strUsername, 'u'.$intUserID),
						'stream_type'		=> 'twitch',
						'stream_icon'		=> '<i class="fa fa-twitch" title="Twitch"></i>',
						'stream_link'		=> 'https://www.twitch.tv/'.str_replace('https://www.twitch.tv/', '', sanitize(utf8_strtolower($strTwitch))),
						'stream_username' 	=> str_replace('https://www.twitch.tv/', '', utf8_strtolower($strTwitch)),
						'stream_username_display' => str_replace('https://www.twitch.tv/', '', utf8_strtolower($strTwitch)),
				);
			}
		}
		
		$strAdditionalAccounts = $this->config->get('twitch_streams', 'livestreams');
		$arrParts = explode("\r\n", $strAdditionalAccounts);
		
		foreach($arrParts as $strTwitch){
			$arrAccounts[] = array(
					'username' 		=> '',
					'userlink' 		=> '',
					'stream_type'		=> 'twitch',
					'stream_icon'		=> '<i class="fa fa-twitch" title="Twitch"></i>',
					'stream_link'		=> 'https://www.twitch.tv/'.str_replace('https://www.twitch.tv/', '', sanitize(utf8_strtolower($strTwitch))),
					'stream_username' 	=> str_replace('https://www.twitch.tv/', '', utf8_strtolower($strTwitch)),
			);
		}
		
		return $arrAccounts;
	}
	
	public function queryTwitch($arrAccounts){		
		$arrCacheAccounts = $this->pdc->get('plugin.livestream.accounts.'.$this->user->id);
		
		if($arrCacheAccounts !== null){
			return $arrCacheAccounts;
		}
		
		$arrTwitchUsers = array();
		foreach($arrAccounts as $arrData){
			if($arrData['stream_type'] != 'twitch') continue;
			
			$arrTwitchUsers[] = $arrData['stream_username'];
		}
		
		
		$arrTwitchUsers = array_unique($arrTwitchUsers);
		
		$arrReturnData = array();
		
		//Query User Information
		$mixRequest = register('urlfetcher')->fetch('https://api.twitch.tv/helix/users?login='.implode('&login=', $arrTwitchUsers), array('Client-ID: '.$this->strTwitchClientID));
		
		$arrUserIDToLogin = array();
		
		if ($mixRequest){
			$arrResponseData = json_decode($mixRequest, true);
			foreach($arrResponseData['data'] as $arrUserData){
				$arrReturnData[$arrUserData['login']] = $arrUserData;
				$arrUserIDToLogin[$arrUserData['id']] = $arrUserData['login'];
			}
		}
		
		//Query Stream Information
		$arrGames = array();
		$mixRequest = register('urlfetcher')->fetch('https://api.twitch.tv/helix/streams?user_login='.implode('&user_login=', $arrTwitchUsers), array('Client-ID: '.$this->strTwitchClientID));
		if ($mixRequest){
			$arrResponseData = json_decode($mixRequest, true);
			foreach($arrResponseData['data'] as $arrStreamData){
				$strLoginName = $arrUserIDToLogin[$arrStreamData['user_id']];
				
				$arrReturnData[$strLoginName]['stream_data'] = $arrStreamData;
				$arrGames[$arrStreamData['game_id']] = $arrStreamData['game_id'];
			}
		}
		
		//Query Games Information
		$mixRequest = register('urlfetcher')->fetch('https://api.twitch.tv/helix/games?id='.implode('&id=', $arrGames), array('Client-ID: '.$this->strTwitchClientID));
		$arrGameIDs = array();
		if ($mixRequest){
			$arrResponseData = json_decode($mixRequest, true);
			
			foreach($arrResponseData['data'] as $arrGameData){
				$arrGameIDs[$arrGameData['id']] = $arrGameData;
				
			}
		}
		
		
		//Now combine the results
		
		foreach($arrReturnData as $strLoginName => $arrUserdata){
			if(isset($arrUserdata['stream_data']['game_id'])){
				$arrReturnData[$strLoginName]['stream_data']['game_infos'] = $arrGameIDs[$arrUserdata['stream_data']['game_id']];
				$arrReturnData[$strLoginName]['live'] = 1;
			} else {
				$arrReturnData[$strLoginName]['live'] = 0;
			}
			
		}
		
		$arrSortOnline = $arrSortUsername = array();
		
		foreach($arrAccounts as $key => $val){
			if($arrData['stream_type'] != 'twitch') continue;
			$arrAccounts[$key]['stream_data'] = $arrReturnData[$val['stream_username']];
			$arrAccounts[$key]['stream_username_display'] = $arrAccounts[$key]['stream_data']['display_name'];
			$arrAccounts[$key]['stream_live'] = $arrAccounts[$key]['stream_data']['live'];
			$arrAccounts[$key]['stream_game'] = ($arrAccounts[$key]['stream_live']) ? $arrAccounts[$key]['stream_data']['stream_data']['game_infos']['name'] : '';
			$arrAccounts[$key]['stream_start'] = ($arrAccounts[$key]['stream_live']) ? strtotime($arrAccounts[$key]['stream_data']['stream_data']['started_at']) : 0;
			$arrAccounts[$key]['stream_viewer'] = ($arrAccounts[$key]['stream_live']) ? $arrAccounts[$key]['stream_data']['stream_data']['viewer_count'] : 0;
			$arrAccounts[$key]['stream_background'] = ($arrAccounts[$key]['stream_live']) ? str_replace(array('{width}', '{height}'), array(360, 200), $arrAccounts[$key]['stream_data']['stream_data']['thumbnail_url']) : $arrAccounts[$key]['stream_data']['offline_image_url'];
			$arrAccounts[$key]['stream_avatar'] = $arrAccounts[$key]['stream_data']['profile_image_url'];
			$arrSortOnline[] = $arrAccounts[$key]['stream_live'];
			$arrSortUsername[] = $arrAccounts[$key]['stream_username'];
		}
		
		array_multisort($arrSortOnline, SORT_DESC, SORT_NUMERIC, $arrSortUsername, SORT_ASC, SORT_REGULAR, $arrAccounts);
		
		$this->pdc->put('plugin.livestream.accounts.'.$this->user->id,$arrAccounts,(60*$this->intCacheMinutes));
		
		//Sort by Online, And Displayname
		
		return $arrAccounts;
		
	}
	
	
}