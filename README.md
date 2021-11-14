## MediaWiki-extension-EveOnlineSSOAuth
这个扩展提供了一个OAuth提供者用于[WSOAuth扩展](https://www.mediawiki.org/wiki/Extension:WSOAuth) 。
安装本扩展可以给维基提供EVE Online SSO登录的能力。即允许使用EVE帐号登录维基。

### 要求
* MediaWiki >= 1.37.0
* [WSOAuth扩展](https://www.mediawiki.org/wiki/Extension:WSOAuth) >= 4.1 -- 此扩展还依赖[PluggableAuth扩展](https://www.mediawiki.org/wiki/Extension:PluggableAuth)

### 安装
#### 下载源码
TODO

#### 安装依赖
TODO

#### 配置
下面是运行EveOnlineSSOAuth的必要配置
```injectablephp
wfLoadExtension( 'PluggableAuth' );
wfLoadExtension( 'WSOAuth' );
wfLoadExtension( 'EveOnlineSSOAuth' );
$wgGroupPermissions['*']['autocreateaccount'] = true;
$wgGroupPermissions['*']['createaccount'] = true;
$wgOAuthAuthProvider = 'eveonline';

// Following config must be set
$wgOAuthClientId = '<The client id of your application>';
$wgOAuthClientSecret = '<The client secret key of your application>';
$wgOAuthRedirectUri = '<The url to Special:PluggableAuthLogin>';
```

如果你想禁止通过标准流程创建本地用户可以使用下面配置禁用`Special:CreateAccount`特殊页面
```injectablephp
$wgSpecialPages['CreateAccount'] = DisabledSpecialPage::getCallback( 'CreateAccount' );

// 下面的代码可以隐藏右上角的创建用户链接
$wgHooks['PersonalUrls'][] = 'removeCreateAccountLink';
function removeCreateAccountLink( &$personal_urls ) {
	unset( $personal_urls['createaccount'] );
	return true;
}
```
