README.md

https://github.com/vercel-community/php
    Node 18.x / PHP 8.3.x (https://example-php-8-3.vercel.app)

Vercel PATH = /var/task/user/api/README.md

php.ini
	curl.cainfo="/var/task/user/cacert.pem"
	openssl.cafile="/var/runtime/ca-cert.pem"
database.php redis
            'scheme' => 'tls',
            'read_timeout' => 600,
            'timeout' => 600,

Vercel.json
        "api/index.php": {
            "runtime": "vercel-php@0.5.4",
            "memory": 3008,
            "maxDuration": 180
        }

https://vercel.com/docs/functions/runtimes#file-system-support

https://vercel.com/docs/deployments/troubleshoot-a-build#understanding-build-cache

https://github.com/vercel-community/php/issues/518

dg/composer-cleaner