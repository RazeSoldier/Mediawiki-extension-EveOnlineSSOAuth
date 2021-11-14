<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

namespace RazeSoldier\MWEveOnlineSSOAuth;

use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\DBError;
use Wikimedia\Rdbms\IDatabase;
use Wikimedia\Rdbms\IResultWrapper;

/**
 * 更新`eve_character`表的包装器
 */
class EveCharacterTableUpdater {
	private const TABLE_NAME = 'eve_character';

	/**
	 * @var IDatabase
	 */
	private $dbConn;

	/**
	 * @param int $serverIndex DB_PRIMARY或者DB_REPLICA
	 */
	public function __construct( int $serverIndex = DB_REPLICA ) {
		$this->dbConn = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnectionRef( $serverIndex );
	}

	/**
	 * 尝试存储数据如果$characterId不存在
	 * @param int $characterId
	 * @param string $name
	 * @throws DBError
	 */
	public function createIfNotExits( int $characterId, string $name ): void {
		if ( $this->first( $characterId )->count() === 0 ) {
			$this->create( $characterId, $name );
		}
	}

	/**
	 * 在eve_character表查找匹配$characterId的第一条数据
	 * @param int $characterId
	 * @return IResultWrapper
	 * @throws DBError
	 */
	public function first( int $characterId ): IResultWrapper {
		return $this->dbConn->select(
			self::TABLE_NAME,
			'ec_id',
			"ec_id = $characterId",
			__METHOD__,
			[ 'limit' => 1 ]
		);
	}

	/**
	 * 向eve_character表存储数据
	 * @param int $characterId
	 * @param string $name
	 * @throws DBError
	 */
	private function create( int $characterId, string $name ): void {
		$this->dbConn->insert(
			self::TABLE_NAME,
			[ 'ec_id' => $characterId, 'ec_name' => $name ],
			__METHOD__
		);
	}
}
