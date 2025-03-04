# Alocasia

![](alocasia.jpg)

Alocasiaは、スタックベースのプログラミング言語です。[PHPerKaigi 2025のセッション: PHPで作る電子計算機](https://fortee.jp/phperkaigi-2025/proposal/32569e1d-99ae-4b61-a839-be77ee3127e6)で紹介された言語です。

## インストール

```
$ git clone git@github.com:shunsock/alocasia.git
$ cd alocasia
$ nix build .#alocasia
$ ./result/bin/alocasia -h
usage main.php file, main.php -i interactive mode, main.php -o oneliner mode
```

## 使い方

Alocasiaはいくつか実行方法があります。次の例は、ファイルからソースコードを入力する形式です。

```shell
$ ./result/bin/alocasia ./document/example.aloc
1
1
3.14
0
1
Hello World
```

Alocasiaは `-o` でOneliner記法をサポートしています。

```
$ ./result/bin/alocasia -o "x = { 1 2 + } x print"
3
```

また、インタラクティブモードが存在します。インタラクティブモードは `exit` で終了します。

```
$ ./result/bin/alocasia -i
入力してください (exitで終了):
> 100 108 114 111 87 32 111 108 108 101 72 11 print_ascii_str
Hello World
> exit
bye%
```

## 構文

Alocasiaは次のような特徴を持っています。

- 型はintegerとfloatのどちらかです
- 逆ポーランド記法を使います
    - 例: `1 + 2` は `1 2 +` と書きます
- ユーザー定義関数はありません

次の例は、Alocasiaの基本的な構文です。

```shell
1 print
# 1

1 1 print
# 1

x = { 3.14 }
x print
# 3.14

if { 0 } { 1 print } { 0 print }
# 0

if { 1 } { 1 print } { 0 print }
# 1

y = { 0 }
loop { if { y 5 == } { 0 } { y = { y 1 + } 1 } }

100 108 114 111 87 32 111 108 108 101 72 11 print_ascii_str
# Hello World
```

詳細を確認したい場合は[ドキュメント](./document/syntax.md)を参照してください。

## ライセンス

[MIT](./LICENSE)

## コントリビュート

プルリクエストを歓迎します。気になる点があれば、Issueを立ててください。

