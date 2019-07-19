set :branch, ENV["BRANCH"] || "master"


server "library-staging1", user: "deploy", roles: %w{app}
server "library-staging2", user: "deploy", roles: %w{app}

