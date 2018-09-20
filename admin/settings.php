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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'livestreams');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path.'common.php');


/*+----------------------------------------------------------------------------
  | livestreamsSettings
  +--------------------------------------------------------------------------*/
class livestreamsSettings extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'config', 'core', 'in', 'jquery', 'html', 'tpl');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('livestreams', PLUGIN_INSTALLED))
      message_die($this->user->lang('ls_plugin_not_installed'));

    $handler = array(
      'save' => array('process' => 'save', 'csrf' => true),
    );
	
	$this->user->check_auth('a_livestreams_settings');  
	
    parent::__construct(null, $handler);

    $this->process();
  }
  
  private $arrData = false;

  /**
   * save
   * Save the configuration
   */
  public function save()
  {

  	$objForm = register('form', array('ls_settings'));
  	$objForm->langPrefix = 'ls_';
  	$objForm->validate = true;
  	$objForm->add_fieldsets($this->fields());
  	$arrValues = $objForm->return_values();

  	if ($objForm->error){
  		$this->arrData = $arrValues;
  	} else {  
  		$messages = array();
	  	// update configuration
	    $this->config->set($arrValues, '', 'livestreams');
	    // Success message
	    $messages[] = $this->user->lang('ls_config_saved');
	    
	    $this->pdc->del_prefix('plugin.livestream.accounts.');
	
	    $this->display($messages);
  	}
   
  }
  
  
  private function fields(){
  	$arrFields = array(
  		'general' => array(
			'show_offline' => array(
  				'type'		=> 'radio',
  			),   
  			'open_platform' => array(
  				'type'		=> 'radio',
  			), 
  		),
  		'twitch' => array(
  				'twitch_streams' => array(
  						'type'		=> 'textarea',
  						'cols'		=> 80,
  				),
  				'twitch_clientid' => array(
  						'type'		=> 'text',
  				),
  		),

  	);

  	
  	return $arrFields;
  }
  

  /**
   * display
   * Display the page
   *
   * @param    array  $messages   Array of Messages to output
   */
  public function display($messages=array())
  {
    // -- Messages ------------------------------------------------------------
    if ($messages)
    {
      foreach($messages as $name)
        $this->core->message($name, $this->user->lang('livestreams'), 'green');
    }
    
    $arrValues = $this->config->get_config('livestreams');
    if ($this->arrData !== false) $arrValues = $this->arrData;

    // -- Template ------------------------------------------------------------
	// initialize form class
	$objForm = register('form', array('ls_settings'));
	$objForm->reset_fields();
  	$objForm->lang_prefix = 'ls_';
  	$objForm->validate = true;
  	$objForm->use_fieldsets = true;
  	$objForm->use_dependency = true;
  	$objForm->add_fieldsets($this->fields());
		
	// Output the form, pass values in
	$objForm->output($arrValues);
	
    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array(
      'page_title'    => $this->user->lang('ls_livestreams').' - '.$this->user->lang('settings'),
      'template_path' => $this->pm->get_data('livestreams', 'template_path'),
      'template_file' => 'admin/settings.html',
    		'page_path'			=> [
    				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
    				['title'=>$this->user->lang('livestreams').': '.$this->user->lang('settings'), 'url'=>' '],
    		],
      'display'       => true
    ));
  }
  
}

registry::register('livestreamsSettings');

?>