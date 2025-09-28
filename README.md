.MD 要換行，在句子最後加兩個空格

https://www.youtube.com/watch?v=fw5ObX8P6as  
安裝 laragon  
安裝 Beekeeper studio (database viewer)

把 PHP 例如 (C:\laragon\bin\php\php-8.4.5-nts-Win32-vs17-x64) 加到 path

# local server
入口檔要叫 index.php 才會自動抓  
`php -S localhost:8080` 跑內建 server  
或是把專案放到 C:\laragon\www 底下，然後連線 localhost/[資料夾名]


# PHP
純 PHP 檔(沒有 html)，開頭也要有 <?php，但可以不用寫 ?>  
php code 結尾必須有 ';'，例如 `echo "123";`  
'+' 字串要用 '.'，例如 `echo "1" . "2" . "3"`  
變數用 $ 定義，例如 `$name = "dodo"`  
template string 外面要用雙引號""包，不能用單引號''，例如 `echo "Hi, $name";` ，`echo 'Hi, $name';` 這樣會變純字串    
     
`<?php echo $message; ?>` 等於 `<?= $message; ?>`

變數後面要加字的時候，可以用 {} 把變數包起來，例如 $book 後面要馬上接 ™，但是寫 `$book™` 會錯誤，所以要寫 `{$book}™`，™ 才不會被當成變數名的一部分

## Array
```php
<?php foreach ($books as $book) {
    echo "<li>{$book}™</li>";
} ?>

<?php foreach ($books as $book) : ?>
    <li><?= $book ?></li>
<?php endforeach; ?>
```

## Template (View)
在 `index.php` 檔案最底下寫 `require "index.view.php";`，這樣 `index.view.php` 就能用 `index.php` 的變數  
連結要加 .php 寫 `/about.php` 不能寫 `/about`
連結要寫相對路徑 `./about.php`，相對的路徑是 `.php` 而不是 `.view.php`
做 router 的話則不用寫 `.php`

controller takes requests and reponds

print super variable
``` php
function dd($value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die() // 後面的東西就不會顯示
}
```

如果 .php 檔裡只有一個 class，通常檔名會用大寫開頭例如 Database.php  


!!! 永遠不要用 user data 串 query string，例如 query("SELECT * FROM posts WHERE id = $id")  
要用 ? 當 placeholder 然後傳參數 query("SELECT * FROM posts WHERE id = ?", [$id])  
或是 ':' 加參數名，例如 query("SELECT * FROM posts WHERE id = :id", [':id' => $id]);  
associative array 裡的參數名可加可不加 ':'


打包成 zip 上傳到 eb
```bash
TIMESTAMP=$(date +%Y%m%d-%H%M%S) && 7z a -tzip "archive_$TIMESTAMP.zip" . -x\!.git
```


## Apache 設定

以下設定可以連到 https://laracast-php.test/
```conf
# 定義網站根目錄 (ROOT)，這裡指向 public 資料夾，避免外部直接存取敏感檔案
define ROOT "C:/laragon/www/laracast-php/public"

# 定義網站的主要網域名稱 (SITE)
define SITE "laracast-php.test"


# ------------------ HTTP (Port 80) 設定 ------------------
<VirtualHost *:80> 
    # 網站根目錄，Apache 會從這裡開始尋找要回應的檔案
    DocumentRoot "${ROOT}"

    # 主要的網域名稱
    ServerName ${SITE}

    # 可使用的別名網域，例如：sub.laracast-php.test
    ServerAlias *.${SITE}

    # 指定目錄的存取規則
    <Directory "${ROOT}">
        # 關閉目錄瀏覽，避免使用者看到檔案清單
        Options -Indexes

        # 允許使用 .htaccess 覆寫 Apache 設定
        AllowOverride All

        # 開放所有人存取這個目錄
        Require all granted
    </Directory>
	
    # 啟用網址改寫規則 (Rewrite)
    <IfModule mod_rewrite.c>
        RewriteEngine On

        # 如果請求的目標不是實體檔案
        RewriteCond %{REQUEST_FILENAME} !-f
        # 且也不是實體資料夾
        RewriteCond %{REQUEST_FILENAME} !-d

        # 就把請求導向 index.php，交給 PHP 路由器處理
        RewriteRule ^ index.php [L]
    </IfModule>
</VirtualHost>


# ------------------ HTTPS (Port 443) 設定 ------------------
<VirtualHost *:443>
    # 網站根目錄
    DocumentRoot "${ROOT}"

    # 主要的網域名稱
    ServerName ${SITE}

    # 可使用的別名網域
    ServerAlias *.${SITE}

    # 指定目錄的存取規則
    <Directory "${ROOT}">
        Options -Indexes
        AllowOverride All
        Require all granted
		
        # 同樣的網址改寫規則，確保 HTTPS 也能使用 index.php 作為路由
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^ index.php [L]
        </IfModule>
    </Directory>

    # 啟用 SSL
    SSLEngine on

    # SSL 憑證檔案
    SSLCertificateFile      C:/laragon/etc/ssl/laragon.crt

    # SSL 金鑰檔案
    SSLCertificateKeyFile   C:/laragon/etc/ssl/laragon.key
</VirtualHost>

```