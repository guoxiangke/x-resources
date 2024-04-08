README.md

https://github.com/vercel-community/php
    Node 18.x / PHP 8.3.x (https://example-php-8-3.vercel.app)

Error: No Output Directory named "dist" found after the Build completed. You can configure the Output Directory in your Project Settings.
    Build and Output Settings > Output Directory > storage/app/

php: error while loading shared libraries: libssl.so.10: cannot open shared object file: No such file or directory
    https://github.com/vercel-community/php/issues/504
    I'm having the same issue because default node version is set to 20. It works after changed to 18.

Add 'scheme' => 'tls',


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