drop database IF EXISTS apilaravel;
create database IF NOT EXISTS apilaravel;
use apilaravel;

create table users(
id int(255) auto_increment not null,
email varchar(255) null,
role varchar(20) null,
name varchar(255) null,
password varchar(255) null,
created_at datetime,
update_at datetime,
remenber_token varchar(255),
primary key(id))ENGINE=InnoDb;

create table cars (
id int(255) auto_increment not null,
user_id_fk int(255) null,
title varchar(255) null,
description text null,
price varchar(30) null,
status varchar(30) null,
created_at datetime,
updated_at datetime,
primary key(id))ENGINE=InnoDb;

alter table cars add constraint user_id_fk foreign key(user_id_fk) references users(id) on update cascade on delete restrict