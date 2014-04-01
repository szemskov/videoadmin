-- таблица линий воспроизведения --
create table if not exists play_lines(
  id          int not null auto_increment,
  name        varchar(100) not null,
  stream      varchar(100),
  created     timestamp,

  primary key(id)
)engine=innodb;

-- таблица видеотрансляций --
create table if not exists translations(
  id            int not null auto_increment,
  name          varchar(1000) not null,
  date_start    int not null,
  play_point    varchar(1500) not null,
  line_id       int,
  media_state   int not null default 1,
  start_live_time int,
  stop_live_time  int,
  cdn             int not null default 0,
  ova_url         varchar(2000), -- url для рекламного плагина ova pre-roll --
  ova_url_post 	  varchar(2000), -- url для рекламного плагина ova post-roll --
  format_3x4      int not null default 0,
  hd_disabled     int not null default 0,
  node_id         int,
  user_name       varchar(100),
  dvr             int not null default 0,
  keywords        text,
  substitutions   text,
  setevizor       int not null default 0,
  online          int not null default 0,
  max_views       int not null default 0,
  check_geoip     int not null default 1,

  primary key(id),
  foreign key(line_id) references play_lines(id) on delete set null on update restrict
)engine=innodb;

-- таблица соответствия id трансляции и nid в drupal на russiasport.ru --
create table if not exists translations2nodes(
  tid         int,
  nid         int not null,
  updated     timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,

  primary key(tid, nid)
)engine=innodb;

create table if not exists translation_views(
  tid             int,
  views_live      long,
  views_archive   long,

  primary key(tid)
)engine=innodb;

create table if not exists users(
  `name`        varchar(100) not null,
  `role`        varchar(50) not null,

  primary key(`name`, `role`)
)engine=innodb;

create table if not exists translation_logsheets(
  id              int not null auto_increment,
  translation_id  int not null,
  log_id          varchar(100),

  primary key(id),
  foreign key(translation_id) references translations(id) on delete cascade
)engine=innodb DEFAULT CHARSET=utf8;

create table if not exists logsheet(
  id              int not null auto_increment,
  log_id          varchar(100),
  `name`          varchar(1000),
  `time`          int,

  primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists logsheet_labels(
  id              int not null auto_increment,
  log_id          varchar(100),
  `name`          varchar(1000),
  `time`          int,

  primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table logsheet_filters(
  id            int not null auto_increment,
  `name`        varchar(255),
  keywords      text,

  primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table ip_blacklist(
  id            int not null auto_increment,
  `name`        varchar(255),
  iplist        text,

  primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create index i_log_id on logsheet(log_id);
create index i_log_id on logsheet_labels(log_id);


