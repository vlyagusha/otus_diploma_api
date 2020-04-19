# config valid for current version and patch releases of Capistrano
lock "~> 3.13.0"

set :application, 'Otus_HW_17'
set :repo_url, 'git@github.com:vlyagusha/otus_diploma_api.git'

# Default branch is :master
set :branch, 'master'

# Default deploy_to directory is /var/www/my_app_name
set :deploy_to, '/home/vlyagusha/www/otus'

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
append :linked_files, '.env'

# Default value for linked_dirs is []
append :linked_dirs, 'var'

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for local_user is ENV['USER']
# set :local_user, -> { `git config user.name`.chomp }

# Default value for keep_releases is 5
set :keep_releases, 5

# Uncomment the following to require manually verifying the host key before first deploy.
# set :ssh_options, verify_host_key: :secure

append :copy_files, 'vendor'

namespace :cache do
  task :clear do
    on roles(:web) do
      within release_path do
        execute "bin/console", "cache:clear"
      end
    end
  end

  task :warmup do
      on roles(:web) do
        within release_path do
          execute "bin/console", "cache:warmup"
        end
      end
    end
end

namespace :migrations do
  task :migrate do
    on roles(:db) do
      within release_path do
        execute "bin/console", "doctrine:migrations:migrate", "--no-interaction"
      end
    end
  end
end

namespace :deploy do
  after :updated, 'migrations:migrate'
  after :updated, 'cache:clear'
  after :updated, 'cache:warmup'
end
