<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'SecureMyData');

// Project repository
set('repository', 'git@github.com:YaroslavMelnychenko/secure-my-data-server.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false); 

// Shared files/dirs between deploys 
set('shared_files', []);

add('shared_files', [
    'src/.env'
]);

set('shared_dirs', ['src/storage']);

add('shared_dirs', ['src/vendor']);

// Writable dirs by web server 
set('writable_dirs', [
    'src/bootstrap/cache',
    'src/storage',
    'src/storage/app',
    'src/storage/app/public',
    'src/storage/app/session/keys',
    'src/storage/framework',
    'src/storage/framework/cache',
    'src/storage/framework/sessions',
    'src/storage/framework/views',
    'src/storage/logs'
]);

set('log_files', 'src/storage/logs/*.log');

// Artisan
set('artisan', function () {
    return run('echo {{release_path}}/src/artisan');
});

// Laravel version
set('laravel_version', function () {
    $result = run('{{bin/php}} {{artisan}} --version');
    preg_match_all('/(\d+\.?)+/', $result, $matches);
    return $matches[0][0] ?? 5.5;
});

// Hosts
host('deployer')
    ->set('deploy_path', '/var/www/smd')
    ->configFile('/etc/ssh/ssh_config');    

// Tasks

// Upload .env.production
task('upload:env', function () {
    run('mkdir -p {{deploy_path}}/shared/src');
    runLocally('rsync -v -e ssh src/.env.production deployer:{{deploy_path}}/shared/src/.env');
})->desc('Environment setup');

task('deploy:vendors', function () {
    run('cd {{release_path}}/src && composer install --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader --no-suggest');
})->desc('Install composer dependencies');

// Generate application key
task('artisan:key:generate', function () {
    run('{{bin/php}} {{artisan}} key:generate --ansi');
});

// Clear view cache
task('artisan:view:clear', function () {
    run('{{bin/php}} {{artisan}} view:clear');
});

// Migrate
task('artisan:migrate', function () {
    run('{{bin/php}} {{artisan}} migrate');
});

// Migrate refresh
task('artisan:migrate:refresh', function () {
    run('{{bin/php}} {{artisan}} migrate:refresh --force');
});

// Clear cache
task('artisan:cache:clear', function () {
    run('{{bin/php}} {{artisan}} cache:clear');
});

// Clear config cache
task('artisan:config:clear', function () {
    run('{{bin/php}} {{artisan}} config:clear');
});

// Passport install
task('passport:install', function () {
    run('{{bin/php}} {{artisan}} passport:install');
});

// Clear all application data
task('artisan:application:flush', function () {
    run('{{bin/php}} {{artisan}} application:flush');
});

// Tasks
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php7.2-fpm.service');
})->desc('Restart PHP-FPM service');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'upload:env',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:key:generate',
    'artisan:view:clear',
    'artisan:migrate',
    'php-fpm:restart',
    'artisan:cache:clear',
    'artisan:config:clear',

    'deploy:symlink',
    'deploy:unlock',
    'cleanup'
]);