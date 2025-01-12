# System Architecture

## 開発環境

- Nix Shellを使います。

## 実行方法

- Nix Runを使います。
- なお、コードの内容的にPHPとComposerがあれば動くはず。
- Dockerも用意するかもしれません。（未定）

## 言語仕様

### 概要

- Stack型です。
- 型をintegerとfloatに制限します
- 型キャストを使った明示的な型の管理を行います
- 逆ポーランド記法を使います
    - 例: `1 + 2` は `1 2 +` と書きます
- ユーザー定義関数を使いません

### コメントアウト

- `#` で始まる行はコメントアウトされます

### 変数と型

- 変数はintegerとfloatのみです
- 変数宣言は次のように行います。
    - `string` 型Literalが存在しないため、let句やvar句、prefixは不要です。

```
x = {1};
y = {1.0};
```

- 変数の値は次のように決められます
    - `=`の後に`{`と`}`で囲まれた演算が書かれます
    - Evaluatorはこの演算を行います
    - 演算後Stackのトップの値を取り出し、その値をHashMapに登録します
    - 内部的には`Block`として扱われます

```
x = {1 2 +} # x = 3
y = {1.0 + if {x 0 >} {1} {0}} # y = 2.0
```

- 変数は呼び出すことができます。

```
x = {1}
y = {1.0}
z = {x y +} // z = 2.0
```

- 変数は再代入できます
- 変数は型を持ちます
- 型の変更はできません

```
x = {1}
x = {2} // OK
x = {1.0} // Error
```

変数のスコープは常にグローバルです。

```
x = {1}
{
    x = {2}
}
x print # 2
```

### 演算

- 四則演算、比較演算、論理演算が使えます
- Stack型なので、優先順位はありません
- 演算結果はintegerとfloatのどちらかになります
- 関数表記で書くと次のようになります(擬似コード)

```
fn + (x: float|integer, y: float|integer) -> float|integer {
    match x, y {
        (float, integer) => x as float + y as float,
        (integer, float) => x as float + y as float,
        _ => x + y
    }
}
fn - (x: float|integer, y: float|integer) -> float|integer {
    match x, y {
        (float, integer) => x as float - y as float,
        (integer, float) => x as float - y as float,
        _ => x - y
    }
}
fn * (x: float|integer, y: float|integer) -> float|integer {
    match x, y {
        (float, integer) => x as float * y as float,
        (integer, float) => x as float * y as float,
        _ => x * y
    }
}
fn / (x: float|integer, y: float|integer) -> float {
    match x, y {
        (_, 0) => panic!("Division by zero"),
        _ => x / y
    }
}
fn // (x: integer, y: integer) -> integer {
    match x, y {
        (_, 0) => panic!("Division by zero"),
        _ => x // y
    }
}
fn > (x: float|integer, y: float|integer) -> integer {
    if x > y {
        1
    } else {
        0
    }
}
fn < (x: float|integer, y: float|integer) -> integer {
    if x < y {
        1
    } else {
        0
    }
}
fn == (x: float|integer, y: float|integer) -> integer {
    if x == y {
        1
    } else {
        0
    }
}
```

### 組み込み関数

組み込み関数は以下の通りです

- `print`: Stackのトップの値を出力します
- `print_ascii_str`: Stackのトップからlength個の値を取り出し、ASCII文字列に変換して出力します

### 制御構文

- `if`: Stackのトップから3つの値を取り出し、条件によって2つ目か3つ目の演算を行います。
- `loop`: Stackのトップから2つの値(条件とブロック)を取り出し、条件が真の間、ブロックを実行します。

## インタープリタの構成

- Scanner: 入力文字列をトークンに分割します
- Evaluator: トークンを用いて演算を行います

### Scanner

入力文字列を受け取り、トークンを返します

```
class Scanner(input: string) {
    /**
     * トークンを返します
     * @return array[Token]
     */
    public static function run(): array {}
}
```

Tokenの種類は以下の通りです

