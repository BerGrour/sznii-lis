<?php

use yii\db\Migration;

/**
 * Class m240516_134445_sample
 */
class m240516_134445_sample extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO sample (id, identificator, num, departament_id, batch_id, busy, losted_at) VALUES
                (1, "К23.01.01.1", 1, 4, 1, 1, null),
                (2, "К23.01.01.2", 2, 4, 1, 1, null),
                (3, "К23.01.01.3", 3, 4, 1, 1, null),
                (4, "К23.01.01.4", 4, 4, 1, 1, null),
                (5, "М23.01.01.1", 1, 5, 1, 1, null),
                (6, "М23.01.01.2", 2, 5, 1, 1, null),
                (7, "М23.01.01.3", 3, 5, 1, 1, null),
                (8, "М23.01.01.4", 4, 5, 1, 1, null),
                (9, "М23.01.01.5", 5, 5, 1, 1, null),
                (10, "М23.01.01.6", 6, 5, 1, 1, null),
                (11, "М23.01.01.7", 7, 5, 1, 1, null),
                (12, "М23.01.01.8", 8, 5, 1, 1, null),
                (13, "М23.01.01.9", 9, 5, 1, 1, null),
                (14, "М23.01.01.10", 10, 5, 1, 1, "2023-01-01 16:31:56"),

                (15, "Б23.01.02.1", 1, 6, 2, 1, null),
                (16, "Б23.01.02.2", 2, 6, 2, 1, null),
                (17, "Б23.01.02.3", 3, 6, 2, 1, null),
                (18, "Б23.01.02.4", 4, 6, 2, 1, null),
                (19, "Б23.01.02.5", 5, 6, 2, 1, null),
                (20, "Б23.01.02.6", 6, 6, 2, 1, null),
                (21, "Б23.01.02.7", 7, 6, 2, 1, null),
                (22, "Б23.01.02.8", 8, 6, 2, 1, null),
                (23, "Б23.01.02.9", 9, 6, 2, 1, null),
                (24, "Б23.01.02.10", 10, 6, 2, 1, null),
                (25, "Б23.01.02.11", 11, 6, 2, 1, null),
                (26, "Б23.01.02.12", 12, 6, 2, 1, null),
                (27, "Б23.01.02.13", 13, 6, 2, 1, null),
                (28, "Б23.01.02.14", 14, 6, 2, 1, null),
                (29, "Б23.01.02.15", 15, 6, 2, 1, null),
                (30, "Б23.01.02.16", 16, 6, 2, 1, null),
                (31, "Б23.01.02.17", 17, 6, 2, 1, null),
                (32, "Б23.01.02.18", 18, 6, 2, 1, null),
                (33, "Б23.01.02.19", 19, 6, 2, 1, null),
                (34, "Б23.01.02.20", 20, 6, 2, 1, null),
                (35, "Б23.01.02.21", 21, 6, 2, 1, null),
                (36, "Б23.01.02.22", 22, 6, 2, 1, null),
                (37, "Б23.01.02.23", 23, 6, 2, 1, null),
                (38, "Б23.01.02.24", 24, 6, 2, 1, null),
                (39, "Б23.01.02.25", 25, 6, 2, 1, null),
                (40, "Б23.01.02.26", 26, 6, 2, 1, null),
                (41, "Б23.01.02.27", 27, 6, 2, 1, null),
                (42, "Б23.01.02.28", 28, 6, 2, 1, null),
                (43, "Б23.01.02.29", 29, 6, 2, 1, null),
                (44, "Б23.01.02.30", 30, 6, 2, 1, null),


                (45, "М24.05.20.1", 1, 5, 3, 1, null),
                (46, "М24.05.20.2", 2, 5, 3, 1, null),
                (47, "М24.05.20.3", 3, 5, 3, 1, null),
                (48, "М24.05.20.4", 4, 5, 3, 1, null),
                (49, "М24.05.20.5", 5, 5, 3, 1, null),
                (50, "К24.05.20.1", 1, 4, 3, 1, null),
                (51, "К24.05.20.2", 2, 4, 3, 1, null),
                (52, "К24.05.20.3", 3, 4, 3, 1, null),
                (53, "К24.05.20.4", 4, 4, 3, 1, null),
                (54, "К24.05.20.5", 5, 4, 3, 1, null),

                (55, "К24.05.20.6", 6, 4, 4, 1, null),
                (56, "К24.05.20.7", 7, 4, 4, 1, null),
                (57, "К24.05.20.8", 8, 4, 4, 1, null),
                (58, "К24.05.20.9", 9, 4, 4, 1, null),
                (59, "К24.05.20.10", 10, 4, 4, 1, null),
                (60, "Д24.05.20.1", 1, 7, 4, 1, null),
                (61, "Д24.05.20.2", 2, 7, 4, 1, null),
                (62, "Д24.05.20.3", 3, 7, 4, 1, null),
                (63, "Д24.05.20.4", 4, 7, 4, 1, null),
                (64, "Д24.05.20.5", 5, 7, 4, 1, null),
                (65, "Д24.05.20.6", 6, 7, 4, 1, null),


                (66, "Д24.05.20.7", 7, 7, 5, 1, null),
                (67, "Д24.05.20.8", 8, 7, 5, 1, null),
                (68, "Д24.05.20.9", 9, 7, 5, 1, null),
                (69, "Д24.05.20.10", 10, 7, 5, 1, null),

                (70, "Б24.05.21.1", 1, 6, 5, 1, null),
                (71, "Б24.05.21.2", 2, 6, 5, 1, null),
                (72, "Б24.05.21.3", 3, 6, 5, 1, null),
                (73, "Б24.05.21.4", 4, 6, 5, 1, null),
                (74, "Б24.05.21.5", 5, 6, 5, 1, null),
                (75, "Б24.05.21.6", 6, 6, 5, 1, null),
                (76, "Б24.05.21.7", 7, 6, 5, 1, null),
                (77, "Б24.05.21.8", 8, 6, 5, 1, null),
                (78, "Б24.05.21.9", 9, 6, 5, 1, null),
                (79, "Б24.05.21.10", 10, 6, 5, 1, null),

                (80, "Д24.05.21.11", 11, 7, 6, 1, null),
                (81, "Д24.05.21.12", 12, 7, 6, 1, null),
                (82, "Д24.05.21.13", 13, 7, 6, 1, null),

                (83, "Д24.05.21.14", 14, 7, 7, 1, null),
                (84, "Д24.05.21.15", 15, 7, 7, 1, null),
                (85, "Д24.05.21.16", 16, 7, 7, 1, null),
                (86, "Д24.05.21.17", 17, 7, 7, 1, null),
                (87, "Д24.05.21.18", 18, 7, 7, 1, null),


                (88, "К24.05.22.11", 11, 4, 8, 1, null),
                (89, "К24.05.22.12", 12, 4, 8, 1, null),
                (90, "К24.05.22.13", 13, 4, 8, 1, null),
                (91, "К24.05.22.14", 14, 4, 8, 1, null),
                (92, "К24.05.22.15", 15, 4, 8, 1, null),
                (93, "К24.05.22.16", 16, 4, 8, 1, null),
                (94, "К24.05.22.17", 17, 4, 8, 1, null),
                (95, "М24.05.22.6", 6, 5, 8, 1, null),
                (96, "М24.05.22.7", 7, 5, 8, 1, null),
                (97, "М24.05.22.8", 8, 5, 8, 1, null),
                (98, "М24.05.22.9", 9, 5, 8, 1, null),
                (99, "М24.05.22.10", 10, 5, 8, 1, null),
                (100, "М24.05.22.11", 11, 5, 8, 1, null),
                (101, "М24.05.22.12", 12, 5, 8, 1, null),
                (102, "М24.05.22.13", 13, 5, 8, 1, null),

                (103, "Б24.05.22.11", 11, 6, 9, 1, null),
                (104, "Б24.05.22.12", 12, 6, 9, 1, null),
                (105, "Б24.05.22.13", 13, 6, 9, 1, null),
                (106, "Б24.05.22.14", 14, 6, 9, 1, null),
                (107, "Б24.05.22.15", 15, 6, 9, 1, null),
                (108, "Б24.05.22.16", 16, 6, 9, 1, null),


                (109, "К24.05.25.18", 18, 4, 10, 1, null),
                (110, "К24.05.25.19", 19, 4, 10, 1, null),
                (111, "К24.05.25.20", 20, 4, 10, 1, null),
                (112, "М24.05.25.14", 14, 5, 10, 1, null),
                (113, "М24.05.25.15", 15, 5, 10, 1, null),
                (114, "М24.05.25.16", 16, 5, 10, 1, null),
                (115, "М24.05.25.17", 17, 5, 10, 1, null),
                (116, "М24.05.25.18", 18, 5, 10, 1, null),
                (117, "М24.05.25.19", 19, 5, 10, 1, null),
                (118, "М24.05.25.20", 20, 5, 10, 1, null),
                (119, "М24.05.25.21", 21, 5, 10, 1, null),
                (120, "М24.05.25.22", 22, 5, 10, 1, null),
                (121, "М24.05.25.23", 23, 5, 10, 1, null),
                (122, "Б24.05.25.17", 17, 6, 10, 1, null),
                (123, "Б24.05.25.18", 18, 6, 10, 1, null),
                (124, "Б24.05.25.19", 19, 6, 10, 1, null),
                (125, "Б24.05.25.20", 20, 6, 10, 1, null),
                (126, "Б24.05.25.21", 21, 6, 10, 1, null),
                (127, "Б24.05.25.22", 22, 6, 10, 1, null),
                (128, "Б24.05.25.23", 23, 6, 10, 1, null),
                (129, "Б24.05.25.24", 24, 6, 10, 1, null),
                (130, "Б24.05.25.25", 25, 6, 10, 1, null),
                (131, "Б24.05.25.26", 26, 6, 10, 1, null),
                (132, "Б24.05.25.27", 27, 6, 10, 1, null),
                (133, "Б24.05.25.28", 28, 6, 10, 1, null),
                (134, "Б24.05.25.29", 29, 6, 10, 1, null),
                (135, "Б24.05.25.30", 30, 6, 10, 1, null),
                (136, "Б24.05.25.31", 31, 6, 10, 1, null),
                (137, "Б24.05.25.32", 32, 6, 10, 1, null),
                (138, "Б24.05.25.33", 33, 6, 10, 1, "2024-05-26 12:00:00"),
                (139, "Б24.05.25.34", 34, 6, 10, 1, "2024-05-26 12:01:00"),
                (140, "Б24.05.25.35", 35, 6, 10, 1, "2024-05-26 12:02:00"),
                (141, "Б24.05.25.36", 36, 6, 10, 1, "2024-05-26 12:03:00"),
                (142, "Д24.05.25.19", 19, 7, 10, 1, null),
                (143, "Д24.05.25.20", 20, 7, 10, 1, null),
                (144, "Д24.05.25.21", 21, 7, 10, 1, null),
                (145, "Д24.05.25.22", 22, 7, 10, 1, null),
                (146, "Д24.05.25.23", 23, 7, 10, 1, null);
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('sample');
    }

}
