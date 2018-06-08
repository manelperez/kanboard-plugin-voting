<?php
namespace Kanboard\Plugin\Voting\Schema;

const VERSION = 1;

function version_1($pdo)
{
    $pdo->exec("
        CREATE TABLE voting (
            id INT NOT NULL AUTO_INCREMENT,
			evaluated_user_id INT DEFAULT '0',
            title VARCHAR(255),
            date_creation INT,
            date_completed INT,
            score INT,
            PRIMARY KEY (id),
            FOREIGN KEY(evaluated_user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");
    $pdo->exec("
        CREATE TABLE activities_evaluation (
            id INT NOT NULL AUTO_INCREMENT,
			voting_id INT DEFAULT '0',
			user_id INT DEFAULT '0',
			date INT,
            importance INT,
			accuracy INT,
			time INT,
			initiative INT,
			collaboration INT,
            PRIMARY KEY (id),
            FOREIGN KEY(voting_id) REFERENCES voting(id) ON DELETE CASCADE,
			FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");
    $pdo->exec("ALTER TABLE users ADD COLUMN weight INT DEFAULT NULL");
}