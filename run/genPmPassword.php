<?

$envPath = dirname(NGN_PATH);
$r = include $envPath.'/config/server.php';
$pass = crypt($r['sshPass'], base64_encode($r['sshPass']));
file_put_contents($envPath.'/pm/web/.htpasswd', $r['sshUser'].':'.$pass);
