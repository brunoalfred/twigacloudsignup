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

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\Security\ISecureRandom;

class RegistrationMapper extends QBMapper {

	/** @var ISecureRandom */
	protected $random;

	public function __construct(IDBConnection $db, ISecureRandom $random) {
		parent::__construct($db, 'twigacloudsignup', Registration::class);
		$this->random = $random;
	}

	/**
	 * @param string $token
	 * @return Registration
	 * @throws DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function findByToken(string $token): Entity {
		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from($this->getTableName())
			->where($query->expr()->eq('token', $query->createNamedParameter($token)));

		return $this->findEntity($query);
	}

	/**
	 * @param string $secret
	 * @return Registration
	 * @throws DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function findBySecret(string $secret): Entity {
		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from($this->getTableName())
			->where($query->expr()->eq('client_secret', $query->createNamedParameter($secret)));

		return $this->findEntity($query);
	}

	/**
	 * @param string $secret
	 * @return Registration
	 * @throws DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function findByUserId(string $userId): Entity
	{
		$query = $this->db->getQueryBuilder();
		$query->select('*')
		->from($this->getTableName())
			->where($query->expr()->eq('username', $query->createNamedParameter($userId)));

		return $this->findEntity($query);
	}


	public function usernameIsPending(string $username): bool {
		try {
			$query = $this->db->getQueryBuilder();
			$query->select('*')
				->from($this->getTableName())
				->where($query->expr()->eq('username', $query->createNamedParameter($username)));

			$this->findEntity($query);
		} catch (DoesNotExistException $e) {
			return false;
		}
		return true;
	}

	/**
	 * @param string $phone
	 * @return Registration
	 */
	public function find(string $phone): Entity {
		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from($this->getTableName())
			->where($query->expr()->eq('phone', $query->createNamedParameter($phone)));

		return $this->findEntity($query);
	}

	/**
	 * @param Entity $entity
	 * @return Registration
	 */
	public function insert(Entity $entity): Entity {
		$entity->setRequested(date('Y-m-d H:i:s'));
		return parent::insert($entity);
	}

	/**
	 * @param Registration $registration
	 */
	public function generateNewToken(Registration $registration): void {
		$token = $this->random->generate(6, ISecureRandom::CHAR_DIGITS);
		$registration->setToken($token);
	}

	/**
	 * @param Registration $registration
	 */
	public function generateClientSecret(Registration $registration): void {
		$token = $this->random->generate(32, ISecureRandom::CHAR_HUMAN_READABLE);
		$registration->setClientSecret($token);
	}

	public function deleteOlderThan(\DateTime $date): void {
		$query = $this->db->getQueryBuilder();
		$query->delete($this->getTableName())
			->where($query->expr()->lt('requested', $query->createNamedParameter($date, IQueryBuilder::PARAM_DATE)))
			->executeStatement();
	}
}
