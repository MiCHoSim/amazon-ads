CREATE TABLE `user` (
                        `user_id` int(11) NOT NULL,
                        `email` varchar(100) COLLATE utf8mb4_bin NOT NULL,
                        `password` varchar(255) COLLATE utf8mb4_bin NOT NULL,
                        `registration_date_time` datetime NOT NULL DEFAULT current_timestamp(),
                        `login_date_time` datetime DEFAULT NULL,
                        `admin` int(11) DEFAULT NULL,
                        `programmer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `user` (`user_id`, `email`, `password`, `registration_date_time`, `login_date_time`, `admin`, `programmer`)
VALUES
    (1, 'simalmichal@gmail.com', '$2y$10$kgxT5IJNQ86JPiDW2MQqMOZFdg.4xt1BvEHQljiDDnBBmAQ1YojoG', '2022-10-06 20:33:12', '2023-02-10 09:42:15', 1, NULL);

ALTER TABLE `user`
    ADD PRIMARY KEY (`user_id`);

ALTER TABLE `user`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;