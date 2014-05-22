UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Temperature taken and recorded' WHERE `indicator_id`='1';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Weight taken and recorded' WHERE `indicator_id`='2';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Height/Length taken and recorded' WHERE `indicator_id`='3';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='MUAC taken and recorded' WHERE `indicator_id`='19';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Breath counts taken and recorded' WHERE `indicator_id`='25';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Temperature taken and recorded' WHERE `indicator_id`='30';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Breath counts taken and recorded' WHERE `indicator_id`='50';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Temperature taken and recorded' WHERE `indicator_id`='55';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='Developmental milestones' WHERE `indicator_id`='37';
UPDATE `mnh_rest`.`indicators` SET `indicator_name`='ORS given approximately according to plan' WHERE `indicator_id`='47';
INSERT INTO `mnh_rest`.`indicators` (`indicator_name`, `indicator_code`, `indicator_for`) VALUES ('ICCM Tools', 'CHI50', 'ror');
INSERT INTO `mnh_rest`.`questions` (`question_code`, `question_name`, `question_for`) VALUES ('QHC18', 'What is the relationship of the caregiver to the child?', 'int');
