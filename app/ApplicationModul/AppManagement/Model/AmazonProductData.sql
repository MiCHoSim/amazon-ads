CREATE TABLE IF NOT EXISTS amazon_product_data (
    amazon_product_data_id int(11) NOT NULL,
    addition_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    profile_id VARCHAR(50) DEFAULT NULL,
    sku varchar(255) DEFAULT NULL,
    fba_fees decimal(8,4) DEFAULT NULL,
    landing_cost decimal(8,4) DEFAULT NULL,
    break_even decimal(8,4) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE amazon_product_data
    ADD PRIMARY KEY (amazon_product_data_id),
    ADD KEY addition_date(addition_date),
    ADD KEY profile_id(profile_id);

ALTER TABLE amazon_product_data
    MODIFY amazon_product_data_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE amazon_product_data
    ADD CONSTRAINT amazon_product_data_ibfk_1 FOREIGN KEY (profile_id) REFERENCES amazon_ads_profile (profile_id);