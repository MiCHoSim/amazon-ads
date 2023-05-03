UPDATE `amazon_ads_sp_targeting` SET amazon_ads_theme_based_bid_recommendation_id = NULL WHERE amazon_ads_theme_based_bid_recommendation_id is NOT NULL;

DELETE FROM amazon_ads_theme_based_bid_recommendation;

ALTER TABLE `amazon_ads_theme_based_bid_recommendation` AUTO_INCREMENT=1;

INSERT INTO `amazon_ads_theme_based_bid_recommendation` (`amazon_ads_theme_based_bid_recommendation_id`, `targeting_expression_array_type`, `targeting_expression_array_value`, `bid_values_array_low_array_suggested_bid`, `bid_values_array_median_array_suggested_bid`, `bid_values_array_high_array_suggested_bid`) VALUES (NULL, NULL, NULL, NULL, NULL, NULL);


