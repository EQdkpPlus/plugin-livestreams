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
  'livestreams_long_desc'			=> 'Zeigt den aktuellen Status von Livestreams der Benutzer an',
  
  'ls_plugin_not_installed'			=> 'Das livestreams-Plugin ist nicht installiert.',
  'ls_config_saved'					=> 'Die Einstellungen wurden erfolgreich gespeichert.',
  'ls_livestreams'					=> 'Livestreams',
	'ls_fs_general'					=> 'Allgemeines',
	'ls_fs_twitch'					=> 'Twitch',
	'ls_f_twitch_streams'			=> 'Twitch-Streams',
	'ls_f_help_twitch_streams'		=> 'Trage hier zusätzliche Twitch-Streamuser ein, einer pro Zeile',
	'ls_f_show_offline'				=> 'Zeige offline Streams',
	'ls_f_open_platform'			=> 'Öffne Streams direkt auf der jeweiligen Plattform',
	'ls_f_twitch_clientid'			=> 'Twitch App Client-ID',
	'ls_f_help_twitch_clientid'		=> 'Erstelle eine neue Anwendung auf https://dev.twitch.tv/console/apps und trage die Client-ID deiner Anwendung ein. Als Redirect-URL kannst du http://localhost eintragen.',
	'ls_viewer'						=> 'Zuschauer',
	'ls_fs_mixer'					=> 'Mixer',
	'ls_f_mixer_streams'			=> 'Mixer-Streams',
	'ls_f_help_mixer_streams'		=> 'Trage hier zusätzliche Mixer-Streamuser ein, einer pro Zeile',
 );

?>