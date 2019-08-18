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
	private $strYoutubeClientID = "";
	private $intTwitchCacheMinutes = 5;
	private $intYoutubeCacheMinutes = 5;
	
	public function __construct(){		
		$strTwitchClientID = $this->config->get('twitch_clientid', 'livestreams');
		if($strTwitchClientID && $strTwitchClientID != "") {
			$this->strTwitchClientID = $strTwitchClientID;
			$this->intTwitchCacheMinutes = 1;
		}
		
		$strYoutubeClientID = $this->config->get('youtube_clientid', 'livestreams');
		if($strYoutubeClientID && $strYoutubeClientID != "") {
			$this->strYoutubeClientID = $strYoutubeClientID;
		}
		
		
	}
	
	public function getStreamAccounts(){
		$arrUserIDs = $this->pdh->sort($this->pdh->get('user', 'id_list'), 'user', 'name', 'asc');
		$arrAccounts = array();
		
		foreach($arrUserIDs as $intUserID){
			
			$strTwitch = trim($this->pdh->get('user', 'profilefield_by_name', array($intUserID, 'twitch', false, true)));
			
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
			
			$strMixer = trim($this->pdh->get('user', 'profilefield_by_name', array($intUserID, 'mixer', false, true)));
			
			if($strMixer && $strMixer != ""){
				
				$strUsername = $this->pdh->get('user', 'name', array($intUserID));
				
				$arrAccounts[] = array(
						'username' 			=> $strUsername,
						'userlink' 			=> $this->routing->build('user', $strUsername, 'u'.$intUserID),
						'stream_type'		=> 'mixer',
						'stream_icon'		=> '<img src="'.$this->server_path.'plugins/livestreams/images/mixer.png" title="Mixer" style="height: 20px;" />',
						'stream_link'		=> 'https://mixer.com/'.str_replace('https://mixer.com/', '', sanitize(utf8_strtolower($strMixer))),
						'stream_username' 	=> str_replace('https://mixer.com/', '', utf8_strtolower($strMixer)),
						'stream_username_display' => str_replace('https://mixer.com/', '', utf8_strtolower($strMixer)),
				);
			}
			
			$strYoutube = trim($this->pdh->get('user', 'profilefield_by_name', array($intUserID, 'youtube', false, true)));
			
			if($strYoutube && $strYoutube != ""){
				
				$strUsername = $this->pdh->get('user', 'name', array($intUserID));
				
				if(strpos($strYoutube, 'UC') === 0){
					$strYoutubeLink = "https://www.youtube.com/channel/".$strYoutube;
				} else {
					$strYoutubeLink = "https://www.youtube.com/user/".$strYoutube;
				}
				
				$arrAccounts[] = array(
						'username' 					=> $strUsername,
						'userlink' 					=> $this->routing->build('user', $strUsername, 'u'.$intUserID),
						'stream_type'				=> 'youtube',
						'stream_icon'				=> '<i class="fa fa-youtube" title="YouTube"></i>',
						'stream_link'				=> $strYoutubeLink,
						'stream_username' 			=> $strYoutube,
						'stream_username_display'	=> $strYoutube,
				);
			}
		}
		
		$strAdditionalAccounts = $this->config->get('twitch_streams', 'livestreams');
		if(strlen($strAdditionalAccounts)){
			$arrParts = explode("\r\n", $strAdditionalAccounts);
		} else {
			$arrParts = array();
		}
		
		foreach($arrParts as $strTwitch){
			$strTwitch = trim($strTwitch);
			if($strTwitch == "") continue;
			
			$arrAccounts[] = array(
					'username' 		=> '',
					'userlink' 		=> '',
					'stream_type'		=> 'twitch',
					'stream_icon'		=> '<i class="fa fa-twitch" title="Twitch"></i>',
					'stream_link'		=> 'https://www.twitch.tv/'.str_replace('https://www.twitch.tv/', '', sanitize(utf8_strtolower($strTwitch))),
					'stream_username' 	=> str_replace('https://www.twitch.tv/', '', utf8_strtolower($strTwitch)),
			);
		}
		
		$strAdditionalAccounts = $this->config->get('mixer_streams', 'livestreams');
		if(strlen($strAdditionalAccounts)){
			$arrParts = explode("\r\n", $strAdditionalAccounts);
		} else {
			$arrParts = array();
		}
		
		foreach($arrParts as $strMixer){
			$strMixer = trim($strMixer);
			if($strMixer == "") continue;
			
			$arrAccounts[] = array(
					'username' 		=> '',
					'userlink' 		=> '',
					'stream_type'		=> 'mixer',
					'stream_icon'		=> '<img src="'.$this->server_path.'plugins/livestreams/images/mixer.png" title="Mixer" style="height: 20px;" />',
					'stream_link'		=> 'https://mixer.com/'.str_replace('https://mixer.com/', '', sanitize(utf8_strtolower($strMixer))),
					'stream_username' 	=> str_replace('https://mixer.com/', '', utf8_strtolower($strMixer)),
			);
		}
		
		$strAdditionalAccounts = $this->config->get('youtube_streams', 'livestreams');
		if(strlen($strAdditionalAccounts)){
			$arrParts = explode("\r\n", $strAdditionalAccounts);
		} else {
			$arrParts = array();
		}
		
		foreach($arrParts as $strYoutube){
			$strYoutube = trim($strYoutube);
			if($strYoutube == "") continue;
			
			if(strpos($strYoutube, 'UC') === 0){
				$strYoutubeLink = "https://www.youtube.com/channel/".$strYoutube;
			} else {
				$strYoutubeLink = "https://www.youtube.com/user/".$strYoutube;
			}
			
			$arrAccounts[] = array(
					'username' 		=> '',
					'userlink' 		=> '',
					'stream_type'		=> 'youtube',
					'stream_icon'		=> '<i class="fa fa-youtube" title="YouTube"></i>',
					'stream_link'		=> $strYoutubeLink,
					'stream_username' 	=> $strYoutube,
			);
		}
		
		return $arrAccounts;
	}
	
	public function queryData($arrAccounts){
		$arrCacheAccounts = $this->pdc->get('plugin.livestream.accounts.'.$this->user->id);
		if($arrCacheAccounts !== null){
			return $arrCacheAccounts;
		}
		
		$arrAccounts = $this->queryTwitch($arrAccounts);
		$arrAccounts = $this->queryMixer($arrAccounts);
		$arrAccounts = $this->queryYoutube($arrAccounts);
		
		$arrSortOnline = $arrSortUsername = array();
		foreach($arrAccounts as $key => $val){
			$arrSortOnline[] = $arrAccounts[$key]['stream_live'];
			$arrSortUsername[] = $arrAccounts[$key]['stream_username'];
		}
		
		array_multisort($arrSortOnline, SORT_DESC, SORT_NUMERIC, $arrSortUsername, SORT_ASC, SORT_REGULAR, $arrAccounts);
		
		
		$this->pdc->put('plugin.livestream.accounts.'.$this->user->id,$arrAccounts,(60*$this->intTwitchCacheMinutes));
		
		return $arrAccounts;
	}
	
	
	public function queryMixer($arrAccounts){
		$arrMixerUsers = array();
		foreach($arrAccounts as $arrData){
			if($arrData['stream_type'] != 'mixer') continue;
			
			$arrMixerUsers[] = $arrData['stream_username'];
		}
		
		$arrMixerUsers = array_unique($arrMixerUsers);
		
		$arrReturnData = array();
		
		if(count($arrMixerUsers)){
			$arrQueryData = array();
			foreach($arrMixerUsers as $strMixername){
				$mixRequest = register('urlfetcher')->fetch('https://mixer.com/api/v1/channels/'.$strMixername);
				if($mixRequest){
					$arrQueryData[$strMixername] = json_decode($mixRequest, true);
				}
			}
		}
		
		
		//Now combine the results
		
		foreach($arrAccounts as $key => $val){
			if($val['stream_type'] != 'mixer') continue;
			#$arrAccounts[$key]['stream_data'] = $arrQueryData[$val['stream_username']];
			$arrAccounts[$key]['stream_username_display'] = $arrQueryData[$val['stream_username']]['token'];
			$arrAccounts[$key]['stream_live'] = ($arrQueryData[$val['stream_username']]['online']) ? 1 : 0;
			$arrAccounts[$key]['stream_game'] = ($arrQueryData[$val['stream_username']]['type']['name']) ? $arrQueryData[$val['stream_username']]['type']['name'] : '';
			$arrAccounts[$key]['stream_viewer'] = ($arrQueryData[$val['stream_username']]['viewersCurrent']) ? $arrQueryData[$val['stream_username']]['viewersCurrent'] : 0;
			$arrAccounts[$key]['stream_avatar'] = $arrQueryData[$val['stream_username']]['user']['avatarUrl'];
			$arrAccounts[$key]['stream_background'] = ($arrAccounts[$key]['stream_live']) ? 'https://thumbs.mixer.com/channel/'.$arrQueryData[$val['stream_username']]['id'].'.small.jpg' : $arrQueryData[$val['stream_username']]['bannerUrl'];
		}
		
		//Sort by Online, And Displayname
		
		return $arrAccounts;
	}
	
	public function queryTwitch($arrAccounts){		
		$arrTwitchUsers = array();
		foreach($arrAccounts as $arrData){
			if($arrData['stream_type'] != 'twitch') continue;
			
			$arrTwitchUsers[] = $arrData['stream_username'];
		}
		
		$arrTwitchUsers = array_unique($arrTwitchUsers);
		
		$arrReturnData = array();
		
		if(count($arrTwitchUsers)){
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

		foreach($arrAccounts as $key => $val){
			if($val['stream_type'] != 'twitch') continue;
			$arrAccounts[$key]['stream_data'] = $arrReturnData[$val['stream_username']];
			$arrAccounts[$key]['stream_username_display'] = $arrAccounts[$key]['stream_data']['display_name'];
			$arrAccounts[$key]['stream_live'] = $arrAccounts[$key]['stream_data']['live'];
			$arrAccounts[$key]['stream_game'] = ($arrAccounts[$key]['stream_live']) ? $arrAccounts[$key]['stream_data']['stream_data']['game_infos']['name'] : '';
			$arrAccounts[$key]['stream_start'] = ($arrAccounts[$key]['stream_live']) ? strtotime($arrAccounts[$key]['stream_data']['stream_data']['started_at']) : 0;
			$arrAccounts[$key]['stream_viewer'] = ($arrAccounts[$key]['stream_live']) ? $arrAccounts[$key]['stream_data']['stream_data']['viewer_count'] : 0;
			$arrAccounts[$key]['stream_background'] = ($arrAccounts[$key]['stream_live']) ? str_replace(array('{width}', '{height}'), array(360, 200), $arrAccounts[$key]['stream_data']['stream_data']['thumbnail_url']) : $arrAccounts[$key]['stream_data']['offline_image_url'];
			$arrAccounts[$key]['stream_avatar'] = $arrAccounts[$key]['stream_data']['profile_image_url'];
		}
				
		//Sort by Online, And Displayname
		
		return $arrAccounts;
		
	}
	
	function queryYoutube($arrAccounts){		
		$arrYoutubeUsers = array();
		foreach($arrAccounts as $arrData){
			if($arrData['stream_type'] != 'youtube') continue;
			
			$arrYoutubeUsers[] = $arrData['stream_username'];
		}
		
		$arrYoutubeUsers = array_unique($arrYoutubeUsers);
		
		$arrReturnData = array();
		
		if(count($arrYoutubeUsers)){
			$arrChannelData = $arrLiveData = array();
			foreach($arrYoutubeUsers as $strYoutube){
				
				if(strpos($strYoutube, 'UC') === 0){
					$mixRequest = register('urlfetcher')->fetch('https://www.googleapis.com/youtube/v3/search?order=date&maxResults=1&type=video&eventType=live&safeSearch=none&videoEmbeddable=true&channelId='.$strYoutube.'&part=snippet&key='.$this->strYoutubeClientID);
					if($mixRequest){
						$arrLiveData[$strYoutube] = json_decode($mixRequest, true);
						
						if(count($arrLiveData[$strYoutube]['items']) == 0) {
							//Get some channel infos
							$mixRequest = register('urlfetcher')->fetch('https://www.googleapis.com/youtube/v3/channels?key='.$this->strYoutubeClientID.'&id='.$strYoutube.'&part=snippet');
							if($mixRequest){
								$arrChannelData[$strYoutube] = json_decode($mixRequest, true);
							}
							
						}
					}					
				} else {
					$mixRequest = register('urlfetcher')->fetch('https://www.googleapis.com/youtube/v3/channels?key='.$this->strYoutubeClientID.'&forUsername='.$strYoutube.'&part=snippet');
					if($mixRequest){
						$arrChannelData[$strYoutube] = json_decode($mixRequest, true);
						$strChannelID = $arrChannelData[$strYoutube]['items'][0]['id'];
						if(!$strChannelID) continue;
						
						$mixRequest = register('urlfetcher')->fetch('https://www.googleapis.com/youtube/v3/search?order=date&maxResults=1&type=video&eventType=live&safeSearch=none&videoEmbeddable=true&channelId='.$strChannelID.'&part=snippet&key='.$this->strYoutubeClientID);
						if($mixRequest){
							$arrLiveData[$strYoutube] = json_decode($mixRequest, true);
						}		
					}
					
				}

			}
		}
		
		//Now combine the results
			
		foreach($arrAccounts as $key => $val){
			if($val['stream_type'] != 'youtube') continue;
			
			if(isset($arrLiveData[$val['stream_username']]) && count($arrLiveData[$val['stream_username']]['items']) > 0){
				$arrTmpData = $arrLiveData[$val['stream_username']]['items'][0];
				$intViewers = 0;
				
				if(isset($arrTmpData['id']['videoId'])){
					$mixRequest = register('urlfetcher')->fetch('https://www.googleapis.com/youtube/v3/videos?part=liveStreamingDetails&id='.$arrTmpData['id']['videoId'].'&fields=items%2FliveStreamingDetails%2FconcurrentViewers&key='.$this->strYoutubeClientID);
					if($mixRequest){
						$arrResult = json_decode($mixRequest, true);
						$intViewers = $arrResult['items'][0]['liveStreamingDetails']['concurrentViewers'];
					}
				}
				
				$arrAccounts[$key]['stream_username_display'] = $arrTmpData['snippet']['channelTitle'];
				$arrAccounts[$key]['stream_live'] = 1;
				$arrAccounts[$key]['stream_game'] = $arrTmpData['snippet']['title'];
				$arrAccounts[$key]['stream_viewer'] = $intViewers;
				$arrAccounts[$key]['stream_avatar'] = $arrChannelData[$val['stream_username']]['items'][0]['snippet']['thumbnails']['default']['url'];
				$arrAccounts[$key]['stream_background'] = $arrTmpData['snippet']['thumbnails']['high']['url'];
				$arrAccounts[$key]['stream_videoid'] = $arrTmpData['snippet']['channelId'];
				
			} else {
				$arrTmpData = $arrChannelData[$val['stream_username']]['items'][0];
				
				$arrAccounts[$key]['stream_username_display'] = $arrTmpData['snippet']['title'];
				$arrAccounts[$key]['stream_live'] = 0;
				$arrAccounts[$key]['stream_game'] = '';
				$arrAccounts[$key]['stream_viewer'] = 0;
				$arrAccounts[$key]['stream_avatar'] = $arrTmpData['snippet']['thumbnails']['default']['url'];
				$arrAccounts[$key]['stream_background'] = $arrTmpData['snippet']['thumbnails']['high']['url'];
			}
		}
		
		//Sort by Online, And Displayname
		
		return $arrAccounts;
	}
	
}