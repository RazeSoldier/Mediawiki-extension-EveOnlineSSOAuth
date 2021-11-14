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

use DatabaseUpdater;

class EveOnlineSSOAuthHooks {
	/**
	 * 实现WSOAuthGetAuthProviders Hook。
	 * 添加{@link EveOnlineSSOAuthProvider}作为SOAuth扩展的OAuth提供者。
	 * @see https://www.mediawiki.org/wiki/Extension:WSOAuth/Hooks/WSOAuthGetAuthProviders
	 * @param array &$authProviders
	 */
	public static function addAuthProvider( array &$authProviders ) {
		$authProviders['eveonline'] = EveOnlineSSOAuthProvider::class;
	}

	/**
	 * 实现WSOAuthAfterGetUser Hook。
	 * @see https://www.mediawiki.org/wiki/Extension:WSOAuth/Hooks/WSOAuthAfterGetUser
	 * @param array &$userInfo
	 */
	public static function onAfterGetUser( array &$userInfo ) {
		$characterID = $userInfo['characterID'];
		$name = $userInfo['name'];

		$updater = new EveCharacterTableUpdater( DB_PRIMARY );
		$updater->createIfNotExits( $characterID, $name );
	}

	/**
	 * 实现核心的LoadExtensionSchemaUpdates Hook
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/LoadExtensionSchemaUpdates
	 * @param DatabaseUpdater $updater
	 */
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
		$updater->addExtensionTable( 'eve_character', __DIR__ . '/../sql/create_eve_character_table.sql' );
	}
}
