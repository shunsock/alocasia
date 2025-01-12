{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    # PHP 8.3 with mbstring enabled
    (pkgs.php83.withExtensions (extensions: with extensions.all; [ mbstring ]))
    # Composer
    pkgs.php83.packages.composer

    # PHPStan
    pkgs.php83.packages.phpstan

    # PHPUnit
    pkgs.phpunit

    # Justfile
    pkgs.just
  ];
}

