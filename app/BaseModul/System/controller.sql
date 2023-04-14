CREATE TABLE `controller` (
                              `controller_id` int(11) NOT NULL,
                              `title` varchar(255) COLLATE utf8mb4_bin NOT NULL,
                              `url` varchar(255) COLLATE utf8mb4_bin NOT NULL,
                              `description` varchar(255) COLLATE utf8mb4_bin NOT NULL,
                              `controller_path` varchar(255) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `controller` (`controller_id`, `title`, `url`, `description`, `controller_path`) VALUES
(1, 'Error', 'error', 'Error page', 'BaseModul\\Controller\\Error'),
(2, 'Article', 'article', 'Display of Articles', 'ArticleModul\\Controller\\Article'),
(3, 'Contact', 'contact', 'Contact. Contact Information', 'BaseModul\\Controller\\Contact'),
(4, 'Account', 'account', 'User account', 'AccountModul\\Controller\\Account'),
(5, 'App management', 'app-management', 'Setting up the application for amazon advertising', 'ApplicationModul\\AppManagement\\Controller\\AppManagement'),
(6, 'Amazon Advertising', 'amazon-ads', 'Report and Statistics for Amazon Advertising', 'ApplicationModul\\AmazonAds\\Controller\\AmazonAds');

ALTER TABLE `controller`
    ADD PRIMARY KEY (`controller_id`),
  ADD UNIQUE KEY `url` (`url`);

ALTER TABLE `controller`
    MODIFY `controller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;