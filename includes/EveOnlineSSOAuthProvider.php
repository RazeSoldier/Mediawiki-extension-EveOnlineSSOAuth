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

use AuthProvider;
use DtsEve\OAuth2\Client\Provider\EveOnline;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use MediaWiki\MediaWikiServices;
use RequestContext;
use User;

class EveOnlineSSOAuthProvider implements AuthProvider {
	/**
	 * @var AbstractProvider
	 */
	private $provider;

	public function __construct() {
		$configRepo = MediaWikiServices::getInstance()->getMainConfig();
		$this->provider = new EveOnline( [
			'clientId' => $configRepo->get( 'OAuthClientId' ),
			'clientSecret' => $configRepo->get( 'OAuthClientSecret' ),
			'redirectUri' => $configRepo->get( 'OAuthRedirectUri' ),
		] );
	}

	/**
	 * @inheritDoc
	 */
	public function login( &$key, &$secret, &$auth_url ): bool {
		$auth_url = $this->provider->getAuthorizationUrl();
		$secret = $this->provider->getState();
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function logout( User &$user ) {
	}

	/**
	 * @inheritDoc
	 */
	public function getUser( $key, $secret, &$errorMessage ) {
		$request = RequestContext::getMain()->getRequest();
		$code = $request->getText( 'code' );
		if ( empty( $code ) || $request->getText( 'state' ) !== $secret ) {
			return false;
		}

		try {
			$token = $this->provider->getAccessToken( 'authorization_code', [ 'code' => $code ] );
		} catch ( IdentityProviderException $e ) {
			return false;
		}
		$user = $this->provider->getResourceOwner( $token );

		return [
			'name' => $user->getName(),
			'characterID' => $user->getCharacterID(),
		];
	}

	/**
	 * @inheritDoc
	 */
	public function saveExtraAttributes( $id ) {
	}
}
