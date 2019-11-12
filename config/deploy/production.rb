set :branch, ENV["BRANCH"] || "master"


server "library-prod1", user: "deploy", roles: %w{app}
server "library-prod3", user: "deploy", roles: %w{app}
server "library-prod4", user: "deploy", roles: %w{app}

