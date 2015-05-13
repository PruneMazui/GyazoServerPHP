/*******************************************************************************
 * 外部キー制約
 *******************************************************************************/

/*** DROP ***/

/*
ALTER TABLE t_image DROP FOREIGN KEY fk_image_01;
ALTER TABLE t_image_data DROP FOREIGN KEY fk_image_data_01;
*/

/*** CREATE ***/

ALTER TABLE t_image ADD CONSTRAINT fk_image_01 FOREIGN KEY (user_id) REFERENCES t_user (user_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE t_image_data ADD CONSTRAINT fk_image_data_01 FOREIGN KEY (image_id) REFERENCES t_image (image_id) ON UPDATE CASCADE ON DELETE CASCADE;

