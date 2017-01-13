<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa user o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações
// com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'instituto');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** Nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'c^FJUEBk>Iq:h]eO3k.Y>POXDbaKS80kCdzrHcKv&ayU`&{NKLVX/6h0-{d5JMe^');
define('SECURE_AUTH_KEY',  'uR60+fa-KdG@_3=JUpo,Y@X-5p#1ixKbL&*G=utWu68%xN}4A`<?Cf#z|g^xt;J>');
define('LOGGED_IN_KEY',    '(yDe7t({(kuAni(%18-w$iV12e~^Y8y@+&I|oo]gv/JM^qz-:eiMrZC>{vN?<57B');
define('NONCE_KEY',        'z-]aR{,TG:EFz^(ffy51-#mRE/UP9:#Ly=+pO!BdfbvT_V,o&Xu{Kn9!wr0;Yk3|');
define('AUTH_SALT',        ',VF+`FJg4Xm~}gJnFm/!dx$ZloO]{fI~%Fo]ypPtUm%BC7qe$$;>OuwL|MITIcX4');
define('SECURE_AUTH_SALT', 'lUN/4UGe_foX4<>IDB`arwZ^l|9MhjH*I:l<SOp3vi^thO}KV1`2r^9Wy6-.|<Ng');
define('LOGGED_IN_SALT',   '@;=nv @iQb}9V[^Zy TwL;9{@ Y?zBJLWBYh(<_-UFieP-&=.a|6v>@Id~nP2vV`');
define('NONCE_SALT',       'jdnUGOik.ta[R,1CqUsk@dv}Q7+2b^(T{%r4P}9zA%m;Nsf}s!?zX=!^qN0J?}B@');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * para cada um um único prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
