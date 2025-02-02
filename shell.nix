{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    # PHP 8.3 with mbstring enabled
    (pkgs.php83.withExtensions (extensions: with extensions.all; [ mbstring ctype dom tokenizer xmlwriter ]))
    # Composer
    pkgs.php83.packages.composer

    # PHPStan
    pkgs.php83.packages.phpstan

    # Justfile
    pkgs.just
  ];
}

