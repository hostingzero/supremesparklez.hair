<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          ';f4q, M)u5b[|]qZ{~#(n50j8Mgq~W%W:58XN~x@?H~xz.caFh^)CHl=pVSYc/jQ' );
define( 'SECURE_AUTH_KEY',   'HGONn=S[xBLv%|WwN?3i6Zu?FyhuL}tor:fThI*0V.{!ISV6G GB,LN77`4Z7tCj' );
define( 'LOGGED_IN_KEY',     '=s^>OemYLF<Hp,&~gG<sXA.5h/T2tl@60R3TNHJ`Om,bH |A9TzKUKW_wD29Fb!;' );
define( 'NONCE_KEY',         'kV@>r*b`!zT*(sFJ1eruj7r+b|$$pyp/w&nI,6xJ#*st]MuDA^SS<KF8|K/T0os ' );
define( 'AUTH_SALT',         'i`>WquHCNwwLGbR^=_Vt}7fPE0(U5XB|mCY.jBNX{%$a?FpWEl.~NjQIceW=lPb_' );
define( 'SECURE_AUTH_SALT',  '4@z[UPvk)~;g]e;5l4pEd/!OE-O6o37IQnPs,Y=]fsXBmHLNx[1;l3^4)s-#+R5Z' );
define( 'LOGGED_IN_SALT',    '*S];(8?hXB<j[JDvPf}]LLZY~h#C0~aQY~q$t8vLOv>kT/dJ1o(vs/fePBp,[.)c' );
define( 'NONCE_SALT',        'h~:Js3b!+t{G,bX@n7C8%v8+^)[iCZ#CH+)k^y#ca;Xpe,Q9V&T,SY/uJs>puk h' );
define( 'WP_CACHE_KEY_SALT', 'f%-]`>(mFysGofW&e]-X%+Go`wHr^E|1T${J6hqQ )39M^r=,3{+P_^W6,#VA3v>' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
