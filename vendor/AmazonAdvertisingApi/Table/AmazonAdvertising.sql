CREATE TABLE `amazon_ads_region` (
                                     `amazon_ads_region_id` int(11) NOT NULL,
                                     `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
                                     `title` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
                                     `host` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
                                     `token_url` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
                                     `code_url` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `amazon_ads_region` (`amazon_ads_region_id`, `name`, `title`, `host`, `token_url`, `code_url`) VALUES
                                                                                                               (1, 'Europe', 'eu', 'advertising-api-eu.amazon.com', 'api.amazon.com/auth/o2/token', 'eu.account.amazon.com/ap/oa'),
                                                                                                               (2, 'North America', 'na', 'advertising-api.amazon.com', 'api.amazon.com/auth/o2/token', 'amazon.com/ap/oa');

ALTER TABLE `amazon_ads_region`
    ADD PRIMARY KEY (`amazon_ads_region_id`);

ALTER TABLE `amazon_ads_region`
    MODIFY `amazon_ads_region_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



CREATE TABLE `amazon_ads_config` (
                                     `amazon_ads_config_id` int(11) NOT NULL,
                                     `user_id` int(11) DEFAULT NULL,
                                     `client_id` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
                                     `client_secret` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
                                     `refresh_token` varchar(800) COLLATE utf8mb4_bin DEFAULT NULL,
                                     `amazon_ads_region_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `amazon_ads_config` (`amazon_ads_config_id`, `user_id`, `client_id`, `client_secret`, `refresh_token`, `amazon_ads_region_id`) VALUES
    (1, 1, 'amzn1.application-oa2-client.5b72d04b7a17446ca3178cefb5f99ff0', '7ef1b2fdc0e16bd2ace2a62a67037de596792221518f8312c06fdf026096658f', 'Atzr|IwEBIObvjy5EEkEYUwvlR2Nh75z2l8aQJ3zVLKSEk9v39QnNJVyp7b6L1BTcpHqcUxhxZ_-5hyLghb1To2CQzyb7eMVlT1XMMeraySu0WbbEeCZFiR-PyWa1Nl7cuQvTYizx2J-c-jurMsGkJBv13ai8N8EB8RFDHL0G-q4VfsL5N7qxzk36Dvpfri2Oy9p8dqFh36KulbOIJvaSQ9361SWd7a3hHDXtg7Nt3LWiBDUD5nnHeQB_642xuXp-NKGLUTU-H1zQ8hwvvB700_5wV1claErfVlKJ5lpxzv1OhFpkCowCY6jeNxhJavM3tWwfwC-CvNPQFPzsq6rj2-5R769vnDUAQzktngQqbIHtDi8H9yMeqi6cGkYMp1ItS1FDB8t3OwQHdDAKVc672yKoTRNVw29I7wArD8BScG2MvunAxAB4ifbvfWO6JoRW1h4Ows0Ibw_KrsLwvguKKFqfEnvjMCiJsMdyx04I8wlE00ntFRZFRw', 1);


ALTER TABLE `amazon_ads_config`
    ADD PRIMARY KEY (`amazon_ads_config_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `amazon_ads_region_id` (`amazon_ads_region_id`);

ALTER TABLE amazon_ads_config
    MODIFY amazon_ads_config_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `amazon_ads_config`
    ADD CONSTRAINT `amazon_ads_config_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `amazon_ads_config_ibfk_2` FOREIGN KEY (`amazon_ads_region_id`) REFERENCES `amazon_ads_region` (`amazon_ads_region_id`);



CREATE TABLE IF NOT EXISTS amazon_ads_profile (
    amazon_ads_profile_id int(11) NOT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    user_id int(11) DEFAULT NULL,
    country_code varchar(255) DEFAULT NULL,
    country_name varchar(255) DEFAULT NULL,
    currency_code varchar(255) DEFAULT NULL,
    daily_budget decimal(12,2) DEFAULT NULL,
    timezone varchar(255) DEFAULT NULL,
    account_info_array_marketplace_string_id VARCHAR(50) DEFAULT NULL,
    account_info_array_id VARCHAR(50) DEFAULT NULL,
    account_info_array_type varchar(255) DEFAULT NULL,
    account_info_array_name varchar(255) DEFAULT NULL,
    account_info_array_valid_payment_method int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_profile
    ADD PRIMARY KEY (profile_id),
    ADD KEY user_id (user_id),
    ADD KEY amazon_ads_profile_id (amazon_ads_profile_id);

ALTER TABLE amazon_ads_profile
    MODIFY amazon_ads_profile_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE amazon_ads_profile
    ADD CONSTRAINT amazon_ads_profile_1 FOREIGN KEY (user_id) REFERENCES user (user_id);



CREATE TABLE IF NOT EXISTS amazon_ads_portfolio (
    amazon_ads_portfolio_id int(11) NOT NULL,
    portfolio_id VARCHAR(50) DEFAULT NULL,
    user_id int(11) DEFAULT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    name varchar(255) DEFAULT NULL,
    in_budget decimal(8,4) DEFAULT NULL,
    state varchar(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_portfolio
    ADD PRIMARY KEY (portfolio_id),
    ADD KEY user_id (user_id),
    ADD KEY profile_id (profile_id),
    ADD KEY amazon_ads_portfolio_id (amazon_ads_portfolio_id);

ALTER TABLE amazon_ads_portfolio
    MODIFY amazon_ads_portfolio_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_ads_portfolio
    ADD CONSTRAINT amazon_ads_portfolio_1 FOREIGN KEY (user_id) REFERENCES user (user_id),
    ADD CONSTRAINT amazon_ads_portfolio_2 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id);



CREATE TABLE IF NOT EXISTS amazon_ads_campaign (
    amazon_ads_campaign_id int(11) NOT NULL,
    campaign_id VARCHAR(50) DEFAULT NULL,
    user_id int(11) DEFAULT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    portfolio_id VARCHAR(50) DEFAULT NULL,
    name varchar(255) DEFAULT NULL,
    start_date date DEFAULT NULL,
    state varchar(255) DEFAULT NULL,
    targeting_type varchar(255) DEFAULT NULL,
    budget_array_budget decimal(8,4) DEFAULT NULL,
    budget_array_budget_type varchar(255) DEFAULT NULL,
    dynamic_bidding_array_placement_bidding_array_placement varchar(255) DEFAULT NULL,
    dynamic_bidding_array_placement_bidding_array_percentage decimal(8,4) DEFAULT NULL,
    dynamic_bidding_array_strategy varchar(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_campaign
    ADD PRIMARY KEY (campaign_id),
    ADD KEY user_id (user_id),
    ADD KEY profile_id (profile_id),
    ADD KEY portfolio_id (portfolio_id),
    ADD KEY  amazon_ads_campaign_id (amazon_ads_campaign_id);

ALTER TABLE amazon_ads_campaign
    MODIFY amazon_ads_campaign_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_ads_campaign
    ADD CONSTRAINT amazon_ads_campaign_1 FOREIGN KEY (user_id) REFERENCES user (user_id),
    ADD CONSTRAINT amazon_ads_campaign_2 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id),
    ADD CONSTRAINT amazon_ads_campaign_3 FOREIGN KEY (portfolio_id) REFERENCES amazon_ads_portfolio (portfolio_id);



CREATE TABLE IF NOT EXISTS amazon_ads_ad_group (
    amazon_ads_ad_group_id int(11) NOT NULL,
    ad_group_id VARCHAR(50) DEFAULT NULL,
    user_id int(11) DEFAULT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    campaign_id VARCHAR(50) DEFAULT NULL,
    name varchar(255) DEFAULT NULL,
    default_bid decimal(8,4) DEFAULT NULL,
    state varchar(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_ad_group
    ADD PRIMARY KEY (ad_group_id),
    ADD KEY user_id (user_id),
    ADD KEY profile_id (profile_id),
    ADD KEY campaign_id (campaign_id),
    ADD KEY amazon_ads_ad_group_id (amazon_ads_ad_group_id);

ALTER TABLE amazon_ads_ad_group
    MODIFY amazon_ads_ad_group_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_ads_ad_group
    ADD CONSTRAINT amazon_ads_ad_group_1 FOREIGN KEY (user_id) REFERENCES user (user_id),
    ADD CONSTRAINT amazon_ads_ad_group_2 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id),
    ADD CONSTRAINT amazon_ads_ad_group_3 FOREIGN KEY (campaign_id) REFERENCES amazon_ads_campaign (campaign_id);



CREATE TABLE IF NOT EXISTS amazon_ads_keyword (
    amazon_ads_keyword_id int(11) NOT NULL,
    keyword_id VARCHAR(50) DEFAULT NULL,
    user_id int(11) DEFAULT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    campaign_id VARCHAR(50) DEFAULT NULL,
    ad_group_id VARCHAR(50) DEFAULT NULL,
    keyword_text varchar(255) DEFAULT NULL,
    match_type varchar(255) DEFAULT NULL,
    state varchar(255) DEFAULT NULL,
    bid decimal(8,4) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_keyword
    ADD PRIMARY KEY (keyword_id),
    ADD KEY user_id (user_id),
    ADD KEY profile_id (profile_id),
    ADD KEY campaign_id (campaign_id),
    ADD KEY ad_group_id (ad_group_id),
    ADD KEY amazon_ads_keyword_id (amazon_ads_keyword_id);

ALTER TABLE amazon_ads_keyword
    MODIFY amazon_ads_keyword_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_ads_keyword
    ADD CONSTRAINT amazon_ads_keyword_1 FOREIGN KEY (user_id) REFERENCES user (user_id),
    ADD CONSTRAINT amazon_ads_keyword_2 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id),
    ADD CONSTRAINT amazon_ads_keyword_3 FOREIGN KEY (campaign_id) REFERENCES amazon_ads_campaign (campaign_id),
    ADD CONSTRAINT amazon_ads_keyword_4 FOREIGN KEY (ad_group_id) REFERENCES amazon_ads_ad_group (ad_group_id);



CREATE TABLE IF NOT EXISTS amazon_ads_target (
    amazon_ads_target_id int(11) NOT NULL,
    target_id VARCHAR(50) DEFAULT NULL,
    user_id int(11) DEFAULT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    campaign_id VARCHAR(50) DEFAULT NULL,
    ad_group_id VARCHAR(50) DEFAULT NULL,
    expression_array_type varchar(255) DEFAULT NULL,
    expression_array_value varchar(255) DEFAULT NULL,
    expression_type varchar(255) DEFAULT NULL,
    resolved_expression_array_type varchar(255) DEFAULT NULL,
    resolved_expression_array_value varchar(255) DEFAULT NULL,
    state varchar(255) DEFAULT NULL,
    bid decimal(8,4) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_target
    ADD PRIMARY KEY (target_id),
    ADD KEY user_id (user_id),
    ADD KEY profile_id (profile_id),
    ADD KEY campaign_id (campaign_id),
    ADD KEY ad_group_id (ad_group_id),
    ADD KEY amazon_ads_target_id (amazon_ads_target_id);

ALTER TABLE amazon_ads_target
    MODIFY amazon_ads_target_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_ads_target
    ADD CONSTRAINT amazon_ads_target_1 FOREIGN KEY (user_id) REFERENCES user (user_id),
    ADD CONSTRAINT amazon_ads_target_2 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id),
    ADD CONSTRAINT amazon_ads_target_3 FOREIGN KEY (campaign_id) REFERENCES amazon_ads_campaign (campaign_id),
    ADD CONSTRAINT amazon_ads_target_4 FOREIGN KEY (ad_group_id) REFERENCES amazon_ads_ad_group (ad_group_id);




CREATE TABLE IF NOT EXISTS select_date (
    select_date_id int(11) NOT NULL,
    select_start_date date DEFAULT NULL,
    select_end_date date DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE select_date
    ADD PRIMARY KEY (select_date_id),
    ADD UNIQUE KEY (select_start_date,select_end_date);

ALTER TABLE select_date
    MODIFY select_date_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS time_unit (
    time_unit_id int(11) NOT NULL,
    name varchar(255) DEFAULT NULL,
    time_unit_name varchar(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE time_unit
    ADD PRIMARY KEY (time_unit_id);

ALTER TABLE time_unit
    MODIFY time_unit_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;