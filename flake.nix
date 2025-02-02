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

        # PHP 8.3 に必要な拡張を追加したパッケージを定義
        phpWithExtensions = pkgs.php83.withExtensions (extensions: with extensions.all; [ mbstring ctype dom tokenizer xmlwriter pcntl ]);

        # vendor を生成するための derivation
        alocasiaDerivation = pkgs.stdenv.mkDerivation {
          pname = "alocasia";
          version = "1.0.0";
          src = self;
          # buildInputs に、composer と PHP (拡張付き) を指定
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

        # 実行用のラッパースクリプトを作成
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

