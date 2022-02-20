BEGIN TRANSACTION;

CREATE TABLE "photos" (
    filename VARCHAR(255) NOT NULL PRIMARY KEY,
    description TEXT NOT NULL,
    source text,
    source_url text NOT NULL,
    created VARCHAR(32) NOT NULL
);

CREATE INDEX visible ON photos ( created );

CREATE VIRTUAL TABLE search USING FTS4(
    filename,
    description,
    created
);

CREATE TRIGGER after_photos_insert
    AFTER INSERT ON photos
    BEGIN
        INSERT INTO search (
            filename,
            description,
            created
        )
        VALUES (
            new.filename,
            new.description,
            new.created
        );
    END;

COMMIT;
