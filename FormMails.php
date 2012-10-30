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


class FormMails extends Frontend
{

	public function processFormData($arrPost, $arrForm, $arrFiles)
	{
		if ($arrForm['cmail'] && $arrForm['cmailTemplate'])
		{
			$objField = $this->Database->prepare("SELECT name FROM tl_form_field WHERE id=?")
									   ->limit(1)
									   ->execute($arrForm['cmailRecipient']);

			// Return if field can not be found or the e-mail is invalid
			if (!$objField->numRows || !$this->isValidEmailAddress($arrPost[$objField->name]))
			{
				return;
			}

			$objTemplate = $this->Database->prepare("SELECT * FROM tl_mail_templates WHERE id=?")
										  ->limit(1)
										  ->execute($arrForm['cmailTemplate']);

			// Return if the template was not found
			if (!$objTemplate->numRows)
			{
				return;
			}

			$objEmail = new EmailTemplate($objTemplate->id);

			if ($objEmail->send($arrPost[$objField->name], $this->preparePostData($arrPost)))
			{
				$this->Database->prepare("INSERT INTO tl_form_mails (pid,tstamp,cmailSender,cmailSubject,cmailRecipient,cmailBcc,cmailMessage,form_post,form_files) VALUES (?,?,?,?,?,?,?,?,?)")
							   ->execute($arrForm['id'], time(), $objTemplate->sender_address, $objEmail->subject, $arrForm['cmailRecipient'], ($objTemplate->recipient_bcc ? $objTemplate->recipient_bcc : ''), nl2br($objEmail->text), serialize($arrPost), serialize($arrFiles));
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

