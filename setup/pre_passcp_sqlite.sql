
CREATE TABLE IF NOT EXISTS "nuser" (
        "id" INTEGER NOT NULL  ,
        "name" VARCHAR(30) NOT NULL DEFAULT '' ,
        "teststring" TEXT NULL  ,
        "iv" VARCHAR(24) NULL  ,
        PRIMARY KEY ("id")
);
CREATE TABLE IF NOT EXISTS "nlogins" (
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