- `IntegerLiteral(integer)`: 整数
- `FloatLiteral(float)`: 浮動小数点数
- `Plus`: `+`
- `Minus`: `-`
- `Asterisk`: `*`
- `Slash`: `/`
- `DoubleSlash`: `//`
- `Declaration`: `=`
- `Equal`: `==`
- `GreaterThan`: `>`
- `LessThan`: `<`
- `Semicolon`: `;`
- `If`: `if` Keyword
- `Loop`: `loop` Keyword
- `Variable(String)`: 変数名
- `BuiltInFunction(String)`: 組み込み関数名
- `Block`: 演算の要素をまとめるブロック

Blockは次のような表現の時に使います。

```
if {x y <}
{
    1 2 +
    3 4 +
    *
}
{
    5 6 +
    7 8 +
    *
}
```

この場合、Scannerは次のようなトークンを返します。

```
Keyword("if")
Block([
    Variable("x")
    Variable("y")
    LessThan
])
RightParen
Block([
    IntegerLiteral(1)
    IntegerLiteral(2)
    Plus
    IntegerLiteral(3)
    IntegerLiteral(4)
    Plus
    Asterisk
])
Block([
    IntegerLiteral(5)
    IntegerLiteral(6)
    Plus
    IntegerLiteral(7)
    IntegerLiteral(8)
    Plus
    Asterisk
])
```

### Evaluator

トークンを受け取り、スタックを操作します

```
class Evaluator(tokens: array, stack: array, hashmap: HashMap) {
    public function run(): Self {}
}
```

- `IntegerLiteral(integer)`: Stackに値を積みます
- `FloatLiteral(float)`: Stackに値を積みます
- `Plus`: Stackから2つの値を取り出し、和を積みます
- `Minus`: Stackから2つの値を取り出し、差を積みます
- `Asterisk`: Stackから2つの値を取り出し、積を積みます
- `Slash`: Stackから2つの値を取り出し、商を積みます
- `DoubleSlash`: Stackから2つの値を取り出し、整数商を積みます
- `Equal`: `==`
- `GreaterThan`: `>`
- `LessThan`: `<`
- `Semicolon`:
    - Stackのトップの値を取り出し、表示します
    - Evaluatorの処理を中断し、Evaluatorのデータ型を返します
    - `Evaluator { stack: [1, 2, 3], hashmap: {x: 1, y: 2} }` の場合、`Evaluator { stack: [1, 2, 3], hashmap: {x: 1, y: 2} }` を返します
- `If`:
    - 前提 `IF Block1 Block2 Block3` とする
    - 後続するBlock1を評価する
        - 評価終了後Stackのトップの値が0以外の場合、Block2を評価する
        - 評価終了後Stackのトップの値が0の場合、Block3を評価する
- `loop`:
    - 前提 `loop Block` とする
    - 後続するBlockを評価する
        - 評価終了後Stackのトップの値が0の場合、処理を終了する
        - 評価終了後Stackのトップの値が0以外の場合、Block2を評価する
- `Variable(String)` + `Declaration` + ... + `Semicolon`:
    - HashMapに変数を登録します
    - 値は`Declaration`と`Semiconlon`の間の計算を行い、Stackのトップの値を取り出します
    - 自動で型を判別し、HashMapに登録します
- `Variable(String)`:
    - `Variable`の後ろに`Declaration`がない場合、HashMapから変数の中身を取り出し、Stackに積みます
    - HashMapから変数の中身を取り出し、Stackに積みます
- `BuiltInFunction(String)`: 組み込み関数を呼び出します
    - `print`: Stackのトップの値を出力します
    - `print_ascii_str`:
        - Stackのトップをpopしてlengthとして扱います
        - その後、length個の値を取り出し、ASCII文字列に変換して出力します
- `Block`: 演算の要素を取り出して実行します

## CLIの構成

- `main.php`: インタープリタのエントリーポイント
- `Cli.php`: コマンドラインインターフェースを提供
- `Router.php`: コマンドライン引数を解析
- `Controller.php`: Controllerの基底クラス
    - `InteractiveController.php`: インタラクティブモードを提供
    - `FileController.php`: ファイルモードを提供
    - `OneLinerController.php`: ワンライナーモードを提供
- `Interpreter/`
    - `Scanner.php`: 字句解析器
    - `Evaluator.php`: 評価器

