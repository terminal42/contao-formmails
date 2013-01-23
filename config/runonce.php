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


class FormmailRunonce extends Controller
{

	/**
	 * Initialize the object
	 */
	public function __construct()
	{
		parent::__construct();

		// Fix potential Exception on line 0 because of __destruct method (see http://dev.contao.org/issues/2236)
		$this->import((TL_MODE=='BE' ? 'BackendUser' : 'FrontendUser'), 'User');
		$this->import('Database');
	}


	/**
	 * Run the controller
	 */
	public function run()
	{
		if (in_array('mailtemplates', $this->Config->getActiveModules()) && !$this->Database->fieldExists('cmail_templates', 'tl_form') && $this->Database->fieldExists('cmailSender', 'tl_form'))
		{
			$this->Database->query("ALTER TABLE tl_form ADD cmail_templates blob NULL");

			$time = time();
			$objForms = $this->Database->execute("SELECT * FROM tl_form WHERE cmail='1'");

			while ($objForms->next())
			{
				$arrSet = array
				(
					'tstamp' => $time,
					'name' => $objForms->title,
					'priority' => 3,
					'template' => 'mail_default'
				);

				$insertId = $this->Database->prepare("INSERT INTO tl_mail_templates %s")->set($arrSet)->execute()->insertId;

				if ($insertId > 0)
				{
					$arrSet = array
					(
						'pid' => $insertId,
						'tstamp' => $time,
						'language' => 'en',
						'fallback' => 1,
						'subject' => $objForms->cmailSubject,
						'content_text' => $objForms->cmailMessage
					);

					$this->Database->prepare("INSERT INTO tl_mail_template_languages %s")->set($arrSet)->execute();

					$arrTemplate = array(array
					(
						'recipient' => $objForms->cmailRecipient,
						'template' => $insertId,
						'additional_recipients' => $objForms->cmailBcc
					));

					$this->Database->prepare("UPDATE tl_form SET cmail_templates=? WHERE id=?")->execute(serialize($arrTemplate), $objForms->id);
				}
			}
		}

	}
}


/**
 * Instantiate controller
 */
$objRunonce = new FormmailRunonce();
$objRunonce->run();
