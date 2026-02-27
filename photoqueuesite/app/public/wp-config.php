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
define( 'AUTH_KEY',          '6g9lM@h e_<o!GxG+<49NN,qbP[:#a+g9~FDC:jvJnDsN$+}1nXv!OBcSR4&3D<?' );
define( 'SECURE_AUTH_KEY',   'VaVWjQ3XT@qh])Yy8Oeo+Xq%bSbtXMupQE,{;9Sdsd%<,ijdE}W^h=mv7~=4)pJN' );
define( 'LOGGED_IN_KEY',     '.%kcQPQ[)`D2%1?@W!df&@~[OAh+x~HxG)Q%OsPY$DQbZmt~iu)z(h$=l7/#0~-D' );
define( 'NONCE_KEY',         'tM:@xxLgQHN6J(ZW}CaEm1N@[-O}X9u>$#(j<)}poLXDS)-O{v?3a@%UA_23KJ?z' );
define( 'AUTH_SALT',         '{ZDIj?hiUWCAAM3y/nG(73OhHI_fjc$~sT/e.!2CS-t^TiL Y6e&UY)b(jfdu)@m' );
define( 'SECURE_AUTH_SALT',  '/qvAgH[Qc-(((!wOjMqDyDwP$HP<|!25p*)@U_2@WmzNh$HW60De1))SUTe1Nvb)' );
define( 'LOGGED_IN_SALT',    'brPsKH9t4w.t{`YLB;ZB8b#S=Z*wyP>5XOnL!{fa CZfb/IOA{D7_@@i((rjN+>l' );
define( 'NONCE_SALT',        ',O]mb*+8(lMmTMThm6 Ua4;RLU0~k)G[%!tGuOb8xI~jkr/I{;jz[]6iL6I8oZzu' );
define( 'WP_CACHE_KEY_SALT', 's<l/PpG/Vk&gFT!Ii=.Le%:t8:SDN&hL89K~f5T f&Xiau6S(TO0H7MCFq;)Sg]A' );


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
