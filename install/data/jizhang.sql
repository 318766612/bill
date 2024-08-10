CREATE TABLE `jz_user`
(
    uid      INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(15) NOT NULL,
    password VARCHAR(35) NOT NULL,
    email    VARCHAR(20) NOT NULL,
    Isallow  SMALLINT( 2 ) NOT NULL DEFAULT ( 0 ),
    Isadmin  SMALLINT( 2 ) DEFAULT ( 0 ),
    add_time  INT( 11 ) NOT NULL,
    update_time    INT( 11 ) NOT NULL,
    salt     VARCHAR(35) NOT NULL
);
CREATE TABLE `jz_bank`
(
    bank_id       INTEGER PRIMARY KEY AUTOINCREMENT,
    bank_name     VARCHAR(50)    NOT NULL,
    account  VARCHAR(50)    NOT NULL,
    balance_money DECIMAL(10, 2) NOT NULL,
    add_time      INT( 11 ) NOT NULL,
    update_time   INT( 11 ) NOT NULL,
    uid       INT( 8 ) NOT NULL
);
CREATE TABLE `jz_category`
(
    category_id   INTEGER PRIMARY KEY AUTOINCREMENT,
    category_name VARCHAR(20) NOT NULL,
    type INT( 1 ) NOT NULL,
    uid      INT( 8 ) NOT NULL
);
CREATE TABLE `jz_account`
(
    acid     INTEGER PRIMARY KEY AUTOINCREMENT,
    money  INTEGER( 10 ) NOT NULL,
    time   DATETIME NOT NULL,
    remark VARCHAR(50),
    type    INT( 8 ) NOT NULL,
    category_id  INT( 8 ) NOT NULL,
    bank_id   INT( 8 ) DEFAULT ( 0 ),
    uid     INT( 8 ) NOT NULL
);