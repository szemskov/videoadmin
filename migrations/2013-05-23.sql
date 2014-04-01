create table if not exists marks(
  id              int not null auto_increment,
  translation_id  int not null,
  `name`          varchar(1000),
  `time`          int,

  primary key(id),
  foreign key(translation_id) references translations(id) on delete cascade
)engine=innodb;