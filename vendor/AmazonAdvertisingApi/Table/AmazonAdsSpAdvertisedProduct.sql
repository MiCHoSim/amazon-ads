CREATE TABLE IF NOT EXISTS amazon_ads_sp_advertised_product (
    amazon_ads_sp_advertised_product_id int(11) NOT NULL,
    select_date_id int(11) DEFAULT NULL,
    time_unit_id int(11) DEFAULT NULL,
    user_id int(11) DEFAULT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    campaign_name VARCHAR(255) DEFAULT NULL,
    campaign_id VARCHAR(50) DEFAULT NULL,
    ad_group_name varchar(255) DEFAULT NULL,
    ad_group_id VARCHAR(50) DEFAULT NULL,
    ad_id int(11) DEFAULT NULL,
    portfolio_id VARCHAR(50) DEFAULT NULL,
    impressions int(11) DEFAULT NULL,
    clicks int(11) DEFAULT NULL,
    cost_per_click decimal(8,4) DEFAULT NULL,
    click_through_rate decimal(8,4) DEFAULT NULL,
    cost decimal(8,4) DEFAULT NULL,
    spend decimal(8,4) DEFAULT NULL,
    campaign_budget_currency_code varchar(255) DEFAULT NULL,
    campaign_budget_amount decimal(8,4) DEFAULT NULL,
    campaign_budget_type varchar(255) DEFAULT NULL,
    campaign_status varchar(255) DEFAULT NULL,
    advertised_asin varchar(255) DEFAULT NULL,
    advertised_sku varchar(255) DEFAULT NULL,
    purchases1d int(11) DEFAULT NULL,
    purchases7d int(11) DEFAULT NULL,
    purchases14d int(11) DEFAULT NULL,
    purchases30d int(11) DEFAULT NULL,
    purchases_same_sku1d int(11) DEFAULT NULL,
    purchases_same_sku7d int(11) DEFAULT NULL,
    purchases_same_sku14d int(11) DEFAULT NULL,
    purchases_same_sku30d int(11) DEFAULT NULL,
    units_sold_clicks1d int(11) DEFAULT NULL,
    units_sold_clicks7d int(11) DEFAULT NULL,
    units_sold_clicks14d int(11) DEFAULT NULL,
    units_sold_clicks30d int(11) DEFAULT NULL,
    sales1d decimal(8,4) DEFAULT NULL,
    sales7d decimal(8,4) DEFAULT NULL,
    sales14d decimal(8,4) DEFAULT NULL,
    sales30d decimal(8,4) DEFAULT NULL,
    attributed_sales_same_sku1d double DEFAULT NULL,
    attributed_sales_same_sku7d double DEFAULT NULL,
    attributed_sales_same_sku14d double DEFAULT NULL,
    attributed_sales_same_sku30d double DEFAULT NULL,
    sales_other_sku7d int(11) DEFAULT NULL,
    units_sold_same_sku1d int(11) DEFAULT NULL,
    units_sold_same_sku7d int(11) DEFAULT NULL,
    units_sold_same_sku14d int(11) DEFAULT NULL,
    units_sold_same_sku30d int(11) DEFAULT NULL,
    units_sold_other_sku7d int(11) DEFAULT NULL,
    kindle_edition_normalized_pages_read14d int(11) DEFAULT NULL,
    kindle_edition_normalized_pages_royalties14d decimal(8,4) DEFAULT NULL,
    acos_clicks7d double DEFAULT NULL,
    acos_clicks14d double DEFAULT NULL,
    roas_clicks7d decimal(10,6) DEFAULT NULL,
    roas_clicks14d decimal(10,6) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_ads_sp_advertised_product
    ADD PRIMARY KEY (amazon_ads_sp_advertised_product_id),
    ADD KEY select_date_id(select_date_id),
    ADD KEY time_unit_id(time_unit_id),
    ADD KEY user_id(user_id),
    ADD KEY profile_id(profile_id),
        ADD KEY portfolio_id(portfolio_id),
        ADD KEY campaign_id(campaign_id),
        ADD KEY ad_group_id(ad_group_id),
    ADD KEY ad_id(ad_id),
    ADD KEY advertised_sku(advertised_sku),
    ADD UNIQUE KEY uniq(select_date_id,time_unit_id,user_id,profile_id,portfolio_id,campaign_id,ad_group_id,ad_id,advertised_sku);

ALTER TABLE amazon_ads_sp_advertised_product
    MODIFY amazon_ads_sp_advertised_product_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_ads_sp_advertised_product
    ADD CONSTRAINT amazon_ads_sp_advertised_product_1 FOREIGN KEY (user_id) REFERENCES user (user_id),
    ADD CONSTRAINT amazon_ads_sp_advertised_product_2 FOREIGN KEY (select_date_id) REFERENCES select_date (select_date_id),
    ADD CONSTRAINT amazon_ads_sp_advertised_product_3 FOREIGN KEY (time_unit_id) REFERENCES time_unit (time_unit_id),
    ADD CONSTRAINT amazon_ads_sp_advertised_product_4 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id),
    ADD CONSTRAINT amazon_ads_sp_advertised_product_5 FOREIGN KEY (portfolio_id) REFERENCES amazon_ads_portfolio (portfolio_id),
    ADD CONSTRAINT amazon_ads_sp_advertised_product_6 FOREIGN KEY (campaign_id) REFERENCES amazon_ads_campaign (campaign_id),
    ADD CONSTRAINT amazon_ads_sp_advertised_product_7 FOREIGN KEY (ad_group_id) REFERENCES amazon_ads_ad_group (ad_group_id);