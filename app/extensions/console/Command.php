<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program (see LICENSE.txt in the base directory.  If
 * not, see:
 *
 * @link <http://www.gnu.org/licenses/>.
 * @author niel
 * @copyright 2014 nZEDb
 */
namespace app\extensions\console;


use lithium\data\DocumentSchema;

class Command extends \lithium\console\Command
{
	protected $_classes = [
			'response'	=> 'app\extensions\console\Response'
	];

	public function __construct(array $config = array())
	{
		$defaults = ['request' => null, 'response' => [], 'classes' => $this->_classes];
		parent::__construct($config + $defaults);
	}

	public function info($text)
	{
		if ($this->silent) {
			return;
		}
		$this->out($text, 'info');
	}

	public function primary($text)
	{
		if ($this->silent) {
			return;
		}
		$this->out($text, 'primary');
	}
}
?>
