DROP TABLE IF EXISTS "cat";
CREATE TABLE IF NOT EXISTS "cat" (
        "id" INTEGER NOT NULL  ,
        "userid" INTEGER NOT NULL DEFAULT '0' ,
        "title" VARCHAR(32) NOT NULL DEFAULT '' ,
        PRIMARY KEY ("id")
);
CREATE INDEX "userid" ON "cat" ("userid");

DROP TABLE IF EXISTS "loginlog";
CREATE TABLE IF NOT EXISTS "loginlog" (
        "name" VARCHAR(30) NULL  ,
        "ip" VARCHAR(16) NULL  ,
        "date" DATETIME NULL  ,
        "outcome" TINYINT NULL
);
CREATE INDEX "name" ON "loginlog" ("name");

DROP TABLE IF EXISTS "logins";
CREATE TABLE IF NOT EXISTS "logins" (
        "id" INTEGER NOT NULL  ,
        "iv" VARCHAR(24) NOT NULL DEFAULT '' ,
        "userid" INTEGER NOT NULL DEFAULT '0' ,
        "catid" INTEGER NOT NULL DEFAULT '0' ,
        "login" TEXT NULL  ,
        "password" TEXT NULL  ,
        "site" TEXT NULL  ,
        "url" TEXT NULL  ,
        PRIMARY KEY ("id")
);
CREATE INDEX "catid" ON "logins" ("catid");

DROP TABLE IF EXISTS "user";
CREATE TABLE IF NOT EXISTS "user" (
        "id" INTEGER NOT NULL  ,
        "name" VARCHAR(30) NOT NULL DEFAULT '' ,
        "teststring" TEXT NULL  ,
        "iv" VARCHAR(24) NULL  ,
        PRIMARY KEY ("id")
);
