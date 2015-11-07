BEGIN TRANSACTION;

CREATE TABLE "posts" (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    path VARCHAR(255) NOT NULL,
    created UNSIGNED INTEGER NOT NULL,
    updated UNSIGNED INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    draft INT(1) NOT NULL,
    public INT(1) NOT NULL,
    body TEXT NOT NULL,
    tags VARCHAR(255)
);

CREATE INDEX visible ON posts ( created, draft, public );
CREATE INDEX visible_tags ON posts ( tags, created, draft, public );
CREATE INDEX visible_author ON posts ( author, created, draft, public );

COMMIT;
