<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2010
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @author     Kamil Kuzminski <kamil.kuzminski@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


/**
 * Operations
 */
$GLOBALS['TL_DCA']['tl_form']['list']['operations']['mails'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_form']['mails'],
	'href'					=> 'table=tl_form_mails',
	'icon'					=> 'system/modules/formmails/html/mails.png',
//	'button_callback'		=> array(),
);


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'cmail';
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace('sendViaEmail', 'sendViaEmail,cmail', $GLOBALS['TL_DCA']['tl_form']['palettes']['default']);


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['cmail'] = 'cmailRecipient,cmailTemplate';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['cmail'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmail'],
	'inputType'				=> 'checkbox',
	'eval'					=> array('submitOnChange'=>true, 'tl_class'=>'clr'),
);

$GLOBALS['TL_DCA']['tl_form']['fields']['cmailRecipient'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmailRecipient'],
	'inputType'				=> 'select',
	'default'				=> 'email',
	'options_callback'		=> array('tl_form_formmails', 'getFields'),
	'eval'					=> array('includeBlankOption'=>true, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_form']['fields']['cmailTemplate'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmailTemplate'],
	'inputType'				=> 'select',
	'foreignKey'			=> 'tl_mail_templates.name',
	'eval'					=> array('mandatory'=>true, 'includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
);


class tl_form_formmails extends Backend
{
	
	public function getFields($dc)
	{
		$arrFields = array();
		$objFields = $this->Database->execute("SELECT * FROM tl_form_field WHERE pid={$dc->id} AND name!=''");
		
		while( $objFields->next() )
		{
			$arrFields[$objFields->id] = $objFields->label ? $objFields->label : $objFields->name;
		}
		
		return $arrFields;
	}
}
