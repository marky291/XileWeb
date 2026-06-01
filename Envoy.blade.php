@servers(['web' => 'forge@xilero.net'])

@setup
    $repository = '/home/forge/xilero.net';
    $branch = 'master';
@endsetup

@story('deploy')
    update-code
    install-dependencies
    reload-fpm
    migrate
    restart-horizon
    optimize
@endstory

@task('update-code', ['on' => 'web'])
    cd {{ $repository }}
    git pull origin {{ $branch }}
@endtask

@task('install-dependencies', ['on' => 'web'])
    cd {{ $repository }}
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
@endtask

@task('reload-fpm', ['on' => 'web'])
    (
        flock -w 10 9 || exit 1
        echo 'Restarting FPM...'
        sudo -S service php8.4-fpm reload
    ) 9>/tmp/fpmlock
@endtask

@task('migrate', ['on' => 'web'])
    cd {{ $repository }}
    php8.4 artisan migrate --force
@endtask

@task('restart-horizon', ['on' => 'web'])
    cd {{ $repository }}
    php8.4 artisan horizon:terminate
@endtask

@task('optimize', ['on' => 'web'])
    cd {{ $repository }}
    php8.4 artisan optimize
@endtask
