# Syntax

## プログラムの概要

1. Stack型のプログラムです
2. 型はintegerとfloatのどちらかです
3. 逆ポーランド記法を使います
    - 例: `1 + 2` は `1 2 +` と書きます
4. ユーザー定義関数はありません
    - 組み込み関数が用意されています

## Stackに値を積む (push)

- 数値リテラルをStack積むことができます

```shell
> 1       # stack: [1]
> 1.0     # stack: [1, 1.0]
```

- alocasiaでは, StringリテラルをStackに積むことはできません
   - 💡これは意図的な仕様です
   - 何故このような仕様か考えてみましょう (ヒント: 字句解析器のコード)

```shell
> "abc" print'
Scan Error: line: 0, position: 0: プログラムで使用できない文字が含まれています: "
```

- なお、Alocasiaでは、Stackに積んだASCIIを逆順に読み、それを出力する関数が用意されています

```shell
# print_ascii_str: int型のASCIIコードを逆順に読み、それを出力する関数 (最後に長さを指定)
> 100 108 114 111 87 32 111 108 108 101 72 11 print_ascii_str
Hello World
```

### Stackに積むことのできるもの

- Stackに積むことのできるものは2つです
    - AlocasiaObject
    - AlocasiaBlock

## AlocasiaObject

- AlocasiaObjectはいわゆるオブジェクトです
    - 型と値を持ちます
    - 型は、integer, floatのいずれかです

- AlocasiaObjectの作りかた (リテラル)
    - Literalは、AlocasiaObjectとして扱われます
    - 演算子によって作られた値もAlocasiaObjectとして扱われます

```shell
> 1
> 1.0
> 1 2 +
```

- AlocasiaObjectの作りかた (変数)
    - 変数に値を代入すると、AlocasiaObjectとして扱われます
    - 変数はhashmapで管理されます

```shell
> x = { 0 }
> x = { 1 }
> x print"
1
```

- AlocasiaObjectは、可変でGlobal変数として扱われます

```shell
> x = { 0 }
> if { 1 } { x = { x 1 + } } {}
> x print
1
```

- AlocasiaObjectの実態は次のPHPのクラスです
    - StackedItemという抽象クラスを継承しています

```php
readonly class AlocasiaObject extends StackedItem
{
    public AlocasiaObjectType $type;
    public mixed $value;

    public function __construct(AlocasiaObjectType $type, mixed $value) {
        $this->type = $type;
        $this->value = $value;
    }
}
```

## AlocasiaBlock

- AlocasiaBlockの作りかた
    - AlocasiaBlockは、`{` と `}` で囲まれた部分です
    - AlocasiaBlockは、代入文や制御構文の中で使われます

```shell
> { 1 }
> x = { 1 2 + } x print
3
```

- AlocasiaBlockの実態は次のPHPのクラスです
    - StackedItemという抽象クラスを継承しています
    - Tokenの配列を持ちます
    - Blockの評価時にこのTokenの配列を取りだして評価します

```php
readonly class AlocasiaBlock extends StackedItem
{
    public int $line;

    public int $position;

    /** @var Token[]  */
    public array $tokens;

    /**
     * @param Token[] $tokens
     */
    public function __construct(int $line, int $position, array $tokens) {
        $this->line = $line;
        $this->position = $position;
        $this->tokens = $tokens;
    }
}
```

- AlocasiaBlockは、`print` などの関数で出力できません
    - `print` などの関数は、AlocasiaObjectを引数に取ります
    - AlocasiaBlockは、AlocasiaObjectではないため、`print` などの関数で出力しようとするとエラーが発生します

```shell
> { 1 2 + } print
Runtime Error: : エラーが発生しました 予期しないトークンが検出されました。期待: Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject, 実際: Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock
```

## 演算子

- 演算子は、AlocasiaObjectを引数に取り、AlocasiaObjectを返します
    - ここで指す引数とは、Stackから取り出した値のことです
    - ここで指す返り値とは、AlolcasiaObjectを指します
    - 例: `1 2 +` は `1` と `2` を引数に取り、`3` を返します

```shell
> 1     # stack: [1]
> 2     # stack: [1, 2]
> +     # stackから2と1を取り出し、それらを足して3を返す
> print # 3 (AlocasiaObjectなので、printできる)
```

## 制御構文

### if

- Syntax: `if { condition } { true_block } { false_block }`
    - `condition` が真の場合、`true_block` が評価されます
    - `condition` が偽の場合、`false_block` が評価されます
    - `condition` が真とは、Stackから取り出した値が1であることを指します
        - 1.0は1として扱われません

```shell
> if { 1 } { 1 print } { 0 print }
1
> if { 0 } { 1 print } { 0 print }
0
> if { 1.0 } { 1 print } { 0 print }
0
```

### loop

- Syntax: `loop { block }`
    - `block` が評価されます
    - `block` の評価後、Stackを取得します
        1. Stackが0以外であった場合、再度 `block` が評価されます
        2. Stackが0であった場合、ループが終了します
    - `block` の評価が終了するまで繰り返されます

```shell
> x = { 0 }
> loop { if { x 5 == } { 0 } { x = { x 1 + } 1 } }
> x print
5
```

## Error

### Scan Error

想定していない文字が含まれているような文字列の読みとりで失敗した場合、Scan Errorが発生します

```shell
> $
Scan Error: line: 0, position: 0: プログラムで使用できない文字が含まれています: $
```

### Evaluation Error

Stack UnderflowやToken Queue Underflowなどプログラムの実行時にエラーが発生した場合、Evaluation Errorが発生します

```shell
> i
Runtime Error: : エラーが発生しました Token Queue Underflowが発生しました
> 0 1 / 
Runtime Error: line: 0, position: 2 : エラーが発生しました ゼロ除算が発生しました.
```

