# COACHTECHフリマアプリ  

## 開発環境  
### Dockerビルド  
1.git clone リンク  
2.docker-compose up -d --build  
＊MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。  
### Laravel環境構築  
1.docker-compose exec php bash  
2.composer install  
3.env.exampleファイルから.envを作成し、環境変数を変更  
4.php artisan key:generate  
5.php artisan migrate  
6.php artisan db:seed  
## 使用技術  
・PHP 8.0  
・Laravel 8.75  
・MySQL 8.0  

## ER図  
![alt text](img/.png)

## URL  
・環境開発
ログイン画面：

・phpMyAdmin ： http://localhost:8080/  

## 本アプリはLaravelを使用し、ユーザー認証機能とフリマアプリ機能を備えています。  
以下に動作画面を示します。  

### 1. ログイン画面  
![ログイン画面](img/)  
※ご自身で登録後ログインしてください。

---

### 2. 新規登録画面  





## GitHubリポジトリ  
[こちらをクリックしてコードと動作を確認できます](https://github.com/marionnu/coachtech-freama.git)
