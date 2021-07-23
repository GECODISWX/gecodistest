-- truncate products tables
TRUNCATE `ps_category_product`;
TRUNCATE `ps_product`;
TRUNCATE `ps_product_attachment`;
TRUNCATE `ps_product_attribute_combination`;
TRUNCATE `ps_product_carrier`;
TRUNCATE `ps_product_lang`;
TRUNCATE `ps_product_shop`;
TRUNCATE `ps_product_supplier`;
TRUNCATE `ps_product_tag`;
TRUNCATE `ps_feature_shop`;


SELECT cat_p.id_category, a.id_attribute_group FROM ps_category_product cat_p
LEFT JOIN ps_product_attribute p_a
ON p_a.id_product = cat_p.id_product
LEFT JOIN ps_product_attribute_combination p_a_c
ON p_a.id_product_attribute = p_a_c.id_product_attribute
LEFT JOIN ps_attribute a
ON a.id_attribute = p_a_c.id_attribute
WHERE a.id_attribute_group IS NOT null
GROUP BY cat_p.id_category, a.id_attribute_group


SELECT id_feature,name,count(name) FROM `ps_feature_lang` WHERE `id_lang` =1
GROUP BY name
HAVING count(name)>1
ORDER BY `count(name)`  DESC


UPDATE `ps_configuration` SET `value` = 'habitatetjardin.fr' WHERE `ps_configuration`.`id_configuration` = 233;
UPDATE `ps_configuration` SET `value` = 'habitatetjardin.fr' WHERE `ps_configuration`.`id_configuration` = 234;
UPDATE `ps_shop_url` SET `domain` = 'habitatetjardin.fr', `domain_ssl` = 'habitatetjardin.fr' WHERE `ps_shop_url`.`id_shop_url` = 1;
UPDATE `ps_shop_url` SET `domain` = 'habitatyjardin.es', `domain_ssl` = 'habitatyjardin.es' WHERE `ps_shop_url`.`id_shop_url` = 3;
