# Note: Using MySQL syntax

CREATE TABLE Users (
    ID       INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(64),
    password VARCHAR(64),
    PRIMARY KEY(ID)
);
