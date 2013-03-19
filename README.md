class.imageresize.php
================

class.imageresize.phpは、PHPのGDモジュールを利用した画像処理クラスです。

現在は以下の機能を実装しています。

* 縦横比を保った画像幅変更
* 縦横比を保った画像高変更
* 縦横比を保たない画像幅高変更
* 画像縮小/拡大(割)
* 画像の切り出し
* 画像の圧縮率の変更(JPEG/0-100,PNG/0-9,GIF/none)
* 画像フォーマットの変更(JPEG,PNG,GIF)
* 画像の別名保存
* 画像の上書き保存
* RAWデータの出力


---


### Sample

    require( "class.imageresize.php");
    $image = new ImageResize( "foo.jpg");
    $image->filename( "bar.png");
    $image->type( "png");
    $image->width( 800);
    $image->save();
    $image->filename( "baz.png");
    $image->ratio( 0.5);
    $image->quality( 50);
    $image->save();

1. "class.imageresize.php"を読み込む
2. "foo.jpg"の画像を加工用ファイルとして開く
3. 保存ファイル名として"bar.png"を指定
4. 保存フォーマットとして"png"を指定
5. 画像の幅を"800px"に指定
6. 画像を保存
7. 保存ファイル名として"baz.png"を指定
8. 画像を"0.5倍(50%)"に縮小(800x0.5=400px)
9. 画像の圧縮率
10. 画像を保存


---


## 使い方

画像のリサイズを行うにはImageResize()オブジェクトを作成し、そのオブジェクト内の各メソッドを呼び出すことでリサイズや画像の切り出しの調整を行います。オブジェクトのメソッドは、呼び出された順に処理されるのではなく、最終的に画像が出力される際にメソッドで設定された値に従ってリサイズされますので、何度でも再設定を行うことができます。


---


### 縦横比を保った画像幅変更

#### example

    $image->width( 500); //幅を500pxに設定
    $image->width(); //幅をリセット

画像の幅を変更します。width()単独で使用した場合は、画像の縦横比が維持されたまま縮小/拡大されます。画像の幅をオリジナルサイズに設定するには、引数を空の状態で呼び出してください。

#### 注意

引数は自然数の数値型で指定して下さい。10.1や"100"は例外が送出されます。


---


### 縦横比を保った画像高変更

#### example

    $image->height( 500); //高さを500pxに設定
    $image->height(); //高さをリセット

画像の高さを変更します。height()単独で使用した場合は、画像の縦横比が維持されたまま縮小/拡大されます。画像の幅をオリジナルサイズに設定するには、引数を空の状態で呼び出してください。

#### 注意

引数は自然数の数値型で指定して下さい。10.1や"100"は例外が出力されます。


---


### 縦横比を保たない画像幅高変更

#### example

    $image->width( 500);
    $image->height( 500);

画像の幅と高さを変更します。これは画像の縦横比を破壊した状態で縮小/拡大されます。


---


### 画像縮小/拡大(割)

#### example

    $image->ratio( 0.5); //幅と高さを0.5倍に縮小
    $image->ratio(); //縮小率をリセット

画像の幅と高さを変更します。width()とheight()が指定されていない場合、又はどちらかのみの指定の場合は、画像の縦横比が維持されたまま縮小/拡大されます。画像の幅をオリジナルサイズに設定するには、引数を空の状態で呼び出してください。

#### 注意

引数は0から1までの少数の数値型で指定して下さい。縮小率を計算した結果、1px以下の小数点が出た場合は四捨五入で丸められます。


---


### 画像の切り出し

#### example

    $image->area( 0.5); //幅と高さを0.5倍に縮小
    $image->area(); //縮小率をリセット

画像の幅と高さを変更します。width()とheight()が指定されていない場合、又はどちらかのみの指定の場合は、画像の縦横比が維持されたまま縮小/拡大されます。画像の幅をオリジナルサイズに設定するには、引数を空の状態で呼び出してください。

#### 注意

引数は0から1までの少数の数値型で指定して下さい。縮小率を計算した結果、1px以下の小数点が出た場合は四捨五入で丸められます。


---


### 圧縮率の変更

#### example

    $image->quality( 60); //圧縮率を60に設定
    $image->quality(); //圧縮率をリセット

画像の圧縮率を変更します(デフォルトは80です)。0が圧縮率が高くなりますが画質が悪く、100が圧縮率は低くなりますが、画質が向上します。

#### 注意

引数は0から100までの自然数の数値型で指定して下さい。quality( 101)やquality( "80")は例外が送出されます。
JPEGでは引数はそのままクオリティとして計算されますが、PNGでは10段階に変換/逆転されます。(100->0, 20->8)


---


### 画像フォーマットの変更(JPEG,PNG,GIF)

#### example

    $image->type( "png"); //画像フォーマットをPNGに変更します。
    $image->type(); //画像フォーマットをリセットします。

画像の圧縮率を変更します(デフォルトは80です)。0が圧縮率が高くなりますが画質が悪く、100が圧縮率は低くなりますが、画質が向上します。

#### 注意

引数は0から100までの自然数の数値型で指定して下さい。quality( 101)やquality( "80")は例外が送出されます。
JPEGでは引数はそのまま圧縮率として計算されますが、PNGでは10段階に変換/逆転されます。(100->0, 20->8)


---


### 画像の別名保存指定

#### example

    $image->filename( "foo.jpg"); //画像を"foo.jpg"で保存
    $image->filename(); //上書き保存戻す

画像を別名で保存します。ファイル名にはディレクトリのPATHを含むことができます。

#### 注意

指定したファイル名は"ファイル名"として利用されるのみで、画像フォーマットの変更には利用されません。別フォーマットで画像を保存する場合は、type()メソッドを利用して下さい。


---


### 画像の保存

#### example

    $image->save();

画像を保存します。filename()メソッドにてファイル名の指定がされている場合は別名保存、されていない場合は上書き保存されます。

#### 注意

元のファイルのバックアップはされていませんので、上書きにはご注意下さい。


---


### RAWデータの出力

#### example

    $image->save();

画像を保存します。filename()メソッドにてファイル名の指定がされている場合は別名保存、されていない場合は上書き保存されます。

#### 注意

元のファイルのバックアップはされていませんので、上書きにはご注意下さい。
