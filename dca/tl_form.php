<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
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
 * @copyright  terminal42 gmbh 2010-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Kamil Kuzminski <kamil.kuzminski@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
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
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['cmail'] = 'cmail_templates';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['cmail'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmail'],
	'inputType'				=> 'checkbox',
	'eval'					=> array('submitOnChange'=>true, 'tl_class'=>'clr'),
);

$GLOBALS['TL_DCA']['tl_form']['fields']['cmail_templates'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmail_templates'],
	'inputType'				=> 'multiColumnWizard',
	'eval'                  => array('mandatory'=>true, 'columnFields'=>array
	(
		'recipient' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmail_recipient'],
			'inputType'				=> 'select',
			'options_callback'      => array('tl_form_formmails', 'getFields'),
			'eval'					=> array('includeBlankOption'=>true, 'style'=>'width:130px;'),
		),
		'template' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmail_template'],
			'inputType'				=> 'select',
			'foreignKey'            => 'tl_mail_templates.name',
			'eval'					=> array('mandatory'=>true, 'style'=>'width:130px;'),
		),
		'additional_recipients' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_form']['cmail_additionalRecipients'],
			'inputType'				=> 'textarea',
			'eval'					=> array('style'=>'width:320px;height:30px;')
		)
	))
);


class tl_form_formmails extends Backend
{

	public function getFields()
	{
		$arrFields = array();
		$objFields = $this->Database->prepare("SELECT * FROM tl_form_field WHERE pid=? AND name!=''")
									->execute($this->Input->get('id'));

		while( $objFields->next() )
		{
			$arrFields[$objFields->id] = $objFields->label ? $objFields->label : $objFields->name;
		}

		return $arrFields;
	}
}
