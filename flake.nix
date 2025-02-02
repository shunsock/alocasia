{
  description = "Alocasia: Stack-based programming language";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, nixpkgs, flake-utils, ... }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = import nixpkgs { inherit system; };

        phpWithExtensions = pkgs.php83.withExtensions (extensions: with extensions.all; [ mbstring ctype dom tokenizer xmlwriter pcntl ]);

        alocasiaDerivation = pkgs.stdenv.mkDerivation {
          pname = "alocasia";
          version = "1.0.0";
          src = self;
          buildInputs = [ pkgs.php83.packages.composer phpWithExtensions ];
          buildPhase = ''
            cd app
            composer install --no-dev --optimize-autoloader
          '';
          installPhase = ''
            mkdir -p $out/app
            cp -r * $out/app/
          '';
        };

        appScript = pkgs.writeShellScriptBin "alocasia" ''
          exec ${phpWithExtensions}/bin/php ${alocasiaDerivation}/app/src/main.php "$@"
        '';
      in {
        packages = {
          alocasia = appScript;
        };

        apps = {
          alocasia = {
            type = "app";
            program = "${appScript}/bin/alocasia";
          };
        };

        defaultPackage = appScript;
      }
    );
}

