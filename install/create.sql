CREATE TABLE classes (
    cid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(20) NOT NULL,
    emblem VARCHAR(50) NOT NULL
);
--
CREATE TABLE users (
    uid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(256) NOT NULL,
    level INTEGER NOT NULL,
    character_name VARCHAR(25) NOT NULL,
    character_class INTEGER NOT NULL,
    gender VARCHAR(10) NOT NULL,
    avatar VARCHAR(100),
    money INTEGER,
    location INTEGER,
    new TINYINT,
    UNIQUE (email)
);
--
CREATE TABLE stats (
    id INTEGER,
    exp INTEGER,
    hp INTEGER,
    sp INTEGER,
    str INTEGER,
    vit INTEGER,
    dex INTEGER,
    agi INTEGER,
    cun INTEGER,
    wis INTEGER,
    UNIQUE (id)
);
--
CREATE TABLE skills (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(20) NOT NULL,
    name VARCHAR(20) NOT NULL,
    description VARCHAR(500) NOT NULL,
    cost INTEGER NOT NULL,
    damage INTEGER NOT NULL
);
--
CREATE TABLE has_skills (
    chara_id INTEGER NOT NULL,
    skill_id INTEGER NOT NULL
);
--
CREATE TABLE items (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(20) NOT NULL,
    name VARCHAR(20) NOT NULL,
    description VARCHAR(500) NOT NULL,
    cost INTEGER NOT NULL,
    avatar VARCHAR(50)
);
--
CREATE TABLE has_items (
    chara_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL
);
--
CREATE TABLE monsters (
    mid INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    description VARCHAR(500) NOT NULL,
    stat_id INTEGER NOT NULL,
    avatar VARCHAR(50)
);