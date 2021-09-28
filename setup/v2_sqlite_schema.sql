CREATE TABLE IF NOT EXISTS "cat" (
        "id" INTEGER NOT NULL  ,
        "userid" INTEGER NOT NULL DEFAULT '0' ,
        "title" VARCHAR(32) NOT NULL DEFAULT '' ,
        PRIMARY KEY ("id")
);
CREATE TABLE IF NOT EXISTS "loginlog" (
        "name" VARCHAR(30) NULL  ,
        "ip" VARCHAR(16) NULL  ,
        "date" DATETIME NULL  ,
        "outcome" TINYINT NULL
);
CREATE TABLE IF NOT EXISTS "user" (
        "id" INTEGER NOT NULL  ,
        "name" VARCHAR(30) NOT NULL DEFAULT '' ,
        "teststring" TEXT NULL  ,
        "iv" VARCHAR(24) NULL  ,
        PRIMARY KEY ("id")
);
CREATE TABLE IF NOT EXISTS "logins" (
        "id" INTEGER NOT NULL  ,
        "iv" VARCHAR(16) NOT NULL DEFAULT '' ,
        "userid" INTEGER NOT NULL DEFAULT '0' ,
        "catid" INTEGER NOT NULL DEFAULT '0' ,
        "login" TEXT NULL  ,
        "password" TEXT NULL  ,
        "site" TEXT NULL  ,
        "url" TEXT NULL  ,
        "noteid" integer default 0,
        "created" integer default 0,
        "modified" integer default 0,
        PRIMARY KEY ("id")
);
CREATE INDEX "userid" ON "cat" ("userid");
CREATE INDEX "name" ON "loginlog" ("name");
CREATE INDEX "catid" ON "logins" ("catid");
