drop schema if exists iliad;

create schema iliad;

create table iliad.orders (
    id int auto_increment primary key,
    customer_name varchar(255) not null,
    order_date datetime default current_timestamp,
    value int not null
);

create table iliad.products (
    id int auto_increment primary key,
    name varchar(255) not null,
    description text,
    price decimal(10, 2) not null
);

create table iliad.order_items (
    id int auto_increment primary key,
    order_id int not null,
    product_id int not null,
    quantity int not null,
    foreign key (order_id) references iliad.orders(id),
    foreign key (product_id) references iliad.products(id)
);