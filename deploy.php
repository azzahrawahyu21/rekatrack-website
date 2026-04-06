<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';
require 'contrib/rsync.php';

// Config
set('bin/php', function () {
    return 'php8.2';
});

set('repository', 'https://github.com/AhmadShodikinn/reka-tracking-web.git');
set('keep_releases', 3);
set('rsync_src', function () {
    return __DIR__;
});

add('rsync', [
    'exclude' => [
        '.git/',
        'vendor/',
        'node_modules/',
        '.github/',
        'deploy.php',
    ],
]);
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path') . '/shared');
});
// Hosts


host('rekatrack')
    ->set('remote_user', 'rekachain')
    ->setHostname('103.211.26.90')
    ->set('deploy_path', '/var/www/rekatrack.ptrekaindo.co.id');

// Hooks

after('deploy:failed', 'deploy:unlock');

task('deploy', [
    'deploy:prepare',
    'rsync',
    'deploy:secrets',
    'deploy:vendors',
    'deploy:shared',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'deploy:publish',
]);

desc('End of application deployment');