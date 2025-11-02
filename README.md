# COACHTECHフリマアプリ  

## 開発環境  
### Dockerビルド  
1.git clone https://github.com/marionnu/coachtech-freama.git  
2.docker-compose up -d --build  
＊OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。  
### Laravel環境構築  
1.docker-compose exec php bash  
2.composer install  
3.env.exampleファイルから.envを作成し、環境変数を変更 cp .env.example .env  
4.php artisan key:generate  
5.php artisan migrate --seed  
6.php artisan storage:link
## 使用技術  
・PHP 8.0  
・Laravel 8.75  
・MySQL 8.0  

## ER図  
![alt text](img/ER.png)

## URL  
・環境開発  
アプリ ： http://localhost  
ログイン画面：  


・phpMyAdmin ： http://localhost:8080/  

## 本アプリはLaravelを使用し、ユーザー認証機能とフリマアプリ機能を備えています。  
以下に動作画面を示します。  

### 1. 商品一覧画面  
![商品一覧画面](img/1.png)  

---

### 2. ログイン画面  
![ログイン画面](img/2.png)  
※ご自身で登録後ログインしてください。  

---

### 3. 新規登録画面  
![新規登録画面](img/3.png)  

---

### 4. メール認証画面  
![メール認証画面](img/4.png)  

---

### 5. 商品詳細画面  
![商品詳細画面](img/5.png)  

---

### 6. 出品画面  
![出品画面](img/6.png)  

---

### 7. 出品後画面  
![出品後画面](img/7.png)  

---

### 8. マイリスト画面  
![マイリスト画面](img/8.png)  

---

### 9. コメント投稿画面  
![コメント投稿画面](img/9.png)  

---

### 10. 購入画面  
![購入画面](img/10.png)  

---

### 11. 送付先住所変更画面  
![送付先住所変更画面](img/11.png)  

---

### 12. 購入成功画面  
![購入成功画面](img/12.png)  

---

### 13. マイページ画面  
![マイページ画面](img/13.png)  

---

### 14. プロフィール編集画面  
![プロフィール編集画面](img/14.png)  

---

## GitHubリポジトリ  
[こちらをクリックしてコードと動作を確認できます](https://github.com/marionnu/coachtech-freama.git)
