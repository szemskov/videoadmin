alter table translations add max_views int not null default 0;

create unique index i_log_time on logsheet(log_id, `time`);