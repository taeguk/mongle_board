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
	brd_real_name VARCHAR(50) not null
) engine=InnoDB;

create table like_store_list (
	ls_id INT not null auto_increment primary key,
	ls_tbl_name VARCHAR(50) not null
) engine=InnoDB;

create table prm_list (
	prm_id INT not null auto_increment primary key,
	ul_id INT not null,
	brd_id INT not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade,
	foreign key(brd_id) references brd_list(brd_id) on delete cascade
) engine=InnoDB;

create table free_brd (
	art_id INT not null auto_increment primary key,
	ul_id INT not null,
	art_title VARCHAR(100) not null,
	art_content TEXT not null,
	art_hit_cnt INT not null,
	art_wr_ts TIMESTAMP not null,
) engine=InnoDB;

create table free_resp_list (
	feel_id INT not null auto_increment primary key,
	ul_id INT not null,
	art_id INT not null,
	resp_type ENUM('wow','soso','fuck') not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade,
	foreign key(art_id) references free_brd(art_id) on delete cascade
) engine=InnoDB;

create table free_cmt_list (
	cmt_id not null auto_increment primary key,
	ul_id INT not null,
	art_id INT not null,
	cmt_content TEXT not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade,
	foreign key(art_id) references free_brd(art_id) on delete cascade
) engine=InnoDB; 


create table cnu_study_brd (
	art_id INT not null auto_increment primary key,
	ul_id INT not null,
	art_title VARCHAR(100) not null,
	art_content TEXT not null,
	art_hit_cnt INT not null,
	art_wr_ts TIMESTAMP not null
) engine=InnoDB;

create table cnu_study_resp_list (
	feel_id INT not null auto_increment primary key,
	ul_id INT not null,
	art_id INT not null,
	resp_type ENUM('wow','soso','fuck') not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade,
	foreign key(art_id) references cnu_study_brd(art_id) on delete cascade
) engine=InnoDB;

create table cnu_study_cmt_list (
	cmt_id not null auto_increment primary key,
	ul_id INT not null,
	art_id INT not null,
	cmt_content TEXT not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade,
	foreign key(art_id) references cnu_study_brd(art_id) on delete cascade
) engine=InnoDB; 
