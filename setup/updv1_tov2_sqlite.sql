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
INSERT INTO nlogins SELECT *,0,0,0 FROM logins;
ALTER TABLE logins RENAME TO ologins;
ALTER TABLE nlogins RENAME TO logins;
DROP INDEX "catid";
DROP TABLE "ologins";
CREATE INDEX "catid" ON "logins" ("catid");