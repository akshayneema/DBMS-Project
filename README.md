# DBMS-Project

update the username and password of postgres in database.ini and config.php

Create new table users:
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT now()
);
ALTER TABLE payrental ALTER COLUMN price type double precision using cast(right(price,-1) as double precision);
update payrental set price = random() * 100 + 1 where price is null or price = 0;
ALTER TABLE payrental ALTER COLUMN host_id type double precision using cast(host_id as double precision);
delete from payrental where host_id in (select host_id from payrental group by host_id having count(distinct(host_name)) = 2) or host_name is null;
create table hosts as select host_id, host_name from payrental group by host_id, host_name;
ALTER TABLE hosts ADD COLUMN "host_username" varchar;
UPDATE hosts SET host_username = replace(host_name, ' ', '');
ALTER TABLE hosts ADD COLUMN password text NOT NULL default substr(md5(random()::text),0,7);
CREATE TABLE bookings (booking_id serial primary key, property_id integer, host_id integer, user_id integer, check_in_date timestamp, check_out_date timestamp, number_adults integer, number_children integer);
create sequence seq start with 314893953;
ALTER TABLE hosts ALTER COLUMN host_id SET DEFAULT nextval('seq');