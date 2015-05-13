/*******************************************************************************
 * テーブル定義
 *******************************************************************************/

/*** DROP ***/

SET @@session.SQL_NOTES = 0;
SET @@session.FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS t_user;
DROP TABLE IF EXISTS t_image;
DROP TABLE IF EXISTS t_image_data;
SET @@session.SQL_NOTES = DEFAULT;
SET @@session.FOREIGN_KEY_CHECKS = DEFAULT;

/*** CREATE ***/

CREATE TABLE t_user (
  user_id SMALLINT NOT NULL auto_increment COMMENT 'ユーザー',
  client_id CHAR (40) NOT NULL DEFAULT '' COMMENT 'クライアントID',
  ip_addr VARCHAR (128) NOT NULL DEFAULT '' COMMENT 'IPアドレス',
  insert_date TIMESTAMP NULL DEFAULT NULL COMMENT '登録日時',
  update_date TIMESTAMP NULL DEFAULT NULL COMMENT '更新日時',
  
  PRIMARY KEY ( user_id )
) COMMENT='ユーザー';

CREATE TABLE t_image (
  image_id INTEGER NOT NULL auto_increment COMMENT '画像連番',
  access_key CHAR (40) NOT NULL DEFAULT '' COMMENT 'アクセスキー',
  size INTEGER NOT NULL DEFAULT 0 COMMENT 'サイズ',
  width SMALLINT NOT NULL DEFAULT 0 COMMENT '横幅',
  height SMALLINT NOT NULL DEFAULT 0 COMMENT '高さ',
  user_id SMALLINT NOT NULL DEFAULT 0 COMMENT 'ユーザーID',
  insert_date TIMESTAMP NULL DEFAULT NULL COMMENT '登録日時',
  
  PRIMARY KEY ( image_id )
) COMMENT='画像';

CREATE TABLE t_image_data (
  image_id INTEGER NOT NULL COMMENT '画像連番',
  data LONGBLOB NOT NULL COMMENT '画像バイナリ',
  
  PRIMARY KEY ( image_id )
) COMMENT='画像データ';

