CREATE TABLE IF NOT EXISTS amazon_ads_theme_based_bid_recommendation (
    amazon_ads_theme_based_bid_recommendation_id int(11) NOT NULL,
    targeting_expression_array_type varchar(255) DEFAULT NULL,
    targeting_expression_array_value varchar(255) DEFAULT NULL,
    bid_values_array_low_array_suggested_bid double DEFAULT NULL,
    bid_values_array_median_array_suggested_bid double DEFAULT NULL,
    bid_values_array_high_array_suggested_bid double DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_theme_based_bid_recommendation
    ADD PRIMARY KEY (amazon_ads_theme_based_bid_recommendation_id);

ALTER TABLE amazon_ads_theme_based_bid_recommendation
    MODIFY amazon_ads_theme_based_bid_recommendation_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS amazon_ads_keyword_recommendations (
    amazon_ads_keyword_recommendations_id int(11) NOT NULL,
    keyword varchar(255) DEFAULT NULL,
    translation varchar(255) DEFAULT NULL,
    user_selected_keyword BOOLEAN DEFAULT NULL,
    search_term_impression_rank varchar(255) DEFAULT NULL,
    search_term_impression_share varchar(255) DEFAULT NULL,
    rec_id VARCHAR(50) DEFAULT NULL,
    bid_info_array_match_type varchar(255) DEFAULT NULL,
    bid_info_array_rank decimal(8,4) DEFAULT NULL,
    bid_info_array_bid double DEFAULT NULL,
    bid_info_array_suggested_bid_array_range_start double DEFAULT NULL,
    bid_info_array_suggested_bid_array_range_median double DEFAULT NULL,
    bid_info_array_suggested_bid_array_range_end double DEFAULT NULL,
    bid_info_array_suggested_bid_array_bid_rec_id VARCHAR(50) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

    ALTER TABLE amazon_ads_keyword_recommendations
    ADD PRIMARY KEY (amazon_ads_keyword_recommendations_id);

    ALTER TABLE amazon_ads_keyword_recommendations
    MODIFY amazon_ads_keyword_recommendations_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS amazon_ads_bid_recommendations_v2 (
    amazon_ads_bid_recommendations_v2_id int(11) NOT NULL,
    recommendations_array_suggested_bid_array_suggested double DEFAULT NULL,
    recommendations_array_suggested_bid_array_range_start double DEFAULT NULL,
    recommendations_array_suggested_bid_array_range_end double DEFAULT NULL,
    recommendations_array_expression_array_value varchar(255) DEFAULT NULL,
    recommendations_array_expression_array_type varchar(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_bid_recommendations_v2
    ADD PRIMARY KEY (amazon_ads_bid_recommendations_v2_id);

ALTER TABLE amazon_ads_bid_recommendations_v2
    MODIFY amazon_ads_bid_recommendations_v2_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;