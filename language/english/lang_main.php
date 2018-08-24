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

if (!defined('EQDKP_INC'))
{
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
		'livestreams'						=> 'Livestreams',
		
		// Description
		'livestreams_short_desc'			=> 'Livestreams',
		'livestreams_long_desc'				=> 'Shows Livestreams of your Users or additional Streamers',
		
		'ls_plugin_not_installed'			=> 'The Plugin "Livestreams" is not installed.',
		'ls_config_saved'					=> 'The settings were successfully saved.',
		'ls_livestreams'					=> 'Livestreams',
		'ls_fs_general'						=> 'General',
		'ls_f_twitch_streams'				=> 'Twitch-Streams',
		'ls_f_help_twitch_streams'			=> 'Insert here additional Twitch-Streamer, one per line',
		'ls_f_show_offline'					=> 'Show offline Streams',
		'ls_f_open_platform'				=> 'Open Streams directly on the plattforms',
		'ls_f_twitch_clientid'				=> 'Twitch App Client-ID',
		'ls_f_help_twitch_clientid'			=> 'Create a new App at https://glass.twitch.tv/console/apps and insert here the Client-ID. As Redirect-URL you can enter http://localhost.'
);

?>