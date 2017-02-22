<?php

define('FS_METHOD', 'direct');

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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db666966133' );

/** MySQL database username */
define( 'DB_USER', 'dbo666966133' );

/** MySQL database password */
define( 'DB_PASSWORD', 'dJlyhuZHxxuRlrBxzaLq' );

/** MySQL hostname */
define( 'DB_HOST', 'db666966133.db.1and1.com' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Ha#iE,k- Zx$i03NU7y5^nU!Nun=:FG+dP]M^o+%?Z]d0n,dZu1kM%{|cDQ -t$W');
define('SECURE_AUTH_KEY',  'or-@#F?4C<HB`A}:bmkV68/ss8G>SFk`Nn%f&&~`9}b>oPENw,Q[F9/b4%(C/G>V');
define('LOGGED_IN_KEY',    '!U|{gS]T!]@6GnJ-~%f6CUmhS}r&O.Rcbzh?R^5pWw-uJS3L4COmVb+t6)Gi[^Pd');
define('NONCE_KEY',        'cn;fF|$RF93o!(PiXK+>|hgLWEl{t}o+(Lx/q<$Q9s-;5_2W)g&j&J+/W j:m5RP');
define('AUTH_SALT',        'ZH,[0b^3?KA]|Gv`/s51ivT9?VMK|}D++|1D[e51%]LRd5wx|3;+&@*EqAmGE-VA');
define('SECURE_AUTH_SALT', ' &|?v.0p./XJ.>B4`__R<h-[i,;&XI8 X=@EcHK:RI-_; 0+[8x[cA3n-o{},oZN');
define('LOGGED_IN_SALT',   '*Q?H-zqMYH.[fl69P7;4^#^7?/^3/=4#(U^rU3l1;*NBc;#X!hRc&q)L]B_E),ae');
define('NONCE_SALT',       'U!x0a_.l,)]>u5Lu.n?/X!htY$#j8|$yfof5Yd[-~C3L:@NL9)h7SfI|s(V_/|--');


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'MLBvYOBX';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
