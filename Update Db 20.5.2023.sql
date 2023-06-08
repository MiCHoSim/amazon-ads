CREATE TABLE IF NOT EXISTS currency (
    currency_id int(11) NOT NULL,
    download_date date DEFAULT NULL,
    from_currency varchar(20) DEFAULT NULL,
    to_currency varchar(20) DEFAULT NULL,
    rate decimal(12,8) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE currency
    ADD PRIMARY KEY (currency_id),
        ADD UNIQUE KEY uniq(download_date,from_currency,to_currency, rate);

ALTER TABLE currency
    MODIFY currency_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE currency
    ADD CONSTRAINT currency_1 FOREIGN KEY (select_date_id) REFERENCES select_date (select_date_id);

INSERT INTO `currency` (`select_date_id`, `download_date`, `from_currency`, `to_currency`, `rate`)
    VALUES ('', '', '', '1'),
            ('2023-03-05', 'SEK', 'EUR', '0.08785'),
           ('2023-03-05', 'PLN', 'EUR', '0.2119'),
           ('2023-03-05', 'EUR', 'GBP', '0.89535'),
           ('2023-03-05', 'SEK', 'GBP', '0.07866'),
           ('2023-03-05', 'PLN', 'GBP', '0.18966'),
           ('2023-02-05', 'SEK', 'EUR', '0.08989'),
           ('2023-02-05', 'PLN', 'EUR', '0.21232'),
           ('2023-02-05', 'EUR', 'GBP', '0.88277'),
           ('2023-02-05', 'SEK', 'GBP', '0.07936'),
           ('2023-02-05', 'PLN', 'GBP', '0.18741'),
           ('2023-04-05', 'SEK', 'EUR', '0.08861'),
           ('2023-04-05', 'PLN', 'EUR', '0.21379'),
           ('2023-04-05', 'EUR', 'GBP', '0.87617'),
           ('2023-04-05', 'SEK', 'GBP', '0.07764'),
           ('2023-04-05', 'PLN', 'GBP', '0.18731');


ALTER TABLE `amazon_monthly_sales`
    ADD `currency_id` INT(11) NOT NULL AFTER `adjusted_net`,
    ADD CONSTRAINT currency_id
        FOREIGN KEY currency(currency_id) ON UPDATE CASCADE ON DELETE CASCADE;
