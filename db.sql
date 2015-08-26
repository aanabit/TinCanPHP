CREATE TABLE questions (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(10) NOT NULL DEFAULT '',
    before_text TEXT DEFAULT '',
    after_text TEXT DEFAULT '',
    values_text TEXT NOT NULL
);