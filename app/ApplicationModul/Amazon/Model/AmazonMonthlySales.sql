CREATE TABLE IF NOT EXISTS amazon_monthly_sales (
    amazon_monthly_sales_id int(11) NOT NULL,
    user_id int(11) DEFAULT NULL,
    profile_id VARCHAR(50) DEFAULT NULL,
    portfolio_id VARCHAR(50) DEFAULT NULL,
    select_date_id int(11) DEFAULT NULL,
    amazon_product_data_id int(11) DEFAULT NULL,
    asin varchar(255) DEFAULT NULL,
    sku varchar(255) DEFAULT NULL,
    page_views int(11) DEFAULT NULL,
    sessions int(11) DEFAULT NULL,
    unit_session decimal(8,4) DEFAULT NULL,
    acos decimal(17,13) DEFAULT NULL,
    tacos decimal(17,13) DEFAULT NULL,
    roi decimal(12,4) DEFAULT NULL,
    units_sold int(11) DEFAULT NULL,
    units_sold_from_ad_sales int(11) DEFAULT NULL,
    ad_sales decimal(17,13) DEFAULT NULL,
    refunds int(11) DEFAULT NULL,
    gross_revenue decimal(12,4) DEFAULT NULL,
    expenses decimal(10,4) DEFAULT NULL,
    ad_cost decimal(12,5) DEFAULT NULL,
    vat decimal(17,11) DEFAULT NULL,
    cogs decimal(8,3) DEFAULT NULL,
    net_profit decimal(8,3) DEFAULT NULL,
    adjusted_net_profit decimal(17,12) DEFAULT NULL,
    margin decimal(8,4) DEFAULT NULL,
    adjusted_net decimal(18,14) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_monthly_sales
    ADD PRIMARY KEY (amazon_monthly_sales_id),
    ADD KEY user_id (user_id),
    ADD KEY profile_id(profile_id),
    ADD KEY portfolio_id(portfolio_id),
    ADD KEY select_date_id(select_date_id),
    ADD KEY asin(asin),
    ADD KEY sku(sku),
    ADD KEY amazon_product_data_id(amazon_product_data_id),
        ADD UNIQUE KEY uniq(user_id,profile_id,portfolio_id,select_date_id,amazon_product_data_id);


ALTER TABLE amazon_monthly_sales
    MODIFY amazon_monthly_sales_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_monthly_sales
    ADD CONSTRAINT amazon_monthly_sales_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (user_id),
    ADD CONSTRAINT amazon_monthly_sales_ibfk_2 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id),
    ADD CONSTRAINT amazon_monthly_sales_ibfk_3 FOREIGN KEY (portfolio_id) REFERENCES amazon_ads_portfolio (portfolio_id),
    ADD CONSTRAINT amazon_monthly_sales_ibfk_4 FOREIGN KEY (select_date_id) REFERENCES select_date (select_date_id),
    ADD CONSTRAINT amazon_monthly_sales_ibfk_5 FOREIGN KEY (amazon_product_data_id) REFERENCES amazon_product_data (amazon_product_data_id);