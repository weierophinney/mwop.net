run "cp #{shared_path}/appconfig/*.php #{release_path}/config/autoload/)"
run "ln -nfs #{shared_path}/users.db #{release_path}/data/users.db"
run "cd #{release_path} && php public/index.php githubfeed fetch"
run "cd #{release_path} && php public/index.php phlysimplepage cache clear all"
