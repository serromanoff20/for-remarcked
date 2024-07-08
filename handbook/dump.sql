-- Database: testdb

-- DROP DATABASE IF EXISTS testdb;

--create DATABASE testdb ++
-- CREATE DATABASE testdb
--     WITH
--     OWNER = postgres
--     ENCODING = 'UTF8'
--     LC_COLLATE = 'Russian_Russia.1251'
--     LC_CTYPE = 'Russian_Russia.1251'
--     TABLESPACE = pg_default
--     CONNECTION LIMIT = -1
--     IS_TEMPLATE = False;
--create DATABASE testdb --

--create schema re ++
CREATE SCHEMA IF NOT EXISTS re
    AUTHORIZATION postgres;
--create schema re --

--create table re.buyers ++
CREATE TABLE IF NOT EXISTS re.buyers
(
    id SERIAL PRIMARY KEY,
    fio character varying(255) COLLATE pg_catalog."default",
    date_birth date,
    gender character(1) COLLATE pg_catalog."default",
    CONSTRAINT buyers_gender_check CHECK (gender = ANY (ARRAY['м'::bpchar, 'ж'::bpchar]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS re.buyers
    OWNER to postgres;
--create table re.buyers --

--create table re.goods ++
CREATE TABLE IF NOT EXISTS re.goods
(
    id SERIAL PRIMARY KEY,
    name character varying COLLATE pg_catalog."default",
    base_cost double precision,
    amount integer NOT NULL DEFAULT 0,
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS re.goods
    OWNER to postgres;
	
INSERT INTO re.goods(name, base_cost, amount)
	VALUES ('монитор', 15000, 5);
INSERT INTO re.goods(name, base_cost, amount)
	VALUES ('клавиатура', 400, 25);
INSERT INTO re.goods(name, base_cost, amount)
	VALUES ('электронная беспроводная мышь', 200, 50);
--create table re.goods --

--create table re.orders ++
CREATE TABLE IF NOT EXISTS re.orders
(
    id SERIAL PRIMARY KEY,
    buyer_id integer NOT NULL,
    goods_id integer NOT NULL,
    delivery_date date NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    cost_order double precision NOT NULL,
    CONSTRAINT fk_buyers FOREIGN KEY (buyer_id)
        REFERENCES re.buyers (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT fk_goods FOREIGN KEY (goods_id)
        REFERENCES re.goods (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS re.orders
    OWNER to postgres;
--create table re.orders --
	