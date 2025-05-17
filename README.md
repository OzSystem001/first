# 大容量ファイルアップロードシステム

このシステムはHTMLとPHPを使用して、最大10GBまでのファイルを複数同時にアップロードできるシステムです。アップロード状況はプログレスバーでリアルタイムに確認できます。サーバーの空き容量確認機能と初期設定機能も備えています。

## 特徴

- 最大10GBまでのファイルアップロード（設定ファイルで変更可能）
- 複数ファイルの同時アップロード
- リアルタイムプログレスバー
- 設定ファイルによる最大容量と保存先フォルダの指定
- 許可するファイル形式の制限機能
- サーバーの空き容量確認機能
- 初期設定画面（setup.html）

## システム要件

- PHP 7.0以上
- Webサーバー（Apache, Nginx, PHPビルトインサーバーなど）
- 十分なディスク容量

## インストール方法

1. ファイルをWebサーバーのドキュメントルートにコピーします
2. ブラウザで `setup.html` にアクセスして初期設定を行います
3. 設定が完了すると自動的にメインページにリダイレクトされます

## 初期設定（setup.html）

初期設定画面では以下の項目を設定できます：

- **最大アップロードサイズ**: 1GB〜100GBの間で指定（デフォルト: 10GB）
- **アップロードディレクトリ**: ファイルの保存先ディレクトリ（絶対パスで指定）
- **許可するファイル拡張子**: カンマ区切りで指定（例: jpg, png, pdf, zip）
  - 空の場合はすべてのファイルタイプが許可されます

設定はサーバー側の `config/settings.php` ファイルに保存されます。

## 設定ファイル

`config/settings.php`ファイルで以下の設定を変更できます：

- `$maxUploadSize`: 最大アップロードサイズ（バイト単位）
- `$uploadDirectory`: アップロードファイルの保存先ディレクトリ
- `$allowedFileExtensions`: 許可するファイル拡張子の配列
- `$maxExecutionTime`: PHPの最大実行時間
- `$maxInputTime`: PHPの最大入力時間
- `$postMaxSize`: POSTリクエストの最大サイズ
- `$memoryLimit`: PHPのメモリ制限

## サーバーコマンド

### PHPビルトインサーバーで実行

```bash
cd /path/to/file_upload_system
php -S localhost:8000
```

ブラウザで http://localhost:8000/setup.html にアクセスして初期設定を行い、その後システムを使用できます。

### Apache/Nginxでの実行

ファイルをApacheまたはNginxのドキュメントルートにコピーし、Webサーバーの設定で以下の項目を変更してください：

#### Apache（.htaccessまたはhttpd.conf）

```
php_value upload_max_filesize 10G
php_value post_max_size 11G
php_value memory_limit 1G
php_value max_execution_time 3600
php_value max_input_time 3600
```

#### Nginx（php.iniまたはnginx.conf）

```
client_max_body_size 11G;
```

また、php.iniファイルで以下の設定を変更してください：

```
upload_max_filesize = 10G
post_max_size = 11G
memory_limit = 1G
max_execution_time = 3600
max_input_time = 3600
```

## 使用方法

1. ブラウザでシステムにアクセスします（初回は自動的に初期設定画面にリダイレクトされます）
2. 初期設定を行います（setup.html）
3. メイン画面で「ファイルを選択」ボタンをクリックして、アップロードするファイルを選択します（複数選択可能）
4. 「アップロード開始」ボタンをクリックしてアップロードを開始します
5. プログレスバーでアップロード状況を確認します
6. サーバーの空き容量情報は画面上部に表示されます
