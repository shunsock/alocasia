{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    # PHP 8.3
    pkgs.php83
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

