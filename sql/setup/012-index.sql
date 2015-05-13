/*******************************************************************************
 * インデックス
 *******************************************************************************/

/*** DROP ***/

/*
DROP INDEX idex_user_01 ON t_user;
DROP INDEX idx_image_01 ON t_image;
*/

/*** CREATE ***/

CREATE UNIQUE INDEX idex_user_01 ON t_user (client_id);
CREATE UNIQUE INDEX idx_image_01 ON t_image (access_key);

