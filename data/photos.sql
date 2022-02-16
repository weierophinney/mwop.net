BEGIN TRANSACTION;

CREATE TABLE "photos" (
    filename VARCHAR(255) NOT NULL PRIMARY KEY,
    description TEXT NOT NULL,
    source text NOT NULL,
    source_url text NOT NULL,
    created VARCHAR(32) NOT NULL
);

CREATE VIRTUAL TABLE search USING FTS4(
    filename,
    description,
)

CREATE TRIGGER after_photos_insert
    AFTER INSERT ON photos
    BEGIN
        INSERT INTO search (
            filename,
            description
        )
        VALUES (
            new.filename,
            new.description
        );
    END

COMMIT;

