run! "exec ssh-agent bash -c 'ssh-add /home/deploy/.ssh/mwopnet-deploy-key && git clone -b mwop.net.config git@github.com:weierophinney/site-settings.git #{current_path}/tmp/config'"
run "mv #{current_path}/tmp/config/*.php #{release_path}/config/autoload/)"

run "ln -nfs #{shared_path}/users.db #{release_path}/data/users.db"
run "cd #{release_path} && php public/index.php githubfeed fetch"
run "cd #{release_path} && php public/index.php phlysimplepage cache clear all"
