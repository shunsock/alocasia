{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    # PHP 8.3 with mbstring enabled
    (pkgs.php83.withExtensions (extensions: with extensions.all; [ mbstring ctype dom tokenizer xmlwriter filter ]))
    # Composer
    pkgs.php83.packages.composer
  ];
}

