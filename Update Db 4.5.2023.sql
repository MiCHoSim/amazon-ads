-- toto spustit
ALTER TABLE amazon_ads_theme_based_bid_recommendation
    ADD `amazon_ads_sp_targeting_id` int(11) NULL AFTER `amazon_ads_theme_based_bid_recommendation_id`,
    ADD UNIQUE KEY `amazon_ads_sp_targeting_id` (`amazon_ads_sp_targeting_id`),
    ADD CONSTRAINT amazon_ads_theme_based_bid_recommendation_1
        FOREIGN KEY (amazon_ads_sp_targeting_id)
            REFERENCES amazon_ads_sp_targeting (amazon_ads_sp_targeting_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE amazon_ads_keyword_recommendations
    ADD `amazon_ads_sp_targeting_id` int(11) NULL AFTER `amazon_ads_keyword_recommendations_id`,
    ADD UNIQUE KEY `amazon_ads_sp_targeting_id` (`amazon_ads_sp_targeting_id`),
    ADD CONSTRAINT amazon_ads_keyword_recommendations_1
        FOREIGN KEY (amazon_ads_sp_targeting_id)
            REFERENCES amazon_ads_sp_targeting (amazon_ads_sp_targeting_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE amazon_ads_bid_recommendations_v2
    ADD `amazon_ads_sp_targeting_id` int(11) NULL AFTER `amazon_ads_bid_recommendations_v2_id`,
    ADD UNIQUE KEY `amazon_ads_sp_targeting_id` (`amazon_ads_sp_targeting_id`),
    ADD CONSTRAINT amazon_ads_bid_recommendations_v2_1
        FOREIGN KEY (amazon_ads_sp_targeting_id)
            REFERENCES amazon_ads_sp_targeting (amazon_ads_sp_targeting_id) ON UPDATE CASCADE ON DELETE CASCADE;


-- spustit SQL na prehodenie:
--amazon-ads/download/4334717172873825

-- spustit ostatne
ALTER TABLE `amazon_ads_sp_targeting` ADD `bid` TINYINT(1) NOT NULL DEFAULT '0' AFTER `amazon_ads_sp_targeting_id`;

ALTER TABLE amazon_ads_sp_targeting DROP FOREIGN KEY amazon_ads_sp_targeting_10;
ALTER TABLE amazon_ads_sp_targeting DROP FOREIGN KEY amazon_ads_sp_targeting_11;
ALTER TABLE amazon_ads_sp_targeting DROP FOREIGN KEY amazon_ads_sp_targeting_12;

ALTER TABLE amazon_ads_sp_targeting DROP amazon_ads_theme_based_bid_recommendation_id;
ALTER TABLE amazon_ads_sp_targeting DROP `amazon_ads_keyword_recommendations_id`;
ALTER TABLE amazon_ads_sp_targeting DROP amazon_ads_bid_recommendations_v2_id;

DELETE FROM `amazon_ads_theme_based_bid_recommendation` WHERE `amazon_ads_sp_targeting_id` IS NULL;
DELETE FROM `amazon_ads_keyword_recommendations` WHERE `amazon_ads_sp_targeting_id` IS NULL;
DELETE FROM `amazon_ads_bid_recommendations_v2` WHERE `amazon_ads_sp_targeting_id` IS NULL;

ALTER TABLE `amazon_ads_theme_based_bid_recommendation` CHANGE `amazon_ads_sp_targeting_id` `amazon_ads_sp_targeting_id` INT(11) NOT NULL;
ALTER TABLE `amazon_ads_keyword_recommendations` CHANGE `amazon_ads_sp_targeting_id` `amazon_ads_sp_targeting_id` INT(11) NOT NULL;
ALTER TABLE `amazon_ads_bid_recommendations_v2` CHANGE `amazon_ads_sp_targeting_id` `amazon_ads_sp_targeting_id` INT(11) NOT NULL;