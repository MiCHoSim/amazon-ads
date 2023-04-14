CREATE TABLE `person_detail` (
                                 `person_detail_id` int(11) NOT NULL,
                                 `name` varchar(30) COLLATE utf8mb4_bin DEFAULT NULL,
                                 `last_name` varchar(30) COLLATE utf8mb4_bin DEFAULT NULL,
                                 `tel` varchar(20) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `person_detail` (`person_detail_id`, `name`, `last_name`, `tel`) VALUES
                                                                                 (1, 'Michal', 'Šimaľa', '0914278743');
ALTER TABLE `person_detail`
    ADD PRIMARY KEY (`person_detail_id`);

ALTER TABLE `person_detail`
    MODIFY `person_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;




CREATE TABLE `person` (
                          `person_id` int(11) NOT NULL,
                          `person_detail_id` int(11) NOT NULL,
                          `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `person` (`person_id`, `person_detail_id`, `user_id`) VALUES
                                                                      (1, 1, 1);
ALTER TABLE `person`
    ADD PRIMARY KEY (`person_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `person_detail_id` (`person_detail_id`);

ALTER TABLE `person`
    MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `person`
    ADD CONSTRAINT `person_ibfk_1` FOREIGN KEY (`person_detail_id`) REFERENCES `person_detail` (`person_detail_id`),
  ADD CONSTRAINT `person_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);