<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'ogidevDBpypjs');

/** MySQL database username */
define('DB_USER', 'ogidevDBpypjs');

/** MySQL database password */
define('DB_PASSWORD', 'ztTI9H9kcT');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ';litLDO_~]halD5H~w_aSdGhZ_91ph~G8ZS|1[dZw81OK~w!VRoh[GCwo-NJgZ!84');
define('SECURE_AUTH_KEY',  'bn3E$<,Mn{7u^$EfX*AifqITPy2bXiA2E+<.Lm;]6t*+De#.;mepHTPx2aWi9LHK');
define('LOGGED_IN_KEY',    'hCS[51Z-C8K~-|dol:Ows~Vhd|GokwNGR[40ZzC8J!}[Rok}Nvr@Ugc,FnkvNYU@7');
define('NONCE_KEY',        '$U^BjfrIUQy3bYu73E$<,Mn<7qnyQbX*Ajb$EAM.{Tum;Pyu*Xie<HqmxPHT]62');
define('AUTH_SALT',        'iEm+xAb.*]itq2PLt;WTe6HDm#PLW;95e~HDO_1]htpWx95H~]#Op1:9x_~Gh[#1p');
define('SECURE_AUTH_SALT', 'nJYU$7B7Iu>Qr3By,^Ij{<3q$yAb.*{iuq2T+y.bmi{Luq+Teb.Eme*LXT+6eam');
define('LOGGED_IN_SALT',   'H;959a_~]htp1S-w_ZSd5GCl|OKh[|1o-w8Z!~[hZkCNKs:VRo0}8w!@Fg[|0ozsJ');
define('NONCE_SALT',       'H;ami]Ltp+Sea_Dlh~KDO#1;Wx95G-w_Zlh[Ksp-SdZ_ClhsGCO|1:Vw84R-w!Zkg');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define('DISALLOW_FILE_EDIT', true);
define('FS_METHOD', 'ftpext');