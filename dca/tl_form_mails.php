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
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


/**
 * Load CSV file
 */
$GLOBALS['TL_CSS'][] = 'system/modules/formmails/html/style.css';


/**
 * Table tl_form_mails
 */
$GLOBALS['TL_DCA']['tl_form_mails'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'					=> 'Table',
		'ptable'						=> 'tl_form',
		'closed'						=> true,
		'notEditable'					=> true,
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'						=> 4,
			'fields'					=> array('tstamp DESC'),
			'flag'						=> 8,
			'panelLayout'				=> 'filter,limit',
			'headerFields'				=> array('id', 'title'),
			'child_record_callback'		=> array('tl_form_mails', 'listRows')
		),
		'global_operations' => array
		(
//			'export' => array
//			(
//				'label'					=> &$GLOBALS['TL_LANG']['tl_form_mails']['export'],
//				'href'					=> 'key=export_mails',
//				'class'					=> 'header_export_mails',
//				'attributes'			=> 'onclick="Backend.getScrollOffset();"'
//			),
			'all' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'					=> 'act=select',
				'class'					=> 'header_edit_all',
				'attributes'			=> 'onclick="Backend.getScrollOffset();"'
			),
		),
		'operations' => array
		(
			'delete' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_form_mails']['delete'],
				'href'					=> 'act=delete',
				'icon'					=> 'delete.gif',
				'attributes'			=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_form_mails']['show'],
				'href'					=> 'act=show',
				'icon'					=> 'show.gif'
			),
		)
	),

	// Fields
	'fields' => array
	(
		'tstamp' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_form_mails']['tstamp'],
			'filter'					=> true,
			'flag'						=> 8,
		),
		'cmailRecipient' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_form_mails']['cmailRecipient'],
			'inputType'					=> 'select',
			'default'					=> 'email',
			'options_callback'			=> array('tl_form_formmails', 'getFields'),
			'eval'						=> array('includeBlankOption'=>true, 'tl_class'=>'w50'),
		),
		'cmailBcc' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_form_mails']['cmailBcc'],
			'inputType'					=> 'text',
			'default'					=> $GLOBALS['TL_ADMIN_EMAIL'],
			'eval'						=> array('maxlength'=>255, 'rgxp'=>'extnd', 'tl_class'=>'w50'),
		),
		'cmailSender' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_form_mails']['cmailSender'],
			'inputType'					=> 'text',
			'eval'						=> array('maxlength'=>255, 'rgxp'=>'email', 'tl_class'=>'w50'),
		),
		'cmailSubject' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_form_mails']['cmailSubject'],
			'inputType'					=> 'text',
			'eval'						=> array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
		),
		'cmailMessage' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_form_mails']['cmailMessage'],
			'inputType'					=> 'textarea',
			'eval'						=> array('mandatory'=>true, 'tl_class'=>'clr'),
		),
		'source' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_form_mails']['source'],
			'eval'						=> array('fieldType'=>'checkbox', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'csv', 'class'=>'mandatory'),
		),
	)
);


class tl_form_mails extends Backend
{
	
	public function listRows($arrRow)
	{
		return '
<div class="cte_type"><strong>' . $arrRow['cmailSubject'] . '</strong> - ' . $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['tstamp']) . '</div>
<div class="block">
' . $arrRow['cmailMessage'] . '
</div>' . "\n";
	}
	
	
	public function export($dc)
	{
		$arrData = array();
		$objData = $this->Database->execute("SELECT * FROM tl_form_mails WHERE pid={$dc->id} ORDER BY tstamp");
		
		while( $objData->next() )
		{
			$arrData[] = array_merge(array('date'=>$this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $objData->tstamp)), deserialize($objData->form_post, true));
		}
		
		$objParser = new parseCSV();
		
		$objParser->output(true, 'form-'.$dc->id.'.csv', $arrData, array_keys($arrData[0]));
	}
	
	
	public function importFormAutoCSV()
	{
		if ($this->Input->get('key') != 'import_facsv')
		{
			return '';
		}

		// Import CSV
		if ($this->Input->post('FORM_SUBMIT') == 'tl_formauto_csv')
		{
			if (!$this->Input->post('source') || !is_array($this->Input->post('source')))
			{
				$_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['all_fields'];
				$this->reload();
			}

			foreach ($this->Input->post('source') as $strFile)
			{
				// Folders cannot be imported
				if (is_dir(TL_ROOT . '/' . $strFile))
				{
					$_SESSION['TL_ERROR'][] = sprintf($GLOBALS['TL_LANG']['ERR']['importFolder'], basename($strFile));
					continue;
				}

				// Check the file extension
				if (pathinfo($strFile, PATHINFO_EXTENSION) != 'csv')
				{
					$_SESSION['TL_ERROR'][] = sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension);
					continue;
				}
				
				$intForm = $this->Input->get('id');
				$imported = 0;
				
				$objParser = new parseCSV();
				$objParser->auto(TL_ROOT . '/' . $strFile);
				
				foreach( $objParser->data as $arrRow )
				{
					$objDate = new Date($arrRow['Datum'], $GLOBALS['TL_CONFIG']['datimFormat']);
					
					$arrSet = array('pid'=>$intForm, 'tstamp'=>$objDate->tstamp, 'cmailSubject'=>'AutoForm Import', 'form_post'=>$arrRow);
					
					unset($arrSet['form_post']['IP-Adresse']);
					unset($arrSet['form_post']['Datum']);
					
					$this->Database->prepare("INSERT INTO tl_form_mails %s")->set($arrSet)->execute();
					$imported++;
				}

				// Notify the user
				$_SESSION['TL_CONFIRM'][] = sprintf('%s records imported.', $imported);
			}

			// Redirect
			setcookie('BE_PAGE_OFFSET', 0, 0, '/');
			$this->redirect(str_replace('&key=import_facsv', '', $this->Environment->request));
		}

		$objTree = new FileTree($this->prepareForWidget($GLOBALS['TL_DCA']['tl_form_mails']['fields']['source'], 'source', null, 'source', 'tl_form_mails'));

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=import_facsv', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_form_mails']['import'][1].'</h2>'.$this->getMessages().'

<form action="'.ampersand($this->Environment->request, true).'" id="tl_formauto_csv" class="tl_form" method="post">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_formauto_csv" />

<div class="tl_tbox block">
  <h3><label for="source">'.$GLOBALS['TL_LANG']['tl_form_mails']['source'][0].'</label> <a href="contao/files.php" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['fileManager']) . '" onclick="Backend.getScrollOffset(); Backend.openWindow(this, 750, 500); return false;">' . $this->generateImage('filemanager.gif', $GLOBALS['TL_LANG']['MSC']['fileManager'], 'style="vertical-align:text-bottom;"') . '</a></h3>'.$objTree->generate().(strlen($GLOBALS['TL_LANG']['tl_form_mails']['source'][1]) ? '
  <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_form_mails']['source'][1].'</p>' : '').'
</div>

</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['tl_form_mails']['import'][0]).'" />
</div>

</div>
</form>';
	}
}

