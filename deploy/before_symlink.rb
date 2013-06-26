run "(cd #{release_path} ; mkdir tmp)"
run! "(cd #{release_path}/tmp && git clone -b mwop.net.config git@github.com/weierophinney/site-settings.git && mv site-settings/*.php ../config/autoload/)"

run "ln -nfs #{shared_path}/users.db #{release_path}/data/users.db"
run "(cd #{release_path} && php public/index.php githubfeed fetch)"
run "(cd #{release_path} && php public/index.php phlysimplepage cache clear all)"
