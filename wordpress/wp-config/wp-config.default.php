<?php

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('ALLOW_UNFILTERED_UPLOADS', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'iM&yacK1|~*HgovOMApkm8&LVT(L*zemm|W`^<?}r3R!<v(;+Q&(]ojf#6Hol)jk');
define('SECURE_AUTH_KEY',  ';?}<GNKC-uP*#4mR6UBsViP10;05=uSD{Z6lPOM0zkN0@D?Bs{4I7CTx;7O$UHH?');
define('LOGGED_IN_KEY',    '_`:VpICb}_IU05AxE-|#h+Wyv[jNb{xF-c_lrygpS+*ZjuM.fv8S4sB@xI5yEI;w');
define('NONCE_KEY',        'Kyzls,<Wuee^Mcm 8T _x`8a>Q5Ir2Z:[ga@HiHOWxV)&2w/$?<J1HV`[,krm7$t');
define('AUTH_SALT',        '6I@2}[i:!l/PAk}D+qD-G_o:TLhnBo.J)QwM$J=;z%W!<O)FL:2/[XDaH.(Xau^^');
define('SECURE_AUTH_SALT', '((y].;Ny9N6M%{K`v^tN#@d~i$Qo*N_3ZB+X#fSLR^i.fxB1o{-aQhF-5s|d>FqZ');
define('LOGGED_IN_SALT',   '6DD>ZFyAi]61<~z<G|f`?C%`gr >ag/Z]MK=Tkc+i+i]~0IiWIAo[@t[fHkY@:5d');
define('NONCE_SALT',       'RU=uwMixodbr`j_fY<Pu<-hx~Al(Ial1M9.$V0j%fJ?liB#vB,tJ#~/:=sDHxJGq');

/**#@-*/


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
//define('FS_METHOD', 'direct');