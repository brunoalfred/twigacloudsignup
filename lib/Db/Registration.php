<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2022 Bruno Alfred <hello@brunoalfred.me>
 *
 * @author Bruno Alfred <hello@brunoalfred.me>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Twigacloudsignup\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getPhone()
 * @method void setPhone(string $phone)
 * @method string getUsername()
 * @method void setUsername(string $username)
 * @method string getPassword()
 * @method void setPassword(string $password)
 * @method string getDisplayname()
 * @method void setDisplayname(string $displayname)
 * @method bool getEmailConfirmed()
 * @method void setEmailConfirmed(bool $emailConfirmed)
 * @method string getToken()
 * @method void setToken(string $token)
 * @method string getClientSecret()
 * @method void setClientSecret(string $clientSecret)
 * @method string getRequested()
 * @method void setRequested(string $requested)
 */
class Registration extends Entity {
	public $id;
	protected $phone;
	protected $username;
	protected $displayname;
	protected $password;
	protected $token;
	protected $requested;
	protected $phoneConfirmed;
	protected $clientSecret;

	public function __construct() {
		$this->addType('phone', 'string');
		$this->addType('username', 'string');
		$this->addType('password', 'string');
		$this->addType('displayname', 'string');
		$this->addType('phoneConfirmed', 'boolean');
		$this->addType('token', 'string');
		$this->addType('clientSecret', 'string');
		$this->addType('requested', 'datetime');
	}
}
