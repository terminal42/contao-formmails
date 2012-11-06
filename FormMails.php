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


class FormMails extends Frontend
{

	public function processFormData($arrPost, $arrForm, $arrFiles)
	{
		if ($arrForm['cmail'] && $arrForm['cmailTemplate'])
		{
			$objField = $this->Database->prepare("SELECT name FROM tl_form_field WHERE id=?")->limit(1)->execute($arrForm['cmailRecipient']);
			$arrSent = array();
			$arrRecipients = trimsplit(',', $arrForm['cmailAdditionalRecipients']);

			// Send an e-mail to recipient
			if ($objField->numRows)
			{
				array_unshift($arrRecipients, $arrPost[$objField->name]);
			}

			// Send e-mails
			if (!empty($arrRecipients))
			{
				try
				{
					$objEmail = new EmailTemplate($arrForm['cmailTemplate']);
					$arrData = $this->preparePostData($arrPost);
	
					foreach ($arrRecipients as $strEmail)
					{
						if ($this->isValidEmailAddress($strEmail) && ($objEmail->send($strEmail, $arrData) || true))
						{
							$arrSent[] = $strEmail;
						}
					}
				}
				catch (Exception $e)
				{
					$this->log('Unable to send e-mail: ' . $e->getMessage(), 'FormMails processFormData()', TL_ERROR);
				}
			}

			// Create a log entry
			if (!empty($arrSent))
			{
				$this->Database->prepare("INSERT INTO tl_form_mails (pid,tstamp,cmailSender,cmailSubject,cmailRecipient,cmailBcc,cmailMessage,form_post,form_files) VALUES (?,?,?,?,?,?,?,?,?)")
							   ->execute($arrForm['id'], time(), $objEmail->from, (string) $objEmail->subject, $arrForm['cmailRecipient'], implode(', ', $arrSent), nl2br($objEmail->text), serialize($arrPost), serialize($arrFiles));
			}
		}
	}


	private function preparePostData($arrData, $blnImplode=false)
	{
		foreach( $arrData as $k => $v )
		{
			if (is_array($v))
			{
				$arrData[$k] = $this->preparePostData($v, true);
			}
		}

		if ($blnImplode)
		{
			return implode(', ', $arrData);
		}

		return $arrData;
	}
}

