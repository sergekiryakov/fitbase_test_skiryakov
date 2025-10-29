CREATE DATABASE IF NOT EXISTS `yii2app_test`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'yii2user'@'%' IDENTIFIED BY 'yii2pass';

GRANT ALL PRIVILEGES ON `yii2app_test`.* TO 'yii2user'@'%';

FLUSH PRIVILEGES;