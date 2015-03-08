#데이터베이스와 사용자 생성및 권한 설정.
create database sgcsbrd DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
create user 'sgcsbrduser'@'localhost' identified by 'zjarhdtlftp12';
grant all privileges on sgcsbrd.* to sgcsbrduser@localhost;
flush privileges;
use sgcsbrd;

#테이블 생성
create table user_list (
	ul_id INT not null auto_increment primary key,
	user_id VARCHAR(30) not null,
	user_pw VARCHAR(40) not null,
	stdnt_id VARCHAR(20) not null,
	user_name VARCHAR(40) not null,
	user_nickname VARCHAR(40) not null,
	visit_cnt INT not null,
	login_attempt_ts TIMESTAMP not null,
	signin_ts TIMESTAMP not null,
	signup_ts TIMESTAMP not null
) engine=InnoDB;

create table admin_list (
	al_id INT not null auto_increment primary key,
	ul_id INT not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade
) engine=InnoDB;

create table brd_list (
	brd_id INT not null auto_increment primary key,
	brd_tbl_name VARCHAR(50) not null,
	brd_real_name VARCHAR(50) not null,
	brd_article_cnt INT VARCHAR(50) not null,
) engine=InnoDB;

create table prm_list (
	prm_id INT not null auto_increment primary key,
	ul_id INT not null,
	brd_id INT not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade,
	foreign key(brd_id) references brd_list(brd_id) on delete cascade
) engine=InnoDB;

# 밑에부터 수정할꺼 많음

create table like_list (
	like_id INT not null auto_increment primary key,
	ul_id INT not null,
	brd_id INT not null,
	art_id INT not null
) engine=InnoDB;

create table free_brd (
	art_id INT not null auto_increment primary key,
	ul_id INT not null,
	art_title VARCHAR(100) not null,
	art_content TEXT not null,
	art_like_cnt INT not null,
	art_hate_cnt INT not null,
	art_hit_cnt INT not null,
	art_wr_ts TIMESTAMP not null,
) engine=InnoDB;