<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'input-imei');

// Project repository
set('repository', 'https://github.com/kwadev59/input-imei.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false); 

// Shared files/dirs between deploys 
set('shared_files', ['.env']);
set('shared_dirs', ['writable/uploads', 'writable/logs', 'writable/cache', 'public/uploads']);

// Writable dirs by web server 
set('writable_dirs', ['writable', 'public/uploads']);
set('writable_mode', 'chmod');
set('http_user', 'www-data'); // Tambahkan ini

// Hosts
host('103.191.63.191')
    ->user('root')
    ->set('deploy_path', '/var/www/input-imei-deploy');

// Custom Tasks
task('deploy:migrate', function () {
    run('cd {{release_path}} && php spark migrate --all');
});

task('deploy:restart_services', function () {
    run('echo "?passw0rdA" | sudo -S systemctl restart php8.2-fpm');
    run('echo "?passw0rdA" | sudo -S systemctl restart nginx');
});

task('deploy:chown', function () {
    run('chown -R www-data:www-data {{deploy_path}}');
});

// Main Deploy Task for Deployer 6.x
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:migrate',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:chown',
    'deploy:restart_services',
    'cleanup',
    'success'
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
