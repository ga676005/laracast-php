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
