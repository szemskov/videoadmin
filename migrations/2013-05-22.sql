alter table translations add user_name varchar(1000);

create table if not exists users(
  `name`        varchar(100) not null,
  `role`        varchar(50) not null,

  primary key(`name`, `role`)
)engine=innodb;